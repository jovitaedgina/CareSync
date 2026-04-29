<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/booking_helpers.php';
require_once __DIR__ . '/marketplace_helpers.php';

function ensureStaffPortalSchema(PDO $pdo): void
{
    static $ensured = false;

    if ($ensured) {
        return;
    }

    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS staff_status VARCHAR(20) DEFAULT 'Aktif'");
    $pdo->exec("ALTER TABLE Resep ADD COLUMN IF NOT EXISTS idPemesanan INT UNIQUE REFERENCES Pemesanan(idPemesanan) ON DELETE SET NULL");

    $ensured = true;
}

ensureStaffPortalSchema($pdo);

function formatRupiah(float|int $amount): string
{
    return 'Rp ' . number_format((float) $amount, 0, ',', '.');
}

function formatCompactNumber(float|int $number): string
{
    $number = (float) $number;

    if ($number >= 1000000) {
        return number_format($number / 1000000, 1, ',', '.') . ' jt';
    }

    if ($number >= 1000) {
        return number_format($number / 1000, 1, ',', '.') . ' rb';
    }

    return number_format($number, 0, ',', '.');
}

function formatPercentDelta(float $current, float $previous): array
{
    if ($previous <= 0.0) {
        if ($current <= 0.0) {
            return ['label' => '0%', 'direction' => 'flat'];
        }

        return ['label' => '100%', 'direction' => 'up'];
    }

    $delta = (($current - $previous) / $previous) * 100;
    $direction = $delta > 0 ? 'up' : ($delta < 0 ? 'down' : 'flat');

    return [
        'label' => number_format(abs($delta), 1, ',', '.') . '%',
        'direction' => $direction,
    ];
}

function getStaffProfile(PDO $pdo, int $userId): array
{
    $stmt = $pdo->prepare(
        "SELECT u.id, u.nama, u.email, u.role, u.profile_photo, d.spesialisasi, d.nomorSTR
         FROM users u
         LEFT JOIN Dokter d ON d.id_user = u.id
         WHERE u.id = :id
         LIMIT 1"
    );
    $stmt->execute([':id' => $userId]);

    $profile = $stmt->fetch() ?: [];
    $name = (string) ($profile['nama'] ?? '');
    $role = (string) ($profile['role'] ?? '');
    $specialization = (string) ($profile['spesialisasi'] ?? '');

    return [
        'id' => (int) ($profile['id'] ?? $userId),
        'name' => $name !== '' ? $name : (currentUser()['name'] ?? 'Pengguna'),
        'email' => (string) ($profile['email'] ?? (currentUser()['email'] ?? '-')),
        'role' => $role !== '' ? $role : (currentUser()['role'] ?? 'user'),
        'specialization' => $specialization,
        'license' => (string) ($profile['nomorstr'] ?? ''),
        'photo_url' => getUserProfilePhotoUrl((string) ($profile['profile_photo'] ?? '')),
        'initials' => getInitials($name !== '' ? $name : 'CS'),
    ];
}

function getInitials(string $name): string
{
    $parts = preg_split('/\s+/', trim($name)) ?: [];
    $initials = '';

    foreach ($parts as $part) {
        $clean = preg_replace('/[^A-Za-z]/', '', $part) ?? '';
        if ($clean === '') {
            continue;
        }

        $initials .= strtoupper(substr($clean, 0, 1));
        if (strlen($initials) >= 2) {
            break;
        }
    }

    return $initials !== '' ? $initials : 'CS';
}

function fetchSingleValue(PDO $pdo, string $sql, array $params = []): float
{
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $value = $stmt->fetchColumn();

    return $value !== false ? (float) $value : 0.0;
}

function getAdminDashboardData(PDO $pdo): array
{
    $currentRevenue = fetchSingleValue(
        $pdo,
        "SELECT COALESCE(SUM(totalHarga), 0)
         FROM Pemesanan
         WHERE DATE_TRUNC('month', tanggal) = DATE_TRUNC('month', CURRENT_DATE)"
    );
    $previousRevenue = fetchSingleValue(
        $pdo,
        "SELECT COALESCE(SUM(totalHarga), 0)
         FROM Pemesanan
         WHERE DATE_TRUNC('month', tanggal) = DATE_TRUNC('month', CURRENT_DATE - INTERVAL '1 month')"
    );
    $currentPatients = fetchSingleValue(
        $pdo,
        "SELECT COUNT(*)
         FROM users
         WHERE LOWER(COALESCE(role, 'user')) IN ('user', 'pasien')
           AND DATE_TRUNC('month', created_at) = DATE_TRUNC('month', CURRENT_DATE)"
    );
    $previousPatients = fetchSingleValue(
        $pdo,
        "SELECT COUNT(*)
         FROM users
         WHERE LOWER(COALESCE(role, 'user')) IN ('user', 'pasien')
           AND DATE_TRUNC('month', created_at) = DATE_TRUNC('month', CURRENT_DATE - INTERVAL '1 month')"
    );
    $todayVisits = fetchSingleValue(
        $pdo,
        "SELECT COUNT(*)
         FROM SesiKonsultasi
         WHERE DATE(tanggal) = CURRENT_DATE"
    );
    $monthlyPrescriptionItems = fetchSingleValue(
        $pdo,
        "SELECT COALESCE(SUM(ip.jumlah), 0)
         FROM ItemPemesanan ip
         INNER JOIN Pemesanan p ON p.idPemesanan = ip.idPemesanan
         WHERE DATE_TRUNC('month', p.tanggal) = DATE_TRUNC('month', CURRENT_DATE)"
    );

    $revenueDelta = formatPercentDelta($currentRevenue, $previousRevenue);
    $patientDelta = formatPercentDelta($currentPatients, $previousPatients);

    $trendStmt = $pdo->query(
        "WITH days AS (
            SELECT GENERATE_SERIES(CURRENT_DATE - INTERVAL '6 day', CURRENT_DATE, INTERVAL '1 day')::date AS day
        )
        SELECT TO_CHAR(days.day, 'DD Mon') AS label,
               COALESCE(SUM(p.totalHarga), 0) AS total
        FROM days
        LEFT JOIN Pemesanan p ON DATE(p.tanggal) = days.day
        GROUP BY days.day
        ORDER BY days.day"
    );
    $revenueTrend = $trendStmt->fetchAll();

    $distributionStmt = $pdo->query(
        "SELECT COALESCE(d.spesialisasi, 'Dokter Umum') AS specialization, COUNT(*) AS total
         FROM SesiKonsultasi sk
         INNER JOIN Dokter d ON d.idDokter = sk.idDokter
         GROUP BY COALESCE(d.spesialisasi, 'Dokter Umum')
         ORDER BY total DESC, specialization ASC
         LIMIT 5"
    );
    $distribution = $distributionStmt->fetchAll();

    $topDoctorsStmt = $pdo->query(
        "SELECT u.nama, COALESCE(d.spesialisasi, 'Dokter Umum') AS specialization, COUNT(*) AS total_patients
         FROM SesiKonsultasi sk
         INNER JOIN Dokter d ON d.idDokter = sk.idDokter
         INNER JOIN users u ON u.id = d.id_user
         WHERE DATE_TRUNC('month', sk.tanggal) = DATE_TRUNC('month', CURRENT_DATE)
         GROUP BY u.nama, COALESCE(d.spesialisasi, 'Dokter Umum')
         ORDER BY total_patients DESC, u.nama ASC
         LIMIT 3"
    );

    $topSellingStmt = $pdo->query(
        "SELECT o.nama, COALESCE(SUM(ip.jumlah), 0) AS total_sold
         FROM ItemPemesanan ip
         INNER JOIN Pemesanan p ON p.idPemesanan = ip.idPemesanan
         INNER JOIN Obat o ON o.idObat = ip.idObat
         WHERE DATE_TRUNC('month', p.tanggal) = DATE_TRUNC('month', CURRENT_DATE)
         GROUP BY o.nama
         ORDER BY total_sold DESC, o.nama ASC
         LIMIT 3"
    );

    return [
        'stats' => [
            'revenue' => [
                'value' => formatRupiah($currentRevenue),
                'delta' => $revenueDelta,
            ],
            'patients' => [
                'value' => number_format($currentPatients, 0, ',', '.'),
                'delta' => $patientDelta,
            ],
            'visits_today' => [
                'value' => number_format($todayVisits, 0, ',', '.'),
            ],
            'prescription_items' => [
                'value' => number_format($monthlyPrescriptionItems, 0, ',', '.'),
            ],
        ],
        'revenue_chart' => [
            'labels' => array_column($revenueTrend, 'label'),
            'values' => array_map('floatval', array_column($revenueTrend, 'total')),
        ],
        'distribution' => [
            'labels' => array_column($distribution, 'specialization'),
            'values' => array_map('intval', array_column($distribution, 'total')),
            'total' => array_sum(array_map('intval', array_column($distribution, 'total'))),
        ],
        'top_doctors' => $topDoctorsStmt->fetchAll(),
        'top_items' => $topSellingStmt->fetchAll(),
    ];
}

function getPharmacistDashboardData(PDO $pdo): array
{
    $queueStmt = $pdo->query(
        "SELECT
            r.idResep,
            r.tanggalPembuatan,
            pu.nama AS patient_name,
            pu.dob AS patient_dob,
            du.nama AS doctor_name,
            COALESCE(d.spesialisasi, 'Dokter Umum') AS specialization,
            order_match.idPemesanan,
            COALESCE(order_match.statusPesanan, '') AS order_status,
            COALESCE(sh.statusPengiriman, '') AS shipping_status
         FROM Resep r
         INNER JOIN SesiKonsultasi sk ON sk.idKonsultasi = r.idKonsultasi
         INNER JOIN Pasien p ON p.idPasien = sk.idPasien
         INNER JOIN users pu ON pu.id = p.id_user
         INNER JOIN Dokter d ON d.idDokter = sk.idDokter
         INNER JOIN users du ON du.id = d.id_user
         LEFT JOIN LATERAL (
            SELECT pm.idPemesanan, pm.statusPesanan
            FROM Pemesanan pm
            WHERE pm.idPasien = p.idPasien
              AND DATE(pm.tanggal) = DATE(r.tanggalPembuatan)
            ORDER BY pm.tanggal DESC
            LIMIT 1
         ) AS order_match ON TRUE
         LEFT JOIN Pengiriman sh ON sh.idPemesanan = order_match.idPemesanan
         ORDER BY r.tanggalPembuatan DESC
         LIMIT 10"
    );

    $rows = [];
    $newCount = 0;
    $processCount = 0;
    $readyCount = 0;

    foreach ($queueStmt->fetchAll() as $row) {
        $status = 'Menunggu Validasi';
        $badge = 'bg-red-50 text-red-600 border-red-200';
        $filter = 'baru';

        if (!empty($row['idpemesanan'])) {
            $status = 'Sedang Disiapkan';
            $badge = 'bg-yellow-50 text-yellow-700 border-yellow-200';
            $filter = 'proses';

            $shippingStatus = strtolower((string) $row['shipping_status']);
            $orderStatus = strtolower((string) $row['order_status']);

            if (in_array($shippingStatus, ['dikirim', 'terkirim', 'selesai'], true) || in_array($orderStatus, ['selesai', 'siap diambil'], true)) {
                $status = 'Siap Diambil';
                $badge = 'bg-green-50 text-green-700 border-green-200';
                $filter = 'siap';
            }
        }

        if ($filter === 'baru') {
            $newCount++;
        } elseif ($filter === 'proses') {
            $processCount++;
        } else {
            $readyCount++;
        }

        $age = '-';
        if (!empty($row['patient_dob'])) {
            $age = (string) date_diff(new DateTime($row['patient_dob']), new DateTime())->y . ' Thn';
        }

        $rows[] = [
            'id' => (int) $row['idresep'],
            'time_label' => date('H:i', strtotime((string) $row['tanggalpembuatan'])) . ' WIB',
            'code' => 'RX-' . str_pad((string) $row['idresep'], 6, '0', STR_PAD_LEFT),
            'patient_name' => $row['patient_name'],
            'patient_age' => $age,
            'doctor_name' => $row['doctor_name'],
            'specialization' => $row['specialization'],
            'status' => $status,
            'status_badge' => $badge,
            'filter' => $filter,
        ];
    }

    return [
        'stats' => [
            'new' => $newCount,
            'processing' => $processCount,
            'ready' => $readyCount,
        ],
        'queue' => $rows,
    ];
}

function getDoctorDashboardData(PDO $pdo, int $userId): array
{
    $doctorStmt = $pdo->prepare(
        "SELECT d.idDokter, u.nama, COALESCE(d.spesialisasi, 'Dokter Umum') AS specialization
         FROM Dokter d
         INNER JOIN users u ON u.id = d.id_user
         WHERE d.id_user = :user_id
         LIMIT 1"
    );
    $doctorStmt->execute([':user_id' => $userId]);
    $doctor = $doctorStmt->fetch();

    if (!$doctor) {
        return [
            'doctor' => [
                'name' => currentUser()['name'] ?? 'Dokter',
                'specialization' => '',
            ],
            'stats' => [
                'queue' => 0,
                'completed' => 0,
                'pending_prescriptions' => 0,
            ],
            'queue_list' => [],
            'upcoming' => [],
        ];
    }

    $doctorId = (int) $doctor['iddokter'];

    $queueCount = fetchSingleValue(
        $pdo,
        "SELECT COUNT(*)
         FROM SesiKonsultasi
         WHERE idDokter = :doctor_id
           AND DATE(tanggal) = CURRENT_DATE
           AND status IN ('Menunggu', 'Berjalan')",
        [':doctor_id' => $doctorId]
    );
    $completedCount = fetchSingleValue(
        $pdo,
        "SELECT COUNT(*)
         FROM SesiKonsultasi
         WHERE idDokter = :doctor_id
           AND DATE(tanggal) = CURRENT_DATE
           AND status = 'Selesai'",
        [':doctor_id' => $doctorId]
    );
    $pendingPrescriptionCount = fetchSingleValue(
        $pdo,
        "SELECT COUNT(*)
         FROM SesiKonsultasi sk
         LEFT JOIN Resep r ON r.idKonsultasi = sk.idKonsultasi
         WHERE sk.idDokter = :doctor_id
           AND sk.status = 'Selesai'
           AND r.idResep IS NULL",
        [':doctor_id' => $doctorId]
    );

    $queueStmt = $pdo->prepare(
        "SELECT
            sk.idKonsultasi,
            sk.tanggal,
            sk.status::text AS status,
            pu.nama AS patient_name,
            COALESCE(diag.deskripsi, 'Belum ada diagnosis dicatat') AS note
         FROM SesiKonsultasi sk
         INNER JOIN Pasien p ON p.idPasien = sk.idPasien
         INNER JOIN users pu ON pu.id = p.id_user
         LEFT JOIN Diagnosis diag ON diag.idKonsultasi = sk.idKonsultasi
         WHERE sk.idDokter = :doctor_id
           AND DATE(sk.tanggal) = CURRENT_DATE
         ORDER BY sk.tanggal ASC
         LIMIT 8"
    );
    $queueStmt->execute([':doctor_id' => $doctorId]);

    $upcomingStmt = $pdo->prepare(
        "SELECT
            sk.idKonsultasi,
            sk.tanggal,
            pu.nama AS patient_name,
            CASE WHEN COALESCE(sk.jitsi_room_url, '') <> '' THEN 'Video Call' ELSE 'Chat Konsultasi' END AS service_type
         FROM SesiKonsultasi sk
         INNER JOIN Pasien p ON p.idPasien = sk.idPasien
         INNER JOIN users pu ON pu.id = p.id_user
         WHERE sk.idDokter = :doctor_id
           AND sk.tanggal >= NOW()
         ORDER BY sk.tanggal ASC
         LIMIT 5"
    );
    $upcomingStmt->execute([':doctor_id' => $doctorId]);

    return [
        'doctor' => [
            'name' => $doctor['nama'],
            'specialization' => $doctor['specialization'],
        ],
        'stats' => [
            'queue' => (int) $queueCount,
            'completed' => (int) $completedCount,
            'pending_prescriptions' => (int) $pendingPrescriptionCount,
        ],
        'queue_list' => $queueStmt->fetchAll(),
        'upcoming' => $upcomingStmt->fetchAll(),
    ];
}

function findDoctorByUserId(PDO $pdo, int $userId): ?array
{
    $stmt = $pdo->prepare(
        "SELECT d.idDokter, u.nama, u.email, COALESCE(d.spesialisasi, 'Dokter Umum') AS specialization, COALESCE(d.nomorSTR, '-') AS license
         FROM Dokter d
         INNER JOIN users u ON u.id = d.id_user
         WHERE d.id_user = :user_id
         LIMIT 1"
    );
    $stmt->execute([':user_id' => $userId]);

    $row = $stmt->fetch();
    return $row ?: null;
}

function getDoctorConsultationRoomData(PDO $pdo, int $userId, int $requestedConsultationId = 0): array
{
    $consultations = getUserConsultations($pdo, $userId);
    $selected = null;

    foreach ($consultations as $consultation) {
        if ($consultation['id'] === $requestedConsultationId) {
            $selected = $consultation;
            break;
        }
    }

    if (!$selected && $consultations) {
        $selected = $consultations[0];
    }

    $messages = [];
    if ($selected) {
        ensureConsultationIntroMessage($pdo, $selected);
        $selected = findAccessibleConsultation($pdo, (int) $selected['id'], $userId) ?? $selected;
        $messages = getConsultationMessages($pdo, (int) $selected['id']);
    }

    return [
        'consultations' => $consultations,
        'selected' => $selected,
        'messages' => $messages,
    ];
}

function getConsultationClinicalData(PDO $pdo, int $consultationId, int $doctorUserId): ?array
{
    $doctor = findDoctorByUserId($pdo, $doctorUserId);
    if (!$doctor) {
        return null;
    }

    $stmt = $pdo->prepare(
        "SELECT
            sk.idKonsultasi,
            sk.tanggal,
            sk.status::text AS status,
            p.idPasien,
            pu.id AS patient_user_id,
            pu.nama AS patient_name,
            pu.gender,
            pu.dob,
            p.weight,
            p.height,
            p.allergy_notes,
            d.idDokter,
            du.nama AS doctor_name,
            COALESCE(d.spesialisasi, 'Dokter Umum') AS specialization,
            COALESCE(d.nomorSTR, '-') AS license
         FROM SesiKonsultasi sk
         INNER JOIN Pasien p ON p.idPasien = sk.idPasien
         INNER JOIN users pu ON pu.id = p.id_user
         INNER JOIN Dokter d ON d.idDokter = sk.idDokter
         INNER JOIN users du ON du.id = d.id_user
         WHERE sk.idKonsultasi = :consultation_id
           AND d.id_user = :doctor_user_id
         LIMIT 1"
    );
    $stmt->execute([
        ':consultation_id' => $consultationId,
        ':doctor_user_id' => $doctorUserId,
    ]);
    $row = $stmt->fetch();

    if (!$row) {
        return null;
    }

    $diagnosisStmt = $pdo->prepare(
        'SELECT idDiagnosis, deskripsi, catatanDokter
         FROM Diagnosis
         WHERE idKonsultasi = :consultation_id
         ORDER BY idDiagnosis DESC
         LIMIT 1'
    );
    $diagnosisStmt->execute([':consultation_id' => $consultationId]);
    $diagnosis = $diagnosisStmt->fetch() ?: [];

    $prescriptionStmt = $pdo->prepare(
        'SELECT idResep, catatan, tanggalPembuatan
         FROM Resep
         WHERE idKonsultasi = :consultation_id
         ORDER BY idResep DESC
         LIMIT 1'
    );
    $prescriptionStmt->execute([':consultation_id' => $consultationId]);
    $prescription = $prescriptionStmt->fetch() ?: [];

    $prescriptionItems = [];
    if (!empty($prescription['idresep'])) {
        $itemStmt = $pdo->prepare(
            "SELECT ir.idItemResep, ir.idObat, ir.jumlah, ir.aturanPakai, o.nama, o.dosis, o.harga, o.stok
             FROM ItemResep ir
             INNER JOIN Obat o ON o.idObat = ir.idObat
             WHERE ir.idResep = :idResep
             ORDER BY ir.idItemResep ASC"
        );
        $itemStmt->execute([':idResep' => $prescription['idresep']]);
        $prescriptionItems = $itemStmt->fetchAll();
    }

    $complaintStmt = $pdo->prepare(
        "SELECT pc.isiPesan
         FROM PesanChat pc
         WHERE pc.idKonsultasi = :consultation_id
           AND pc.idPengirim = :patient_user_id
         ORDER BY pc.waktu ASC, pc.idPesan ASC
         LIMIT 1"
    );
    $complaintStmt->execute([
        ':consultation_id' => $consultationId,
        ':patient_user_id' => $row['patient_user_id'],
    ]);
    $complaint = (string) ($complaintStmt->fetchColumn() ?: 'Keluhan belum dicatat.');

    $gender = strtoupper((string) ($row['gender'] ?? ''));
    $genderLabel = $gender === 'L' ? 'Laki-laki' : ($gender === 'P' ? 'Perempuan' : '-');
    $ageLabel = '-';
    if (!empty($row['dob'])) {
        $ageLabel = (string) date_diff(new DateTime((string) $row['dob']), new DateTime())->y . ' Tahun';
    }

    return [
        'consultation_id' => (int) $row['idkonsultasi'],
        'scheduled_at' => (string) $row['tanggal'],
        'scheduled_label' => date('d M Y H:i', strtotime((string) $row['tanggal'])) . ' WIB',
        'status' => (string) $row['status'],
        'patient' => [
            'id' => (int) $row['idpasien'],
            'user_id' => (int) $row['patient_user_id'],
            'name' => (string) $row['patient_name'],
            'gender' => $genderLabel,
            'age' => $ageLabel,
            'weight' => (string) ($row['weight'] ?? '-'),
            'height' => (string) ($row['height'] ?? '-'),
            'allergy' => trim((string) ($row['allergy_notes'] ?? '')) ?: 'Tidak ada data',
            'complaint' => $complaint,
        ],
        'doctor' => [
            'id' => (int) $row['iddokter'],
            'name' => (string) $row['doctor_name'],
            'specialization' => (string) $row['specialization'],
            'license' => (string) $row['license'],
        ],
        'diagnosis' => [
            'id' => (int) ($diagnosis['iddiagnosis'] ?? 0),
            'description' => (string) ($diagnosis['deskripsi'] ?? ''),
            'notes' => (string) ($diagnosis['catatandokter'] ?? ''),
        ],
        'prescription' => [
            'id' => (int) ($prescription['idresep'] ?? 0),
            'notes' => (string) ($prescription['catatan'] ?? ''),
            'created_at' => (string) ($prescription['tanggalpembuatan'] ?? ''),
            'items' => array_map(static function (array $item): array {
                return [
                    'id' => (int) $item['iditemresep'],
                    'medicine_id' => (int) $item['idobat'],
                    'name' => (string) $item['nama'],
                    'dosage' => (string) ($item['dosis'] ?? ''),
                    'qty' => (int) $item['jumlah'],
                    'instruction' => (string) ($item['aturanpakai'] ?? ''),
                    'price' => (int) ($item['harga'] ?? 0),
                    'stock' => (int) ($item['stok'] ?? 0),
                ];
            }, $prescriptionItems),
        ],
    ];
}

function getMedicineCatalog(PDO $pdo): array
{
    $stmt = $pdo->query(
        'SELECT idObat, nama, dosis, harga, stok, deskripsi
         FROM Obat
         ORDER BY nama ASC'
    );

    $medicines = [];
    foreach ($stmt->fetchAll() as $row) {
        $medicines[] = [
            'id' => (int) $row['idobat'],
            'name' => (string) $row['nama'],
            'dosage' => (string) ($row['dosis'] ?? ''),
            'price' => (int) ($row['harga'] ?? 0),
            'stock' => (int) ($row['stok'] ?? 0),
            'description' => (string) ($row['deskripsi'] ?? ''),
        ];
    }

    return $medicines;
}

function saveConsultationPrescription(PDO $pdo, int $consultationId, int $doctorUserId, string $diagnosis, string $notes, array $items): array
{
    ensureStaffPortalSchema($pdo);
    $clinical = getConsultationClinicalData($pdo, $consultationId, $doctorUserId);
    if (!$clinical) {
        throw new RuntimeException('Data konsultasi tidak ditemukan atau tidak dapat diakses.');
    }

    $diagnosis = sanitizeText($diagnosis, 255);
    $notes = trim($notes);
    if ($diagnosis === '') {
        throw new RuntimeException('Diagnosis wajib diisi.');
    }

    if (!$items) {
        throw new RuntimeException('Minimal satu obat harus dipilih.');
    }

    $catalog = [];
    foreach (getMedicineCatalog($pdo) as $medicine) {
        $catalog[$medicine['id']] = $medicine;
    }

    $normalizedItems = [];
    foreach ($items as $item) {
        $medicineId = (int) ($item['medicine_id'] ?? 0);
        $qty = (int) ($item['qty'] ?? 0);
        $instruction = sanitizeText((string) ($item['instruction'] ?? ''), 100);

        if ($medicineId <= 0 || !isset($catalog[$medicineId])) {
            throw new RuntimeException('Ada obat yang tidak valid.');
        }

        if ($qty <= 0) {
            throw new RuntimeException('Jumlah obat harus lebih dari 0.');
        }

        $normalizedItems[] = [
            'medicine_id' => $medicineId,
            'qty' => $qty,
            'instruction' => $instruction,
        ];
    }

    try {
        $pdo->beginTransaction();

        $diagnosisStmt = $pdo->prepare('SELECT idDiagnosis FROM Diagnosis WHERE idKonsultasi = :consultation_id LIMIT 1');
        $diagnosisStmt->execute([':consultation_id' => $consultationId]);
        $existingDiagnosisId = (int) ($diagnosisStmt->fetchColumn() ?: 0);

        if ($existingDiagnosisId > 0) {
            $updateDiagnosis = $pdo->prepare(
                'UPDATE Diagnosis SET deskripsi = :deskripsi, catatanDokter = :catatan WHERE idDiagnosis = :idDiagnosis'
            );
            $updateDiagnosis->execute([
                ':deskripsi' => $diagnosis,
                ':catatan' => $notes !== '' ? $notes : null,
                ':idDiagnosis' => $existingDiagnosisId,
            ]);
        } else {
            $insertDiagnosis = $pdo->prepare(
                'INSERT INTO Diagnosis (idKonsultasi, deskripsi, catatanDokter)
                 VALUES (:consultation_id, :deskripsi, :catatan)'
            );
            $insertDiagnosis->execute([
                ':consultation_id' => $consultationId,
                ':deskripsi' => $diagnosis,
                ':catatan' => $notes !== '' ? $notes : null,
            ]);
        }

        $prescriptionStmt = $pdo->prepare('SELECT idResep FROM Resep WHERE idKonsultasi = :consultation_id LIMIT 1');
        $prescriptionStmt->execute([':consultation_id' => $consultationId]);
        $prescriptionId = (int) ($prescriptionStmt->fetchColumn() ?: 0);

        if ($prescriptionId > 0) {
            $updatePrescription = $pdo->prepare('UPDATE Resep SET catatan = :catatan, tanggalPembuatan = CURRENT_TIMESTAMP WHERE idResep = :idResep');
            $updatePrescription->execute([
                ':catatan' => $notes !== '' ? $notes : null,
                ':idResep' => $prescriptionId,
            ]);
            $pdo->prepare('DELETE FROM ItemResep WHERE idResep = :idResep')->execute([':idResep' => $prescriptionId]);
        } else {
            $insertPrescription = $pdo->prepare(
                'INSERT INTO Resep (idKonsultasi, catatan, tanggalPembuatan)
                 VALUES (:consultation_id, :catatan, CURRENT_TIMESTAMP)
                 RETURNING idResep'
            );
            $insertPrescription->execute([
                ':consultation_id' => $consultationId,
                ':catatan' => $notes !== '' ? $notes : null,
            ]);
            $prescriptionId = (int) $insertPrescription->fetchColumn();
        }

        $insertItem = $pdo->prepare(
            'INSERT INTO ItemResep (idResep, idObat, jumlah, aturanPakai)
             VALUES (:idResep, :idObat, :jumlah, :aturanPakai)'
        );
        foreach ($normalizedItems as $item) {
            $insertItem->execute([
                ':idResep' => $prescriptionId,
                ':idObat' => $item['medicine_id'],
                ':jumlah' => $item['qty'],
                ':aturanPakai' => $item['instruction'] !== '' ? $item['instruction'] : null,
            ]);
        }

        syncPrescriptionOrder($pdo, $prescriptionId, (int) $clinical['patient']['id']);

        $pdo->prepare("UPDATE SesiKonsultasi SET status = 'Selesai' WHERE idKonsultasi = :consultation_id")
            ->execute([':consultation_id' => $consultationId]);

        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }

    return getConsultationClinicalData($pdo, $consultationId, $doctorUserId) ?? [];
}

function syncPrescriptionOrder(PDO $pdo, int $prescriptionId, int $patientId): int
{
    ensureStaffPortalSchema($pdo);
    ensureMarketplacePaymentSchema($pdo);

    $prescriptionStmt = $pdo->prepare(
        'SELECT idPemesanan FROM Resep WHERE idResep = :idResep LIMIT 1'
    );
    $prescriptionStmt->execute([':idResep' => $prescriptionId]);
    $orderId = (int) ($prescriptionStmt->fetchColumn() ?: 0);

    $itemsStmt = $pdo->prepare(
        "SELECT ir.idObat, ir.jumlah, COALESCE(o.harga, 0) AS harga
         FROM ItemResep ir
         INNER JOIN Obat o ON o.idObat = ir.idObat
         WHERE ir.idResep = :idResep"
    );
    $itemsStmt->execute([':idResep' => $prescriptionId]);
    $items = $itemsStmt->fetchAll();

    if (!$items) {
        throw new RuntimeException('Resep belum memiliki item obat.');
    }

    $grandTotal = 0;
    foreach ($items as $item) {
        $grandTotal += ((int) $item['jumlah']) * ((int) $item['harga']);
    }

    if ($orderId > 0) {
        $pdo->prepare('DELETE FROM ItemPemesanan WHERE idPemesanan = :idPemesanan')->execute([':idPemesanan' => $orderId]);
        $pdo->prepare('UPDATE Pemesanan SET totalHarga = :totalHarga, statusPesanan = :statusPesanan WHERE idPemesanan = :idPemesanan')
            ->execute([
                ':totalHarga' => $grandTotal,
                ':statusPesanan' => 'Menunggu Pembayaran',
                ':idPemesanan' => $orderId,
            ]);
    } else {
        $insertOrder = $pdo->prepare(
            "INSERT INTO Pemesanan (idPasien, totalHarga, statusPesanan, tanggal)
             VALUES (:idPasien, :totalHarga, 'Menunggu Pembayaran', CURRENT_TIMESTAMP)
             RETURNING idPemesanan"
        );
        $insertOrder->execute([
            ':idPasien' => $patientId,
            ':totalHarga' => $grandTotal,
        ]);
        $orderId = (int) $insertOrder->fetchColumn();

        $pdo->prepare('UPDATE Resep SET idPemesanan = :idPemesanan WHERE idResep = :idResep')
            ->execute([
                ':idPemesanan' => $orderId,
                ':idResep' => $prescriptionId,
            ]);

        $insertShipping = $pdo->prepare(
            "INSERT INTO Pengiriman (idPemesanan, kurir, nomorResi, statusPengiriman)
             VALUES (:idPemesanan, 'CareSync Pharmacy', NULL, 'Menunggu Pembayaran')
             RETURNING idPengiriman"
        );
        $insertShipping->execute([':idPemesanan' => $orderId]);
        $shippingId = (int) $insertShipping->fetchColumn();
        appendMarketplaceTrackingHistory($pdo, $shippingId, 'Menunggu Pembayaran');

        $gateway = buildMarketplaceGatewayPayload('QRIS', $grandTotal);
        $insertPayment = $pdo->prepare(
            "INSERT INTO Pembayaran (
                idPemesanan, metode, status, waktuBayar, payment_code, payment_channel, payment_type,
                payment_reference, payment_account_name, payment_account_number, payment_instructions, payment_payload
             ) VALUES (
                :idPemesanan, 'QRIS', 'Pending', NULL, 'QRIS', 'QRIS', 'qris',
                :reference, :account_name, :account_number, :instructions, CAST(:payload AS JSONB)
             )"
        );
        $insertPayment->execute([
            ':idPemesanan' => $orderId,
            ':reference' => $gateway['reference'] ?? null,
            ':account_name' => $gateway['account_name'] ?? 'CareSync QRIS',
            ':account_number' => $gateway['account_number'] ?? null,
            ':instructions' => $gateway['instructions'] ?? null,
            ':payload' => json_encode($gateway, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);
    }

    $insertItem = $pdo->prepare(
        'INSERT INTO ItemPemesanan (idPemesanan, idObat, jumlah, subtotal)
         VALUES (:idPemesanan, :idObat, :jumlah, :subtotal)'
    );
    foreach ($items as $item) {
        $insertItem->execute([
            ':idPemesanan' => $orderId,
            ':idObat' => (int) $item['idobat'],
            ':jumlah' => (int) $item['jumlah'],
            ':subtotal' => ((int) $item['jumlah']) * ((int) $item['harga']),
        ]);
    }

    return $orderId;
}

function getDoctorMedicalRecords(PDO $pdo, int $doctorUserId): array
{
    $stmt = $pdo->prepare(
        "SELECT
            p.idPasien,
            pu.id AS patient_user_id,
            pu.nama AS patient_name,
            pu.gender,
            pu.dob,
            MAX(sk.tanggal) AS last_visit_at,
            (
                SELECT pc.isiPesan
                FROM PesanChat pc
                INNER JOIN SesiKonsultasi sk2 ON sk2.idKonsultasi = pc.idKonsultasi
                WHERE sk2.idDokter = d.idDokter
                  AND sk2.idPasien = p.idPasien
                  AND pc.idPengirim = pu.id
                ORDER BY pc.waktu DESC, pc.idPesan DESC
                LIMIT 1
            ) AS last_complaint,
            (
                SELECT dg.deskripsi
                FROM Diagnosis dg
                INNER JOIN SesiKonsultasi sk3 ON sk3.idKonsultasi = dg.idKonsultasi
                WHERE sk3.idDokter = d.idDokter
                  AND sk3.idPasien = p.idPasien
                ORDER BY dg.idDiagnosis DESC
                LIMIT 1
            ) AS last_diagnosis
         FROM Dokter d
         INNER JOIN SesiKonsultasi sk ON sk.idDokter = d.idDokter
         INNER JOIN Pasien p ON p.idPasien = sk.idPasien
         INNER JOIN users pu ON pu.id = p.id_user
         WHERE d.id_user = :doctor_user_id
         GROUP BY d.idDokter, p.idPasien, pu.id, pu.nama, pu.gender, pu.dob
         ORDER BY MAX(sk.tanggal) DESC, pu.nama ASC"
    );
    $stmt->execute([':doctor_user_id' => $doctorUserId]);

    $records = [];
    foreach ($stmt->fetchAll() as $row) {
        $age = '-';
        if (!empty($row['dob'])) {
            $age = date_diff(new DateTime((string) $row['dob']), new DateTime())->y;
        }

        $records[] = [
            'patient_id' => (int) $row['idpasien'],
            'patient_user_id' => (int) $row['patient_user_id'],
            'name' => (string) $row['patient_name'],
            'gender' => strtoupper((string) ($row['gender'] ?? '')) === 'L' ? 'Laki-laki' : 'Perempuan',
            'age' => $age,
            'record_code' => 'MED-' . str_pad((string) $row['idpasien'], 4, '0', STR_PAD_LEFT),
            'last_visit_at' => (string) $row['last_visit_at'],
            'last_visit_label' => date('d M Y H:i', strtotime((string) $row['last_visit_at'])) . ' WIB',
            'last_complaint' => trim((string) ($row['last_complaint'] ?? '')) ?: 'Belum ada keluhan dicatat',
            'last_diagnosis' => trim((string) ($row['last_diagnosis'] ?? '')) ?: 'Belum ada diagnosis',
            'avatar' => getInitials((string) $row['patient_name']),
        ];
    }

    return $records;
}

function getDoctorPatientMedicalRecord(PDO $pdo, int $doctorUserId, int $patientUserId): ?array
{
    $stmt = $pdo->prepare(
        "SELECT
            p.idPasien,
            pu.id AS patient_user_id,
            pu.nama AS patient_name,
            pu.gender,
            pu.dob,
            p.weight,
            p.height,
            p.blood_type,
            p.allergy_notes
         FROM Dokter d
         INNER JOIN SesiKonsultasi sk ON sk.idDokter = d.idDokter
         INNER JOIN Pasien p ON p.idPasien = sk.idPasien
         INNER JOIN users pu ON pu.id = p.id_user
         WHERE d.id_user = :doctor_user_id
           AND pu.id = :patient_user_id
         GROUP BY p.idPasien, pu.id, pu.nama, pu.gender, pu.dob, p.weight, p.height, p.blood_type, p.allergy_notes
         LIMIT 1"
    );
    $stmt->execute([
        ':doctor_user_id' => $doctorUserId,
        ':patient_user_id' => $patientUserId,
    ]);
    $patient = $stmt->fetch();

    if (!$patient) {
        return null;
    }

    $historyStmt = $pdo->prepare(
        "SELECT
            sk.idKonsultasi,
            sk.tanggal,
            sk.status::text AS status,
            du.nama AS doctor_name,
            COALESCE(d.spesialisasi, 'Dokter Umum') AS specialization,
            COALESCE(diag.deskripsi, 'Belum ada diagnosis') AS diagnosis,
            COALESCE(diag.catatanDokter, '') AS notes,
            (
                SELECT pc.isiPesan
                FROM PesanChat pc
                WHERE pc.idKonsultasi = sk.idKonsultasi
                  AND pc.idPengirim = pu.id
                ORDER BY pc.waktu ASC, pc.idPesan ASC
                LIMIT 1
            ) AS complaint,
            r.idResep
         FROM Dokter d
         INNER JOIN SesiKonsultasi sk ON sk.idDokter = d.idDokter
         INNER JOIN users du ON du.id = d.id_user
         INNER JOIN Pasien p ON p.idPasien = sk.idPasien
         INNER JOIN users pu ON pu.id = p.id_user
         LEFT JOIN Diagnosis diag ON diag.idKonsultasi = sk.idKonsultasi
         LEFT JOIN Resep r ON r.idKonsultasi = sk.idKonsultasi
         WHERE d.id_user = :doctor_user_id
           AND pu.id = :patient_user_id
         ORDER BY sk.tanggal DESC"
    );
    $historyStmt->execute([
        ':doctor_user_id' => $doctorUserId,
        ':patient_user_id' => $patientUserId,
    ]);

    $history = [];
    foreach ($historyStmt->fetchAll() as $row) {
        $prescriptionItems = [];
        if (!empty($row['idresep'])) {
            $itemStmt = $pdo->prepare(
                "SELECT o.nama, ir.jumlah, ir.aturanPakai
                 FROM ItemResep ir
                 INNER JOIN Obat o ON o.idObat = ir.idObat
                 WHERE ir.idResep = :idResep
                 ORDER BY ir.idItemResep ASC"
            );
            $itemStmt->execute([':idResep' => $row['idresep']]);
            $prescriptionItems = $itemStmt->fetchAll();
        }

        $history[] = [
            'consultation_id' => (int) $row['idkonsultasi'],
            'date' => (string) $row['tanggal'],
            'date_label' => date('d M Y H:i', strtotime((string) $row['tanggal'])) . ' WIB',
            'status' => (string) $row['status'],
            'doctor_name' => (string) $row['doctor_name'],
            'specialization' => (string) $row['specialization'],
            'complaint' => trim((string) ($row['complaint'] ?? '')) ?: 'Keluhan belum dicatat',
            'diagnosis' => (string) $row['diagnosis'],
            'notes' => (string) ($row['notes'] ?? ''),
            'has_prescription' => !empty($row['idresep']),
            'prescription_id' => (int) ($row['idresep'] ?? 0),
            'prescription_items' => array_map(static function (array $item): array {
                return [
                    'name' => (string) $item['nama'],
                    'qty' => (int) $item['jumlah'],
                    'instruction' => (string) ($item['aturanpakai'] ?? ''),
                ];
            }, $prescriptionItems),
        ];
    }

    $age = '-';
    if (!empty($patient['dob'])) {
        $age = date_diff(new DateTime((string) $patient['dob']), new DateTime())->y;
    }

    return [
        'patient' => [
            'id' => (int) $patient['idpasien'],
            'user_id' => (int) $patient['patient_user_id'],
            'name' => (string) $patient['patient_name'],
            'record_code' => 'MED-' . str_pad((string) $patient['idpasien'], 4, '0', STR_PAD_LEFT),
            'gender' => strtoupper((string) ($patient['gender'] ?? '')) === 'L' ? 'Laki-laki' : 'Perempuan',
            'age' => $age,
            'weight' => (string) ($patient['weight'] ?? '-'),
            'height' => (string) ($patient['height'] ?? '-'),
            'blood_type' => (string) ($patient['blood_type'] ?? '-'),
            'allergy' => trim((string) ($patient['allergy_notes'] ?? '')) ?: 'Tidak ada data',
            'avatar' => getInitials((string) $patient['patient_name']),
        ],
        'history' => $history,
    ];
}

function getPrescriptionForPharmacist(PDO $pdo, int $prescriptionId): ?array
{
    $stmt = $pdo->prepare(
        "SELECT
            r.idResep,
            r.catatan,
            r.tanggalPembuatan,
            sk.idKonsultasi,
            sk.status::text AS consultation_status,
            pu.nama AS patient_name,
            pu.gender,
            pu.dob,
            pa.allergy_notes,
            du.nama AS doctor_name,
            COALESCE(d.spesialisasi, 'Dokter Umum') AS specialization,
            COALESCE(diag.deskripsi, 'Belum ada diagnosis') AS diagnosis,
            order_match.idPemesanan,
            COALESCE(order_match.totalHarga, 0) AS order_total,
            COALESCE(py.status, '') AS payment_status
         FROM Resep r
         INNER JOIN SesiKonsultasi sk ON sk.idKonsultasi = r.idKonsultasi
         INNER JOIN Pasien pa ON pa.idPasien = sk.idPasien
         INNER JOIN users pu ON pu.id = pa.id_user
         INNER JOIN Dokter d ON d.idDokter = sk.idDokter
         INNER JOIN users du ON du.id = d.id_user
         LEFT JOIN Diagnosis diag ON diag.idKonsultasi = sk.idKonsultasi
         LEFT JOIN LATERAL (
            SELECT pm.idPemesanan, pm.totalHarga
            FROM Pemesanan pm
            WHERE pm.idPasien = pa.idPasien
            ORDER BY pm.tanggal DESC
            LIMIT 1
         ) AS order_match ON TRUE
         LEFT JOIN Pembayaran py ON py.idPemesanan = order_match.idPemesanan
         WHERE r.idResep = :idResep
         LIMIT 1"
    );
    $stmt->execute([':idResep' => $prescriptionId]);
    $row = $stmt->fetch();

    if (!$row) {
        return null;
    }

    $itemStmt = $pdo->prepare(
        "SELECT ir.idItemResep, ir.jumlah, ir.aturanPakai, o.idObat, o.nama, o.harga, o.stok, COALESCE(o.dosis, '') AS dosis
         FROM ItemResep ir
         INNER JOIN Obat o ON o.idObat = ir.idObat
         WHERE ir.idResep = :idResep
         ORDER BY ir.idItemResep ASC"
    );
    $itemStmt->execute([':idResep' => $prescriptionId]);

    $items = [];
    foreach ($itemStmt->fetchAll() as $item) {
        $items[] = [
            'item_id' => (int) $item['iditemresep'],
            'medicine_id' => (int) $item['idobat'],
            'name' => (string) $item['nama'],
            'qty' => (int) $item['jumlah'],
            'instruction' => (string) ($item['aturanpakai'] ?? ''),
            'price' => (int) ($item['harga'] ?? 0),
            'stock' => (int) ($item['stok'] ?? 0),
            'dosage' => (string) ($item['dosis'] ?? ''),
            'subtotal' => ((int) ($item['harga'] ?? 0)) * ((int) $item['jumlah']),
        ];
    }

    $ageLabel = '-';
    if (!empty($row['dob'])) {
        $ageLabel = (string) date_diff(new DateTime((string) $row['dob']), new DateTime())->y . ' Tahun';
    }

    return [
        'id' => (int) $row['idresep'],
        'code' => 'RX-' . str_pad((string) $row['idresep'], 6, '0', STR_PAD_LEFT),
        'created_at' => (string) $row['tanggalpembuatan'],
        'created_label' => date('d M Y H:i', strtotime((string) $row['tanggalpembuatan'])) . ' WIB',
        'consultation_id' => (int) $row['idkonsultasi'],
        'consultation_status' => (string) $row['consultation_status'],
        'patient' => [
            'name' => (string) $row['patient_name'],
            'gender' => strtoupper((string) ($row['gender'] ?? '')) === 'L' ? 'Laki-laki' : 'Perempuan',
            'age' => $ageLabel,
            'allergy' => trim((string) ($row['allergy_notes'] ?? '')) ?: 'Tidak ada data',
        ],
        'doctor' => [
            'name' => (string) $row['doctor_name'],
            'specialization' => (string) $row['specialization'],
        ],
        'diagnosis' => (string) $row['diagnosis'],
        'notes' => (string) ($row['catatan'] ?? ''),
        'items' => $items,
        'subtotal' => array_sum(array_column($items, 'subtotal')),
        'order_id' => (int) ($row['idpemesanan'] ?? 0),
        'order_total' => (int) ($row['order_total'] ?? 0),
        'payment_status' => (string) ($row['payment_status'] ?? ''),
    ];
}

function getPharmacistTransactionHistory(PDO $pdo): array
{
    $orders = getMarketplaceOrdersForManagement($pdo);
    $history = [];

    foreach ($orders as $order) {
        $detailStmt = $pdo->prepare(
            "SELECT du.nama AS doctor_name
             FROM Pemesanan pm
             INNER JOIN Pasien p ON p.idPasien = pm.idPasien
             INNER JOIN SesiKonsultasi sk ON sk.idPasien = p.idPasien
             INNER JOIN Dokter d ON d.idDokter = sk.idDokter
             INNER JOIN users du ON du.id = d.id_user
             WHERE pm.idPemesanan = :idPemesanan
             ORDER BY sk.tanggal DESC
             LIMIT 1"
        );
        $detailStmt->execute([':idPemesanan' => $order['id']]);
        $doctorName = (string) ($detailStmt->fetchColumn() ?: 'Dokter CareSync');

        $itemsStmt = $pdo->prepare(
            "SELECT o.nama, ip.jumlah, COALESCE(o.harga, 0) AS harga, ip.subtotal
             FROM ItemPemesanan ip
             INNER JOIN Obat o ON o.idObat = ip.idObat
             WHERE ip.idPemesanan = :idPemesanan
             ORDER BY ip.idItemPemesanan ASC"
        );
        $itemsStmt->execute([':idPemesanan' => $order['id']]);

        $items = [];
        foreach ($itemsStmt->fetchAll() as $item) {
            $items[] = [
                'name' => (string) $item['nama'],
                'qty' => (int) $item['jumlah'],
                'price' => (int) ($item['harga'] ?? 0),
                'subtotal' => (int) ($item['subtotal'] ?? 0),
            ];
        }

        $history[] = [
            'id' => $order['order_code'],
            'date' => date('d M Y', strtotime((string) $order['created_at'])),
            'time' => date('H:i', strtotime((string) $order['created_at'])) . ' WIB',
            'patient' => $order['patient_name'],
            'doctor' => $doctorName,
            'status' => $order['shipping_status'] === 'Selesai' ? 'Selesai' : (($order['shipping_status'] === 'Dibatalkan' || $order['status'] === 'Dibatalkan') ? 'Dibatalkan' : 'Diproses'),
            'payment' => $order['payment_method'],
            'total' => (int) $order['total'],
            'items' => $items,
        ];
    }

    return $history;
}

function getAdminStaffMembers(PDO $pdo): array
{
    ensureStaffPortalSchema($pdo);

    $stmt = $pdo->query(
        "SELECT
            u.id,
            u.nama,
            u.email,
            u.role,
            COALESCE(u.staff_status, 'Aktif') AS staff_status,
            COALESCE(d.spesialisasi, CASE WHEN LOWER(COALESCE(u.role, '')) = 'apoteker' THEN 'Instalasi Farmasi' ELSE 'Administrasi' END) AS specialty
         FROM users u
         LEFT JOIN Dokter d ON d.id_user = u.id
         WHERE LOWER(COALESCE(u.role, '')) <> 'user'
           AND LOWER(COALESCE(u.role, '')) <> 'pasien'
         ORDER BY u.nama ASC"
    );

    $staff = [];
    foreach ($stmt->fetchAll() as $row) {
        $staff[] = [
            'id' => (int) $row['id'],
            'code' => 'EMP-' . str_pad((string) $row['id'], 3, '0', STR_PAD_LEFT),
            'name' => (string) $row['nama'],
            'email' => (string) $row['email'],
            'role' => (string) $row['role'],
            'status' => (string) $row['staff_status'],
            'specialty' => (string) $row['specialty'],
            'avatarColor' => substr(md5((string) $row['email']), 0, 6),
        ];
    }

    return $staff;
}

function saveAdminStaffMember(PDO $pdo, array $payload): int
{
    ensureStaffPortalSchema($pdo);

    $staffId = (int) ($payload['staff_id'] ?? 0);
    $name = sanitizeText((string) ($payload['name'] ?? ''), 100);
    $email = normalizeEmail((string) ($payload['email'] ?? ''));
    $role = sanitizeText((string) ($payload['role'] ?? ''), 50);
    $status = sanitizeText((string) ($payload['status'] ?? 'Aktif'), 20);
    $specialty = sanitizeText((string) ($payload['specialty'] ?? ''), 100);

    if ($name === '' || $email === '' || $role === '') {
        throw new RuntimeException('Nama, email, dan role wajib diisi.');
    }

    if (!isValidEmail($email)) {
        throw new RuntimeException('Format email staf tidak valid.');
    }

    $defaultPassword = password_hash('Caresync123!', PASSWORD_DEFAULT);

    if ($staffId > 0) {
        $checkStmt = $pdo->prepare('SELECT id FROM users WHERE email = :email AND id <> :id');
        $checkStmt->execute([':email' => $email, ':id' => $staffId]);
        if ($checkStmt->fetchColumn()) {
            throw new RuntimeException('Email sudah digunakan staf lain.');
        }

        $stmt = $pdo->prepare(
            'UPDATE users
             SET nama = :nama, email = :email, role = :role, staff_status = :status
             WHERE id = :id'
        );
        $stmt->execute([
            ':nama' => $name,
            ':email' => $email,
            ':role' => $role,
            ':status' => $status,
            ':id' => $staffId,
        ]);
    } else {
        $checkStmt = $pdo->prepare('SELECT id FROM users WHERE email = :email');
        $checkStmt->execute([':email' => $email]);
        if ($checkStmt->fetchColumn()) {
            throw new RuntimeException('Email sudah terdaftar.');
        }

        $stmt = $pdo->prepare(
            "INSERT INTO users (nama, email, password, role, staff_status, created_at)
             VALUES (:nama, :email, :password, :role, :status, CURRENT_TIMESTAMP)
             RETURNING id"
        );
        $stmt->execute([
            ':nama' => $name,
            ':email' => $email,
            ':password' => $defaultPassword,
            ':role' => $role,
            ':status' => $status,
        ]);
        $staffId = (int) $stmt->fetchColumn();
    }

    $normalizedRole = normalizeUserRole($role);
    if ($normalizedRole === 'dokter') {
        $doctorStmt = $pdo->prepare('SELECT idDokter FROM Dokter WHERE id_user = :id_user');
        $doctorStmt->execute([':id_user' => $staffId]);
        $doctorId = (int) ($doctorStmt->fetchColumn() ?: 0);
        if ($doctorId > 0) {
            $pdo->prepare('UPDATE Dokter SET spesialisasi = :spesialisasi WHERE id_user = :id_user')
                ->execute([
                    ':spesialisasi' => $specialty !== '' ? $specialty : 'Dokter Umum',
                    ':id_user' => $staffId,
                ]);
        } else {
            $pdo->prepare(
                'INSERT INTO Dokter (id_user, spesialisasi, nomorSTR)
                 VALUES (:id_user, :spesialisasi, :nomorSTR)'
            )->execute([
                ':id_user' => $staffId,
                ':spesialisasi' => $specialty !== '' ? $specialty : 'Dokter Umum',
                ':nomorSTR' => 'STR-CS-' . str_pad((string) $staffId, 4, '0', STR_PAD_LEFT),
            ]);
        }
        $doctorIdStmt = $pdo->prepare('SELECT idDokter FROM Dokter WHERE id_user = :id_user LIMIT 1');
        $doctorIdStmt->execute([':id_user' => $staffId]);
        ensureDoctorScheduleSeed($pdo, (int) $doctorIdStmt->fetchColumn());
    } else {
        $pdo->prepare('DELETE FROM Dokter WHERE id_user = :id_user')->execute([':id_user' => $staffId]);
    }

    return $staffId;
}

function getAdminReportData(PDO $pdo): array
{
    $medicalRows = [];
    $medicalStmt = $pdo->query(
        "SELECT sk.idKonsultasi, sk.tanggal, pu.nama AS patient_name, du.nama AS doctor_name, COALESCE(d.spesialisasi, 'Dokter Umum') AS specialization, sk.status::text AS status
         FROM SesiKonsultasi sk
         INNER JOIN Pasien p ON p.idPasien = sk.idPasien
         INNER JOIN users pu ON pu.id = p.id_user
         INNER JOIN Dokter d ON d.idDokter = sk.idDokter
         INNER JOIN users du ON du.id = d.id_user
         ORDER BY sk.tanggal DESC"
    );

    $metaMap = getDoctorMetaMap();
    foreach ($medicalStmt->fetchAll() as $row) {
        $fee = 50000;
        foreach ($metaMap as $meta) {
            if (($meta['name'] ?? '') === $row['doctor_name']) {
                $fee = (int) ($meta['fee'] ?? $fee);
                break;
            }
        }
        $medicalRows[] = [
            'id' => 'INV-MED-' . date('Ymd', strtotime((string) $row['tanggal'])) . '-' . str_pad((string) $row['idkonsultasi'], 4, '0', STR_PAD_LEFT),
            'date' => date('d M Y', strtotime((string) $row['tanggal'])),
            'time' => date('H:i', strtotime((string) $row['tanggal'])) . ' WIB',
            'patient' => (string) $row['patient_name'],
            'category' => 'Layanan Medis',
            'detail' => 'Konsultasi ' . $row['doctor_name'] . ' (' . $row['specialization'] . ')',
            'method' => 'Konsultasi',
            'amount' => $fee,
            'created_at' => (string) $row['tanggal'],
        ];
    }

    $pharmacyRows = [];
    foreach (getPharmacistTransactionHistory($pdo) as $trx) {
        $createdAt = date('Y-m-d H:i:s', strtotime($trx['date'] . ' ' . str_replace(' WIB', '', $trx['time'])));
        $pharmacyRows[] = [
            'id' => $trx['id'],
            'date' => $trx['date'],
            'time' => $trx['time'],
            'patient' => $trx['patient'],
            'category' => 'Apotek',
            'detail' => 'Transaksi farmasi',
            'method' => $trx['payment'],
            'amount' => (int) $trx['total'],
            'created_at' => $createdAt,
        ];
    }

    $transactions = array_merge($medicalRows, $pharmacyRows);
    usort($transactions, static fn(array $a, array $b): int => strcmp((string) $b['created_at'], (string) $a['created_at']));

    $labels = [];
    $medicalSeries = [];
    $pharmacySeries = [];
    $days = [];
    for ($i = 4; $i >= 0; $i--) {
        $day = (new DateTimeImmutable("-{$i} day"))->format('Y-m-d');
        $days[] = $day;
        $labels[] = date('d M', strtotime($day));
        $medicalSeries[$day] = 0;
        $pharmacySeries[$day] = 0;
    }
    foreach ($transactions as $trx) {
        $day = date('Y-m-d', strtotime((string) $trx['created_at']));
        if (!isset($medicalSeries[$day])) {
            continue;
        }
        if ($trx['category'] === 'Layanan Medis') {
            $medicalSeries[$day] += (int) $trx['amount'];
        } else {
            $pharmacySeries[$day] += (int) $trx['amount'];
        }
    }

    return [
        'transactions' => $transactions,
        'total_medical' => array_sum(array_column($medicalRows, 'amount')),
        'total_pharmacy' => array_sum(array_column($pharmacyRows, 'amount')),
        'chart' => [
            'labels' => $labels,
            'medical' => array_values($medicalSeries),
            'pharmacy' => array_values($pharmacySeries),
        ],
    ];
}
