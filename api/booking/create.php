<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');

require_once '../../includes/config.php';
require_once '../../includes/booking_helpers.php';

if (!isLoggedIn()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Sesi berakhir, silakan login kembali.',
    ]);
    exit;
}

$payload = json_decode(file_get_contents('php://input'), true);
$doctorId = (int) ($payload['doctor_id'] ?? 0);
$date = trim($payload['date'] ?? '');
$time = trim($payload['time'] ?? '');

if ($doctorId <= 0 || $date === '' || $time === '') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Dokter, tanggal, dan jam konsultasi wajib dipilih.',
    ]);
    exit;
}

$selectedAt = DateTimeImmutable::createFromFormat('Y-m-d H:i', $date . ' ' . $time);
if (!$selectedAt) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Format tanggal atau jam tidak valid.',
    ]);
    exit;
}

$doctor = findBookingDoctor($pdo, $doctorId);
if (!$doctor) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Dokter tidak ditemukan.',
    ]);
    exit;
}

$availableSlots = array_column(getAvailableBookingSlots($pdo, $doctorId, $date), 'value');
if (!in_array($time, $availableSlots, true)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Slot konsultasi sudah tidak tersedia. Silakan pilih slot lain.',
    ]);
    exit;
}

$user = currentUser();
$userId = (int) ($user['id'] ?? 0);
if ($userId <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Token tidak valid, silakan login kembali.',
    ]);
    exit;
}

try {
    ensureConsultationVideoColumns($pdo);
    $pdo->beginTransaction();

    $patientId = ensurePatientProfile($pdo, $userId);
    syncConsultationStatuses($pdo);

    $conflictStmt = $pdo->prepare(
        "SELECT idKonsultasi
         FROM SesiKonsultasi
         WHERE idDokter = :idDokter
           AND tanggal = :tanggal
           AND status IN ('Menunggu', 'Berjalan')
         LIMIT 1"
    );
    $conflictStmt->execute([
        ':idDokter' => $doctorId,
        ':tanggal' => $selectedAt->format('Y-m-d H:i:s'),
    ]);

    if ($conflictStmt->fetchColumn()) {
        throw new RuntimeException('Slot konsultasi barusan sudah diambil user lain. Silakan pilih slot lain.');
    }

    $patientConflictStmt = $pdo->prepare(
        "SELECT idKonsultasi
         FROM SesiKonsultasi
         WHERE idPasien = :idPasien
           AND tanggal = :tanggal
           AND status IN ('Menunggu', 'Berjalan')
         LIMIT 1"
    );
    $patientConflictStmt->execute([
        ':idPasien' => $patientId,
        ':tanggal' => $selectedAt->format('Y-m-d H:i:s'),
    ]);

    if ($patientConflictStmt->fetchColumn()) {
        throw new RuntimeException('Anda sudah memiliki booking lain pada jam yang sama. Silakan pilih slot yang berbeda.');
    }

    $insertConsultation = $pdo->prepare(
        "INSERT INTO SesiKonsultasi (idPasien, idDokter, tanggal, status)
         VALUES (:idPasien, :idDokter, :tanggal, 'Menunggu')
         RETURNING idKonsultasi"
    );
    $insertConsultation->execute([
        ':idPasien' => $patientId,
        ':idDokter' => $doctorId,
        ':tanggal' => $selectedAt->format('Y-m-d H:i:s'),
    ]);
    $consultationId = (int) $insertConsultation->fetchColumn();
    $videoCall = assignConsultationVideoCall(
        $pdo,
        $consultationId,
        $doctorId,
        $patientId,
        $selectedAt->format('Y-m-d H:i:s')
    );

    $insertNotification = $pdo->prepare(
        'INSERT INTO Notifikasi (id_user, pesan) VALUES (:id_user, :pesan)'
    );
    $insertNotification->execute([
        ':id_user' => $userId,
        ':pesan' => sprintf(
            'Booking konsultasi dengan %s berhasil untuk %s pukul %s.',
            $doctor['name'],
            $selectedAt->format('d M Y'),
            $selectedAt->format('H:i')
        ),
    ]);

    if (!empty($doctor['user_id'])) {
        $insertNotification->execute([
            ':id_user' => $doctor['user_id'],
            ':pesan' => sprintf(
                'Ada booking baru dari pasien untuk %s pukul %s.',
                $selectedAt->format('d M Y'),
                $selectedAt->format('H:i')
            ),
        ]);
    }

    ensureConsultationIntroMessage($pdo, [
        'id' => $consultationId,
        'consultation_id' => $consultationId,
        'doctor_user_id' => (int) ($doctor['user_id'] ?? 0),
        'doctor_id' => $doctorId,
        'patient_id' => $patientId,
        'scheduled_at' => $selectedAt->format('Y-m-d H:i:s'),
        'doctor_name' => $doctor['name'] ?? 'dokter CareSync',
        'specialization' => $doctor['specialization'] ?? 'Dokter Umum',
        'jitsi_room_name' => $videoCall['room_name'] ?? '',
        'jitsi_room_url' => $videoCall['room_url'] ?? '',
        'video_call_provider' => $videoCall['provider'] ?? 'Jitsi Meet',
    ]);

    $pdo->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'Booking konsultasi berhasil dibuat.',
        'data' => [
            'consultation_id' => $consultationId,
            'doctor' => $doctor,
            'date' => $selectedAt->format('Y-m-d'),
            'time' => $selectedAt->format('H:i'),
            'status' => 'Menunggu',
            'video_call_room' => $videoCall['room_name'] ?? '',
            'video_call_url' => $videoCall['room_url'] ?? '',
            'video_call_provider' => $videoCall['provider'] ?? 'Jitsi Meet',
        ],
    ]);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo json_encode([
        'status' => 'error',
        'message' => 'Gagal membuat booking konsultasi: ' . $e->getMessage(),
    ]);
}
