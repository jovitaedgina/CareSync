<?php

function ensureDoctorScheduleSchema(PDO $pdo): void
{
    static $ensured = false;

    if ($ensured) {
        return;
    }

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS DokterJadwal (
            id BIGSERIAL PRIMARY KEY,
            idDokter INT NOT NULL REFERENCES Dokter(idDokter) ON DELETE CASCADE,
            day_of_week SMALLINT NOT NULL,
            is_active BOOLEAN NOT NULL DEFAULT TRUE,
            start_time TIME NOT NULL,
            end_time TIME NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            UNIQUE (idDokter, day_of_week)
        )"
    );

    $ensured = true;
}

function ensureConsultationVideoColumns(PDO $pdo): void
{
    static $ensured = false;

    if ($ensured) {
        return;
    }

    $pdo->exec("ALTER TABLE SesiKonsultasi ADD COLUMN IF NOT EXISTS jitsi_room_name VARCHAR(180)");
    $pdo->exec("ALTER TABLE SesiKonsultasi ADD COLUMN IF NOT EXISTS jitsi_room_url TEXT");
    $pdo->exec("ALTER TABLE SesiKonsultasi ADD COLUMN IF NOT EXISTS video_call_provider VARCHAR(50) DEFAULT 'Jitsi Meet'");
    $pdo->exec("ALTER TABLE SesiKonsultasi ADD COLUMN IF NOT EXISTS video_call_created_at TIMESTAMP");
    $pdo->exec("ALTER TABLE SesiKonsultasi ADD COLUMN IF NOT EXISTS video_call_started_at TIMESTAMP");
    $pdo->exec("ALTER TABLE SesiKonsultasi ADD COLUMN IF NOT EXISTS video_call_ended_at TIMESTAMP");
    $pdo->exec("ALTER TABLE SesiKonsultasi ADD COLUMN IF NOT EXISTS patient_rating SMALLINT");
    $pdo->exec("ALTER TABLE SesiKonsultasi ADD COLUMN IF NOT EXISTS reviewed_at TIMESTAMP");
    $pdo->exec("CREATE UNIQUE INDEX IF NOT EXISTS sesi_konsultasi_jitsi_room_name_unique ON SesiKonsultasi (jitsi_room_name) WHERE jitsi_room_name IS NOT NULL");

    $ensured = true;
}

function ensureDoctorDirectorySchema(PDO $pdo): void
{
    static $ensured = false;

    if ($ensured) {
        return;
    }

    $ddl = [
        "ALTER TABLE users ADD COLUMN IF NOT EXISTS profile_photo VARCHAR(255)",
        "ALTER TABLE Dokter ADD COLUMN IF NOT EXISTS consultation_fee INT",
        "ALTER TABLE Dokter ADD COLUMN IF NOT EXISTS rating NUMERIC(3,2)",
    ];

    foreach ($ddl as $sql) {
        try {
            $pdo->exec($sql);
        } catch (Throwable $e) {
            // Abaikan jika kolom sudah ada atau engine tidak mendukung IF NOT EXISTS.
        }
    }

    $ensured = true;
}

function buildConsultationRoomName(int $consultationId, int $doctorId, int $patientId, string $scheduledAt): string
{
    $seed = implode('|', [
        JITSI_ROOM_PREFIX !== '' ? JITSI_ROOM_PREFIX : 'caresync-consultation',
        $consultationId,
        $doctorId,
        $patientId,
        $scheduledAt,
        bin2hex(random_bytes(6)),
    ]);
    $hash = strtoupper(substr(hash('sha256', $seed), 0, 18));

    return sprintf(
        '%s-%d-%s',
        JITSI_ROOM_PREFIX !== '' ? preg_replace('/[^a-z0-9]+/i', '-', strtolower(JITSI_ROOM_PREFIX)) : 'caresync-consultation',
        $consultationId,
        $hash
    );
}

function buildConsultationRoomUrl(string $roomName): string
{
    return JITSI_BASE_URL . '/' . rawurlencode($roomName);
}

function getLegacyDoctorScheduleDefaults(): array
{
    return [
        ['day_of_week' => 1, 'is_active' => true, 'start_time' => '09:00:00', 'end_time' => '15:00:00'],
        ['day_of_week' => 2, 'is_active' => true, 'start_time' => '09:00:00', 'end_time' => '15:00:00'],
        ['day_of_week' => 3, 'is_active' => true, 'start_time' => '09:00:00', 'end_time' => '15:00:00'],
        ['day_of_week' => 4, 'is_active' => false, 'start_time' => '09:00:00', 'end_time' => '15:00:00'],
        ['day_of_week' => 5, 'is_active' => true, 'start_time' => '13:00:00', 'end_time' => '18:00:00'],
        ['day_of_week' => 6, 'is_active' => false, 'start_time' => '09:00:00', 'end_time' => '12:00:00'],
        ['day_of_week' => 0, 'is_active' => false, 'start_time' => '09:00:00', 'end_time' => '12:00:00'],
    ];
}

function getDoctorCoverageScheduleDefaults(PDO $pdo, int $doctorId): array
{
    $doctorIds = $pdo->query('SELECT idDokter FROM Dokter ORDER BY idDokter ASC')->fetchAll(PDO::FETCH_COLUMN) ?: [];
    $doctorIds = array_map('intval', $doctorIds);
    $position = array_search($doctorId, $doctorIds, true);
    $position = $position === false ? 0 : (int) $position;
    $doctorCount = max(1, count($doctorIds));

    $days = [1, 2, 3, 4, 5, 6, 0];
    $schedules = [];

    foreach ($days as $dayIndex => $dayOfWeek) {
        if ($doctorCount === 1) {
            [$startTime, $endTime] = ['00:00:00', '23:59:00'];
        } elseif ($doctorCount === 2) {
            [$startTime, $endTime] = (($position + $dayIndex) % 2 === 0)
                ? ['00:00:00', '12:00:00']
                : ['12:00:00', '23:59:00'];
        } else {
            $shifts = [
                ['00:00:00', '08:00:00'],
                ['08:00:00', '16:00:00'],
                ['16:00:00', '23:59:00'],
            ];
            [$startTime, $endTime] = $shifts[($position + $dayIndex) % count($shifts)];
        }

        $schedules[] = [
            'day_of_week' => $dayOfWeek,
            'is_active' => true,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ];
    }

    return $schedules;
}

function getStoredDoctorScheduleRows(PDO $pdo, int $doctorId): array
{
    ensureDoctorScheduleSchema($pdo);

    $stmt = $pdo->prepare(
        'SELECT day_of_week, is_active, start_time, end_time
         FROM DokterJadwal
         WHERE idDokter = :idDokter
         ORDER BY day_of_week ASC'
    );
    $stmt->execute([':idDokter' => $doctorId]);

    return array_map(static function (array $row): array {
        return [
            'day_of_week' => (int) $row['day_of_week'],
            'is_active' => (bool) $row['is_active'],
            'start_time' => substr((string) $row['start_time'], 0, 8),
            'end_time' => substr((string) $row['end_time'], 0, 8),
        ];
    }, $stmt->fetchAll());
}

function doctorScheduleMatchesTemplate(array $rows, array $template): bool
{
    if (count($rows) !== count($template)) {
        return false;
    }

    $indexedRows = [];
    foreach ($rows as $row) {
        $indexedRows[(int) $row['day_of_week']] = $row;
    }

    foreach ($template as $day) {
        $current = $indexedRows[(int) $day['day_of_week']] ?? null;
        if ($current === null) {
            return false;
        }

        if ((bool) $current['is_active'] !== (bool) $day['is_active']) {
            return false;
        }

        if ((string) $current['start_time'] !== (string) $day['start_time']) {
            return false;
        }

        if ((string) $current['end_time'] !== (string) $day['end_time']) {
            return false;
        }
    }

    return true;
}

function ensureDoctorScheduleSeed(PDO $pdo, int $doctorId, bool $forceSync = false): void
{
    ensureDoctorScheduleSchema($pdo);

    $currentRows = getStoredDoctorScheduleRows($pdo, $doctorId);
    if (!$forceSync && count($currentRows) > 0) {
        return;
    }

    $insert = $pdo->prepare(
        'INSERT INTO DokterJadwal (idDokter, day_of_week, is_active, start_time, end_time, updated_at)
         VALUES (:idDokter, :day_of_week, :is_active, :start_time, :end_time, CURRENT_TIMESTAMP)
         ON CONFLICT (idDokter, day_of_week)
         DO UPDATE SET
            is_active = EXCLUDED.is_active,
            start_time = EXCLUDED.start_time,
            end_time = EXCLUDED.end_time,
            updated_at = CURRENT_TIMESTAMP'
    );

    foreach (getDoctorCoverageScheduleDefaults($pdo, $doctorId) as $schedule) {
        $insert->execute([
            ':idDokter' => $doctorId,
            ':day_of_week' => $schedule['day_of_week'],
            ':is_active' => $schedule['is_active'],
            ':start_time' => $schedule['start_time'],
            ':end_time' => $schedule['end_time'],
        ]);
    }
}

function ensureDoctorPracticeCoverage(PDO $pdo): void
{
    static $ensured = false;

    if ($ensured) {
        return;
    }

    ensureDoctorScheduleSchema($pdo);
    $doctorIds = $pdo->query('SELECT idDokter FROM Dokter ORDER BY idDokter ASC')->fetchAll(PDO::FETCH_COLUMN) ?: [];
    $legacyTemplate = getLegacyDoctorScheduleDefaults();

    foreach ($doctorIds as $doctorIdValue) {
        $doctorId = (int) $doctorIdValue;
        $currentRows = getStoredDoctorScheduleRows($pdo, $doctorId);
        if (count($currentRows) < 7 || doctorScheduleMatchesTemplate($currentRows, $legacyTemplate)) {
            ensureDoctorScheduleSeed($pdo, $doctorId, true);
        }
    }

    $ensured = true;
}

function getDoctorWeeklySchedule(PDO $pdo, int $doctorId): array
{
    ensureDoctorScheduleSeed($pdo, $doctorId);

    $stmt = $pdo->prepare(
        'SELECT day_of_week, is_active, start_time, end_time
         FROM DokterJadwal
         WHERE idDokter = :idDokter
         ORDER BY day_of_week ASC'
    );
    $stmt->execute([':idDokter' => $doctorId]);

    $scheduleMap = [];
    foreach ($stmt->fetchAll() as $row) {
        $scheduleMap[(int) $row['day_of_week']] = [
            'day_of_week' => (int) $row['day_of_week'],
            'is_active' => (bool) $row['is_active'],
            'start_time' => substr((string) $row['start_time'], 0, 5),
            'end_time' => substr((string) $row['end_time'], 0, 5),
        ];
    }

    $days = [
        ['day_of_week' => 1, 'name' => 'Senin'],
        ['day_of_week' => 2, 'name' => 'Selasa'],
        ['day_of_week' => 3, 'name' => 'Rabu'],
        ['day_of_week' => 4, 'name' => 'Kamis'],
        ['day_of_week' => 5, 'name' => 'Jumat'],
        ['day_of_week' => 6, 'name' => 'Sabtu'],
        ['day_of_week' => 0, 'name' => 'Minggu'],
    ];

    $result = [];
    foreach ($days as $day) {
        $row = $scheduleMap[$day['day_of_week']] ?? ['is_active' => false, 'start_time' => '09:00', 'end_time' => '15:00'];
        $result[] = [
            'name' => $day['name'],
            'day_of_week' => $day['day_of_week'],
            'active' => (bool) $row['is_active'],
            'start' => $row['start_time'],
            'end' => $row['end_time'],
        ];
    }

    return $result;
}

function saveDoctorWeeklySchedule(PDO $pdo, int $doctorId, array $days): void
{
    ensureDoctorScheduleSeed($pdo, $doctorId);

    $stmt = $pdo->prepare(
        'INSERT INTO DokterJadwal (idDokter, day_of_week, is_active, start_time, end_time, updated_at)
         VALUES (:idDokter, :day_of_week, :is_active, :start_time, :end_time, CURRENT_TIMESTAMP)
         ON CONFLICT (idDokter, day_of_week)
         DO UPDATE SET
            is_active = EXCLUDED.is_active,
            start_time = EXCLUDED.start_time,
            end_time = EXCLUDED.end_time,
            updated_at = CURRENT_TIMESTAMP'
    );

    foreach ($days as $day) {
        $start = trim((string) ($day['start'] ?? '09:00'));
        $end = trim((string) ($day['end'] ?? '15:00'));
        if (!preg_match('/^\d{2}:\d{2}$/', $start) || !preg_match('/^\d{2}:\d{2}$/', $end)) {
            throw new RuntimeException('Format jam praktik tidak valid.');
        }

        if ($start >= $end) {
            throw new RuntimeException('Jam mulai harus lebih kecil dari jam selesai.');
        }

        $stmt->execute([
            ':idDokter' => $doctorId,
            ':day_of_week' => (int) ($day['day_of_week'] ?? 0),
            ':is_active' => !empty($day['active']),
            ':start_time' => $start . ':00',
            ':end_time' => $end . ':00',
        ]);
    }
}

function getDoctorDailySlots(PDO $pdo, int $doctorId, string $date): array
{
    ensureDoctorScheduleSeed($pdo, $doctorId);

    $selectedDate = DateTimeImmutable::createFromFormat('Y-m-d', $date);
    if (!$selectedDate) {
        return [];
    }

    $dayOfWeek = (int) $selectedDate->format('w');
    $stmt = $pdo->prepare(
        'SELECT is_active, start_time, end_time
         FROM DokterJadwal
         WHERE idDokter = :idDokter AND day_of_week = :day_of_week
         LIMIT 1'
    );
    $stmt->execute([
        ':idDokter' => $doctorId,
        ':day_of_week' => $dayOfWeek,
    ]);
    $schedule = $stmt->fetch();

    if (!$schedule || !(bool) $schedule['is_active']) {
        return [];
    }

    $slots = [];
    $cursor = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $date . ' ' . $schedule['start_time']);
    $end = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $date . ' ' . $schedule['end_time']);

    if (!$cursor || !$end) {
        return [];
    }

    while ($cursor < $end) {
        $slots[] = $cursor->format('H:i');
        $cursor = $cursor->modify('+1 hour');
    }

    return $slots;
}

function assignConsultationVideoCall(PDO $pdo, int $consultationId, int $doctorId, int $patientId, string $scheduledAt): array
{
    ensureConsultationVideoColumns($pdo);

    $existingStmt = $pdo->prepare(
        'SELECT jitsi_room_name, jitsi_room_url
         FROM SesiKonsultasi
         WHERE idKonsultasi = :idKonsultasi
         LIMIT 1'
    );
    $existingStmt->execute([':idKonsultasi' => $consultationId]);
    $existing = $existingStmt->fetch() ?: [];

    $roomName = trim((string) ($existing['jitsi_room_name'] ?? ''));
    $roomUrl = trim((string) ($existing['jitsi_room_url'] ?? ''));

    if ($roomName !== '' && $roomUrl !== '') {
        return [
            'room_name' => $roomName,
            'room_url' => $roomUrl,
            'provider' => 'Jitsi Meet',
        ];
    }

    $roomName = buildConsultationRoomName($consultationId, $doctorId, $patientId, $scheduledAt);
    $roomUrl = buildConsultationRoomUrl($roomName);

    $updateStmt = $pdo->prepare(
        "UPDATE SesiKonsultasi
         SET jitsi_room_name = :room_name,
             jitsi_room_url = :room_url,
             video_call_provider = 'Jitsi Meet',
             video_call_created_at = COALESCE(video_call_created_at, CURRENT_TIMESTAMP)
         WHERE idKonsultasi = :idKonsultasi"
    );
    $updateStmt->execute([
        ':room_name' => $roomName,
        ':room_url' => $roomUrl,
        ':idKonsultasi' => $consultationId,
    ]);

    return [
        'room_name' => $roomName,
        'room_url' => $roomUrl,
        'provider' => 'Jitsi Meet',
    ];
}

function ensureConsultationVideoCall(PDO $pdo, array $consultation): array
{
    $consultationId = (int) ($consultation['id'] ?? $consultation['consultation_id'] ?? 0);
    $doctorId = (int) ($consultation['doctor_id'] ?? 0);
    $patientId = (int) ($consultation['patient_id'] ?? 0);
    $scheduledAt = (string) ($consultation['scheduled_at'] ?? '');
    $roomName = trim((string) ($consultation['jitsi_room_name'] ?? $consultation['video_call_room'] ?? ''));
    $roomUrl = trim((string) ($consultation['jitsi_room_url'] ?? $consultation['video_call_url'] ?? ''));

    if ($consultationId <= 0 || $doctorId <= 0 || $patientId <= 0 || $scheduledAt === '') {
        return [
            'room_name' => $roomName,
            'room_url' => $roomUrl,
            'provider' => trim((string) ($consultation['video_call_provider'] ?? 'Jitsi Meet')) ?: 'Jitsi Meet',
        ];
    }

    if ($roomName !== '' && $roomUrl !== '') {
        return [
            'room_name' => $roomName,
            'room_url' => $roomUrl,
            'provider' => trim((string) ($consultation['video_call_provider'] ?? 'Jitsi Meet')) ?: 'Jitsi Meet',
        ];
    }

    return assignConsultationVideoCall($pdo, $consultationId, $doctorId, $patientId, $scheduledAt);
}

function getDefaultConsultationFeeBySpecialization(string $specialization): int
{
    $normalized = strtolower(trim($specialization));

    if (str_contains($normalized, 'anak')) {
        return 70000;
    }
    if (str_contains($normalized, 'mata')) {
        return 75000;
    }
    if (str_contains($normalized, 'kulit') || str_contains($normalized, 'kelamin')) {
        return 60000;
    }
    if (str_contains($normalized, 'penyakit dalam') || str_contains($normalized, 'sp.pd')) {
        return 80000;
    }
    if (str_contains($normalized, 'gigi') || str_contains($normalized, 'kg')) {
        return 65000;
    }
    if (str_contains($normalized, 'psiki') || str_contains($normalized, 'sp.kj')) {
        return 90000;
    }
    if (str_contains($normalized, 'tht')) {
        return 65000;
    }
    if (str_contains($normalized, 'gizi')) {
        return 75000;
    }

    return 50000;
}

function getDefaultDoctorRatingBySpecialization(string $specialization): string
{
    $normalized = strtolower(trim($specialization));

    if (str_contains($normalized, 'penyakit dalam') || str_contains($normalized, 'psiki')) {
        return '4.9';
    }
    if (str_contains($normalized, 'umum')) {
        return '4.7';
    }

    return '4.8';
}

function normalizeDoctorPhotoUrl(?string $path): string
{
    $path = trim((string) $path);
    if ($path === '') {
        return '';
    }

    if (preg_match('#^https?://#i', $path) === 1) {
        return $path;
    }

    return rtrim(BASE_URL, '/') . '/' . ltrim(str_replace('\\', '/', $path), '/');
}

function formatDoctorPatientCount(int $count): string
{
    return number_format(max(0, $count), 0, ',', '.');
}

function getBookingSeedPatientProfiles(): array
{
    return [
        ['name' => 'Aulia Ramadhani', 'gender' => 'P', 'dob' => '1998-02-14'],
        ['name' => 'Bagas Pratama', 'gender' => 'L', 'dob' => '1996-07-20'],
        ['name' => 'Citra Lestari', 'gender' => 'P', 'dob' => '2000-11-08'],
        ['name' => 'Dimas Saputra', 'gender' => 'L', 'dob' => '1994-05-17'],
        ['name' => 'Eka Maharani', 'gender' => 'P', 'dob' => '1999-01-22'],
        ['name' => 'Fajar Nugroho', 'gender' => 'L', 'dob' => '1993-09-13'],
        ['name' => 'Gita Permata', 'gender' => 'P', 'dob' => '2001-03-05'],
        ['name' => 'Hafiz Maulana', 'gender' => 'L', 'dob' => '1997-12-01'],
        ['name' => 'Indah Salsabila', 'gender' => 'P', 'dob' => '1995-08-27'],
        ['name' => 'Jovan Kurniawan', 'gender' => 'L', 'dob' => '1992-04-10'],
        ['name' => 'Karina Putri', 'gender' => 'P', 'dob' => '1998-06-18'],
        ['name' => 'Luthfi Hidayat', 'gender' => 'L', 'dob' => '1991-10-30'],
        ['name' => 'Maya Anggraini', 'gender' => 'P', 'dob' => '2002-02-09'],
        ['name' => 'Naufal Rizki', 'gender' => 'L', 'dob' => '1996-01-15'],
        ['name' => 'Oktavia Ningsih', 'gender' => 'P', 'dob' => '1997-05-25'],
        ['name' => 'Putra Mahesa', 'gender' => 'L', 'dob' => '1990-11-11'],
        ['name' => 'Qonita Azzahra', 'gender' => 'P', 'dob' => '2001-07-07'],
        ['name' => 'Rendy Firmansyah', 'gender' => 'L', 'dob' => '1994-12-19'],
        ['name' => 'Salsa Nuraini', 'gender' => 'P', 'dob' => '1999-09-03'],
        ['name' => 'Tegar Aditya', 'gender' => 'L', 'dob' => '1995-03-28'],
        ['name' => 'Ulfa Mardiah', 'gender' => 'P', 'dob' => '1998-10-16'],
        ['name' => 'Vino Akbar', 'gender' => 'L', 'dob' => '1993-06-02'],
        ['name' => 'Wulan Safitri', 'gender' => 'P', 'dob' => '2000-04-26'],
        ['name' => 'Yoga Prasetyo', 'gender' => 'L', 'dob' => '1992-08-14'],
        ['name' => 'Zahra Amelia', 'gender' => 'P', 'dob' => '2001-12-21'],
        ['name' => 'Ananda Fikri', 'gender' => 'L', 'dob' => '1997-01-04'],
        ['name' => 'Bella Maharani', 'gender' => 'P', 'dob' => '1996-05-09'],
        ['name' => 'Chandra Wijaya', 'gender' => 'L', 'dob' => '1991-09-29'],
        ['name' => 'Dewi Kartika', 'gender' => 'P', 'dob' => '1998-11-24'],
        ['name' => 'Erwin Setiawan', 'gender' => 'L', 'dob' => '1994-02-12'],
    ];
}

function buildBookingSeedPatientEmail(array $profile, int $index): string
{
    $slug = strtolower(trim((string) ($profile['name'] ?? 'pasien')));
    $slug = preg_replace('/[^a-z0-9]+/', '.', $slug) ?? 'pasien';
    $slug = trim($slug, '.');
    if ($slug === '') {
        $slug = 'pasien';
    }

    return sprintf('%s.%02d@mail.caresync.id', $slug, $index + 1);
}

function normalizeBookingSeedPatients(PDO $pdo, array $profiles): void
{
    $stmt = $pdo->query(
        "SELECT u.id, p.idPasien, u.email
         FROM users u
         INNER JOIN Pasien p ON p.id_user = u.id
         WHERE u.email LIKE 'seed.booking.patient.%@caresync.local'
         ORDER BY u.id ASC"
    );
    $rows = $stmt->fetchAll();

    if ($rows === []) {
        return;
    }

    $updateUser = $pdo->prepare(
        'UPDATE users
         SET nama = :nama,
             email = :email,
             role = :role,
             gender = :gender,
             dob = :dob,
             phone = :phone
         WHERE id = :id'
    );
    $updatePatient = $pdo->prepare(
        'UPDATE Pasien
         SET tanggalLahir = :tanggalLahir,
             alamat = :alamat
         WHERE idPasien = :idPasien'
    );

    foreach (array_values($rows) as $index => $row) {
        if (!isset($profiles[$index])) {
            continue;
        }

        $profile = $profiles[$index];
        $updateUser->execute([
            ':id' => (int) $row['id'],
            ':nama' => $profile['name'],
            ':email' => buildBookingSeedPatientEmail($profile, $index),
            ':role' => 'pasien',
            ':gender' => $profile['gender'],
            ':dob' => $profile['dob'],
            ':phone' => '08' . str_pad((string) (821110000 + $index), 10, '0', STR_PAD_LEFT),
        ]);
        $updatePatient->execute([
            ':idPasien' => (int) $row['idpasien'],
            ':tanggalLahir' => $profile['dob'],
            ':alamat' => 'Profil pasien booking CareSync',
        ]);
    }
}

function ensureBookingSeedPatients(PDO $pdo, int $minimum = 30): array
{
    $profiles = getBookingSeedPatientProfiles();
    normalizeBookingSeedPatients($pdo, $profiles);
    $existingStmt = $pdo->query(
        "SELECT p.idPasien, u.email
         FROM Pasien p
         INNER JOIN users u ON u.id = p.id_user
         WHERE u.email LIKE '%@mail.caresync.id'
            OR COALESCE(p.alamat, '') = 'Profil pasien booking CareSync'
         ORDER BY u.email ASC"
    );

    $patientIds = [];
    foreach ($existingStmt->fetchAll() as $row) {
        $patientIds[] = (int) $row['idpasien'];
    }

    if (count($patientIds) >= $minimum) {
        return $patientIds;
    }

    $passwordHash = password_hash('Caresync123!', PASSWORD_DEFAULT);
    $insertUser = $pdo->prepare(
        "INSERT INTO users (nama, email, password, role, created_at, gender, dob, phone)
         VALUES (:nama, :email, :password, 'pasien', CURRENT_TIMESTAMP, :gender, :dob, :phone)
         RETURNING id"
    );
    $insertPatient = $pdo->prepare(
        'INSERT INTO Pasien (id_user, tanggalLahir, alamat)
         VALUES (:id_user, :tanggalLahir, :alamat)
         RETURNING idPasien'
    );

    for ($index = count($patientIds); $index < min($minimum, count($profiles)); $index++) {
        $profile = $profiles[$index];
        $email = buildBookingSeedPatientEmail($profile, $index);

        $insertUser->execute([
            ':nama' => $profile['name'],
            ':email' => $email,
            ':password' => $passwordHash,
            ':gender' => $profile['gender'],
            ':dob' => $profile['dob'],
            ':phone' => '08' . str_pad((string) (821110000 + $index), 10, '0', STR_PAD_LEFT),
        ]);
        $userId = (int) $insertUser->fetchColumn();

        $insertPatient->execute([
            ':id_user' => $userId,
            ':tanggalLahir' => $profile['dob'],
            ':alamat' => 'Profil pasien booking CareSync',
        ]);
        $patientIds[] = (int) $insertPatient->fetchColumn();
    }

    return $patientIds;
}

function ensureDoctorConsultationHistorySeed(PDO $pdo): void
{
    static $ensured = false;

    if ($ensured) {
        return;
    }

    ensureConsultationVideoColumns($pdo);
    $patientIds = ensureBookingSeedPatients($pdo, 30);
    $doctorIds = $pdo->query('SELECT idDokter FROM Dokter ORDER BY idDokter ASC')->fetchAll(PDO::FETCH_COLUMN) ?: [];

    if ($doctorIds === [] || $patientIds === []) {
        $ensured = true;
        return;
    }

    $countStmt = $pdo->prepare(
        'SELECT COUNT(*) FROM SesiKonsultasi WHERE idDokter = :idDokter'
    );
    $insertStmt = $pdo->prepare(
        "INSERT INTO SesiKonsultasi (
            idPasien, idDokter, tanggal, status, patient_rating, reviewed_at,
            video_call_provider, video_call_created_at, video_call_started_at, video_call_ended_at
        ) VALUES (
            :idPasien, :idDokter, :tanggal, 'Selesai', :patient_rating, :reviewed_at,
            'Jitsi Meet', :video_call_created_at, :video_call_started_at, :video_call_ended_at
        )"
    );

    foreach ($doctorIds as $doctorIdValue) {
        $doctorId = (int) $doctorIdValue;
        $countStmt->execute([':idDokter' => $doctorId]);
        $currentCount = (int) $countStmt->fetchColumn();
        $targetCount = 24 + (($doctorId * 3) % 11);

        if ($currentCount >= $targetCount) {
            continue;
        }

        $doctorOffset = max(0, $doctorId - 1);
        $poolSize = min(count($patientIds), 8 + (($doctorId * 2) % 9));
        $selectedPatients = [];
        for ($i = 0; $i < $poolSize; $i++) {
            $selectedPatients[] = $patientIds[($doctorOffset + $i) % count($patientIds)];
        }

        for ($index = $currentCount; $index < $targetCount; $index++) {
            $patientId = $selectedPatients[$index % count($selectedPatients)];
            $daysAgo = 14 + ($doctorId * 5) + ($index * 3);
            $hour = 8 + (($doctorId + $index) % 12);
            $minute = (($doctorId * 7) + ($index * 10)) % 60;
            $scheduledAt = (new DateTimeImmutable('now'))
                ->modify("-{$daysAgo} days")
                ->setTime($hour, $minute, 0);
            $endedAt = $scheduledAt->modify('+45 minutes');

            $insertStmt->execute([
                ':idPasien' => $patientId,
                ':idDokter' => $doctorId,
                ':tanggal' => $scheduledAt->format('Y-m-d H:i:s'),
                ':patient_rating' => 5,
                ':reviewed_at' => $endedAt->format('Y-m-d H:i:s'),
                ':video_call_created_at' => $scheduledAt->modify('-20 minutes')->format('Y-m-d H:i:s'),
                ':video_call_started_at' => $scheduledAt->format('Y-m-d H:i:s'),
                ':video_call_ended_at' => $endedAt->format('Y-m-d H:i:s'),
            ]);
        }
    }

    normalizeDoctorSeedRatings($pdo, $patientIds);

    $ensured = true;
}

function getDoctorTargetRatingBySeed(int $doctorId): float
{
    $targets = [4.8, 4.9, 5.0];
    return $targets[max(0, ($doctorId - 1) % count($targets))];
}

function buildSeedConsultationRatings(int $count, float $targetRating): array
{
    if ($count <= 0) {
        return [];
    }

    if ($targetRating >= 4.95) {
        return array_fill(0, $count, 5);
    }

    $fourCount = $targetRating >= 4.85
        ? max(1, (int) floor($count * 0.1))
        : max(1, (int) floor($count * 0.2));
    $fourCount = min($fourCount, $count);

    $ratings = array_fill(0, $count, 5);
    if ($fourCount === $count) {
        return $ratings;
    }

    for ($i = 0; $i < $fourCount; $i++) {
        $position = (int) floor(($i + 1) * $count / ($fourCount + 1));
        $position = max(0, min($count - 1, $position));
        $ratings[$position] = 4;
    }

    return $ratings;
}

function normalizeDoctorSeedRatings(PDO $pdo, array $seedPatientIds): void
{
    if ($seedPatientIds === []) {
        return;
    }

    $doctorIds = $pdo->query('SELECT idDokter FROM Dokter ORDER BY idDokter ASC')->fetchAll(PDO::FETCH_COLUMN) ?: [];
    $placeholders = implode(', ', array_fill(0, count($seedPatientIds), '?'));
    $consultationSql = sprintf(
        'SELECT idKonsultasi
         FROM SesiKonsultasi
         WHERE idDokter = ?
           AND idPasien IN (%s)
         ORDER BY tanggal ASC, idKonsultasi ASC',
        $placeholders
    );
    $consultationStmt = $pdo->prepare($consultationSql);
    $updateConsultationStmt = $pdo->prepare(
        'UPDATE SesiKonsultasi
         SET patient_rating = :patient_rating,
             reviewed_at = COALESCE(reviewed_at, tanggal + INTERVAL \'45 minutes\')
         WHERE idKonsultasi = :idKonsultasi'
    );
    $updateDoctorStmt = $pdo->prepare(
        'UPDATE Dokter SET rating = :rating WHERE idDokter = :idDokter'
    );

    foreach ($doctorIds as $doctorIdValue) {
        $doctorId = (int) $doctorIdValue;
        $consultationStmt->execute(array_merge([$doctorId], $seedPatientIds));
        $consultationIds = array_map('intval', $consultationStmt->fetchAll(PDO::FETCH_COLUMN));
        $ratings = buildSeedConsultationRatings(count($consultationIds), getDoctorTargetRatingBySeed($doctorId));

        foreach ($consultationIds as $index => $consultationId) {
            $updateConsultationStmt->execute([
                ':patient_rating' => $ratings[$index] ?? 5,
                ':idKonsultasi' => $consultationId,
            ]);
        }

        if ($ratings !== []) {
            $avgRating = array_sum($ratings) / count($ratings);
            $updateDoctorStmt->execute([
                ':rating' => round($avgRating, 2),
                ':idDokter' => $doctorId,
            ]);
        }
    }
}

function doctorHasAvailableScheduleToday(PDO $pdo, int $doctorId): bool
{
    ensureDoctorScheduleSeed($pdo, $doctorId);

    $now = new DateTimeImmutable('now');
    $dayOfWeek = (int) $now->format('w');
    $stmt = $pdo->prepare(
        'SELECT is_active, start_time, end_time
         FROM DokterJadwal
         WHERE idDokter = :idDokter AND day_of_week = :day_of_week
         LIMIT 1'
    );
    $stmt->execute([
        ':idDokter' => $doctorId,
        ':day_of_week' => $dayOfWeek,
    ]);
    $schedule = $stmt->fetch();

    if (!$schedule || !(bool) $schedule['is_active']) {
        return false;
    }

    $currentTime = $now->format('H:i:s');
    $startTime = substr((string) $schedule['start_time'], 0, 8);
    $endTime = substr((string) $schedule['end_time'], 0, 8);

    return $currentTime >= $startTime && $currentTime < $endTime;
}

function ensureDoctorBookingProfiles(PDO $pdo): void
{
    static $ensured = false;

    if ($ensured) {
        return;
    }

    ensureDoctorDirectorySchema($pdo);
    ensureDoctorConsultationHistorySeed($pdo);
    ensureDoctorPracticeCoverage($pdo);
    $stmt = $pdo->query(
        "SELECT d.idDokter, d.spesialisasi, d.consultation_fee, d.rating, u.id AS user_id, u.profile_photo,
                COALESCE(AVG(sk.patient_rating), 0) AS average_rating,
                COUNT(sk.patient_rating) AS total_reviews
         FROM Dokter d
         INNER JOIN users u ON u.id = d.id_user
         LEFT JOIN SesiKonsultasi sk ON sk.idDokter = d.idDokter
         GROUP BY d.idDokter, d.spesialisasi, d.consultation_fee, d.rating, u.id, u.profile_photo
         ORDER BY d.idDokter ASC"
    );
    $rows = $stmt->fetchAll();
    $updateDoctorMeta = $pdo->prepare(
        'UPDATE Dokter
         SET consultation_fee = COALESCE(consultation_fee, :consultation_fee),
             rating = :rating
         WHERE idDokter = :idDokter'
    );

    foreach ($rows as $row) {
        $specialization = (string) ($row['spesialisasi'] ?? 'Dokter Umum');
        $fee = (int) ($row['consultation_fee'] ?? 0);
        if ($fee <= 0) {
            $fee = getDefaultConsultationFeeBySpecialization($specialization);
        }

        $reviewCount = (int) ($row['total_reviews'] ?? 0);
        $rating = $reviewCount > 0
            ? round((float) ($row['average_rating'] ?? 0), 2)
            : max(0, round((float) ($row['rating'] ?? 0), 2));

        $updateDoctorMeta->execute([
            ':consultation_fee' => $fee,
            ':rating' => $rating,
            ':idDokter' => (int) $row['iddokter'],
        ]);
    }

    $ensured = true;
}

function getDoctorCategoryIcon(string $specialization): string
{
    $normalized = strtolower(trim($specialization));

    if (str_contains($normalized, 'anak')) {
        return 'fa-baby';
    }
    if (str_contains($normalized, 'kulit') || str_contains($normalized, 'kelamin')) {
        return 'fa-hand-dots';
    }
    if (str_contains($normalized, 'mata')) {
        return 'fa-eye';
    }
    if (str_contains($normalized, 'gigi')) {
        return 'fa-tooth';
    }
    if (str_contains($normalized, 'tht')) {
        return 'fa-ear-listen';
    }
    if (str_contains($normalized, 'gizi')) {
        return 'fa-apple-whole';
    }
    if (str_contains($normalized, 'psiki') || str_contains($normalized, 'brain')) {
        return 'fa-brain';
    }
    if (str_contains($normalized, 'dalam') || str_contains($normalized, 'paru')) {
        return 'fa-lungs';
    }

    return 'fa-stethoscope';
}

function getBookingDoctors(PDO $pdo): array
{
    ensureDoctorBookingProfiles($pdo);

    $stmt = $pdo->query(
        "SELECT
            d.idDokter,
            d.spesialisasi,
            d.nomorSTR,
            d.consultation_fee,
            d.rating,
            u.id AS id_user,
            u.nama,
            u.email,
            u.profile_photo,
            COUNT(CASE WHEN sk.status = 'Selesai' THEN 1 END) AS total_consultations,
            COUNT(DISTINCT CASE WHEN sk.status = 'Selesai' THEN sk.idPasien END) AS total_patients
         FROM Dokter d
         INNER JOIN users u ON u.id = d.id_user
         LEFT JOIN SesiKonsultasi sk ON sk.idDokter = d.idDokter
         GROUP BY d.idDokter, d.spesialisasi, d.nomorSTR, d.consultation_fee, d.rating, u.id, u.nama, u.email, u.profile_photo
         ORDER BY d.spesialisasi ASC, u.nama ASC
        "
    );
    $doctors = [];

    foreach ($stmt->fetchAll() as $row) {
        $specialization = $row['spesialisasi'] ?: 'Dokter Umum';
        $totalConsultations = (int) ($row['total_consultations'] ?? 0);

        $doctors[] = [
            'id' => (int) $row['iddokter'],
            'user_id' => (int) $row['id_user'],
            'name' => $row['nama'],
            'email' => $row['email'],
            'specialization' => $specialization,
            'license' => $row['nomorstr'] ?: '-',
            'image' => normalizeDoctorPhotoUrl((string) ($row['profile_photo'] ?? '')),
            'rating' => number_format((float) ($row['rating'] ?? 0), 1, '.', ''),
            'patients' => formatDoctorPatientCount($totalConsultations),
            'patient_count' => $totalConsultations,
            'fee' => max(0, (int) ($row['consultation_fee'] ?? 0)),
            'online' => doctorHasAvailableScheduleToday($pdo, (int) $row['iddokter']),
            'total_consultations' => $totalConsultations,
            'category_icon' => getDoctorCategoryIcon($specialization),
        ];
    }

    return $doctors;
}

function getBookingSpecializations(array $doctors): array
{
    $specializations = [];

    foreach ($doctors as $doctor) {
        $specialization = $doctor['specialization'];
        if (isset($specializations[$specialization])) {
            continue;
        }

        $specializations[$specialization] = [
            'name' => $specialization,
            'icon' => $doctor['category_icon'],
        ];
    }

    ksort($specializations);

    return array_values($specializations);
}

function getBookingDateOptions(int $days = 7): array
{
    $options = [];
    $today = new DateTimeImmutable('today');

    for ($i = 0; $i < $days; $i++) {
        $date = $today->modify("+{$i} day");
        $options[] = [
            'value' => $date->format('Y-m-d'),
            'label' => $date->format('D, d M Y'),
        ];
    }

    return $options;
}

function getDailyBookingSlots(): array
{
    return ['09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '19:00', '20:00'];
}

function getAvailableBookingSlots(PDO $pdo, int $doctorId, string $date): array
{
    ensureConsultationVideoColumns($pdo);
    syncConsultationStatuses($pdo);

    $selectedDate = DateTimeImmutable::createFromFormat('Y-m-d', $date);
    if (!$selectedDate) {
        return [];
    }

    $slots = getDoctorDailySlots($pdo, $doctorId, $date);
    if (!$slots) {
        return [];
    }
    $stmt = $pdo->prepare(
        "SELECT TO_CHAR(tanggal, 'HH24:MI') AS jam
         FROM SesiKonsultasi
         WHERE idDokter = :idDokter
           AND DATE(tanggal) = :tanggal
           AND status IN ('Menunggu', 'Berjalan')"
    );
    $stmt->execute([
        ':idDokter' => $doctorId,
        ':tanggal' => $date,
    ]);

    $booked = [];
    foreach ($stmt->fetchAll() as $row) {
        $booked[] = $row['jam'];
    }

    $now = new DateTimeImmutable();
    $available = [];

    foreach ($slots as $slot) {
        if (in_array($slot, $booked, true)) {
            continue;
        }

        $slotDateTime = DateTimeImmutable::createFromFormat('Y-m-d H:i', $date . ' ' . $slot);
        if (!$slotDateTime) {
            continue;
        }

        if ($slotDateTime <= $now) {
            continue;
        }

        $available[] = [
            'value' => $slot,
            'label' => $slot,
        ];
    }

    return $available;
}

function syncConsultationStatuses(PDO $pdo): void
{
    ensureConsultationVideoColumns($pdo);

    $pdo->exec(
        "UPDATE SesiKonsultasi
         SET status = 'Selesai'
         WHERE status IN ('Menunggu', 'Berjalan')
           AND tanggal <= NOW() - INTERVAL '1 hour'"
    );

}

function ensurePatientProfile(PDO $pdo, int $userId): int
{
    $find = $pdo->prepare('SELECT idPasien FROM Pasien WHERE id_user = :id_user');
    $find->execute([':id_user' => $userId]);
    $patientId = $find->fetchColumn();

    if ($patientId) {
        return (int) $patientId;
    }

    $userStmt = $pdo->prepare('SELECT dob FROM users WHERE id = :id');
    $userStmt->execute([':id' => $userId]);
    $user = $userStmt->fetch();

    $insert = $pdo->prepare(
        'INSERT INTO Pasien (id_user, tanggalLahir, alamat) VALUES (:id_user, :tanggalLahir, NULL) RETURNING idPasien'
    );
    $insert->execute([
        ':id_user' => $userId,
        ':tanggalLahir' => $user['dob'] ?? null,
    ]);

    return (int) $insert->fetchColumn();
}

function findBookingDoctor(PDO $pdo, int $doctorId): ?array
{
    foreach (getBookingDoctors($pdo) as $doctor) {
        if ($doctor['id'] === $doctorId) {
            return $doctor;
        }
    }

    return null;
}

function getConsultationAccentColors(): array
{
    return ['#1D4ED8', '#10B981', '#F59E0B', '#8B5CF6', '#F43F5E', '#0EA5E9'];
}

function getConsultationInitials(string $name, string $fallback = 'CS'): string
{
    $letters = preg_replace('/[^A-Za-z]/', '', $name);
    if (!$letters) {
        return strtoupper($fallback);
    }

    return strtoupper(substr($letters, 0, 2));
}

function buildConsultationSummary(PDO $pdo, array $row, int $index, int $viewerUserId): array
{
    $viewerIsDoctor = (int) ($row['doctor_user_id'] ?? 0) === $viewerUserId;
    $displayName = $viewerIsDoctor
        ? ($row['patient_name'] ?: 'Pasien CareSync')
        : ($row['doctor_name'] ?: 'Dokter CareSync');
    $displaySubtitle = $viewerIsDoctor
        ? 'Pasien'
        : ($row['specialization'] ?: 'Dokter Umum');
    $scheduledAt = $row['scheduled_at'] ?? null;
    $preview = trim((string) ($row['last_message'] ?? ''));

    if ($preview === '') {
        $preview = $viewerIsDoctor
            ? 'Belum ada pesan dari pasien di sesi ini.'
            : 'Dokter akan membalas konsultasi Anda melalui chat.';
    }

    $colors = getConsultationAccentColors();
    $colorIndex = max(0, ((int) ($row['consultation_id'] ?? ($index + 1))) - 1) % count($colors);
    $diagnosisDescription = trim((string) ($row['diagnosis_description'] ?? ''));
    $patientRating = isset($row['patient_rating']) ? (int) $row['patient_rating'] : 0;
    $status = (string) ($row['status'] ?? 'Menunggu');
    $chatExpired = isConsultationChatExpired($row);
    $chatNotice = getConsultationChatNotice($row, $chatExpired);
    $chatExpiresAt = getConsultationChatExpiryAt($row);

    if ($chatExpired) {
        $preview = $chatNotice;
    }

    return [
        'id' => (int) $row['consultation_id'],
        'status' => $status,
        'scheduled_at' => $scheduledAt,
        'scheduled_label' => $scheduledAt ? date('d M Y H:i', strtotime($scheduledAt)) : '-',
        'display_name' => $displayName,
        'display_subtitle' => $displaySubtitle,
        'doctor_name' => $row['doctor_name'] ?: 'Dokter CareSync',
        'doctor_user_id' => (int) ($row['doctor_user_id'] ?? 0),
        'doctor_email' => $row['doctor_email'] ?? '',
        'doctor_id' => (int) ($row['doctor_id'] ?? 0),
        'patient_id' => (int) ($row['patient_id'] ?? 0),
        'patient_name' => $row['patient_name'] ?: 'Pasien CareSync',
        'patient_user_id' => (int) ($row['patient_user_id'] ?? 0),
        'specialization' => $row['specialization'] ?: 'Dokter Umum',
        'license' => $row['license'] ?: '-',
        'preview' => $preview,
        'last_message_at' => $row['last_message_at'] ?? $scheduledAt,
        'last_message_label' => !empty($row['last_message_at'])
            ? date('d M H:i', strtotime($row['last_message_at']))
            : ($scheduledAt ? date('d M H:i', strtotime($scheduledAt)) : '-'),
        'initials' => getConsultationInitials($displayName, $viewerIsDoctor ? 'PS' : 'DR'),
        'avatar_color' => $colors[$colorIndex],
        'rating' => number_format((float) ($row['doctor_rating'] ?? 0), 1, '.', ''),
        'patients' => formatDoctorPatientCount((int) ($row['doctor_total_patients'] ?? 0)),
        'patient_count' => (int) ($row['doctor_total_patients'] ?? 0),
        'fee' => max(0, (int) ($row['doctor_fee'] ?? 0)),
        'image' => normalizeDoctorPhotoUrl((string) ($row['doctor_photo'] ?? '')),
        'online' => doctorHasAvailableScheduleToday($pdo, (int) ($row['doctor_id'] ?? 0)),
        'diagnosis_description' => $diagnosisDescription,
        'has_diagnosis' => $diagnosisDescription !== '',
        'patient_rating' => $patientRating > 0 ? $patientRating : null,
        'can_review' => !$viewerIsDoctor && $status === 'Selesai' && $diagnosisDescription !== '' && $patientRating <= 0,
        'video_call_room' => trim((string) ($row['jitsi_room_name'] ?? '')),
        'video_call_url' => trim((string) ($row['jitsi_room_url'] ?? '')),
        'video_call_provider' => trim((string) ($row['video_call_provider'] ?? 'Jitsi Meet')) ?: 'Jitsi Meet',
        'video_call_started_at' => (string) ($row['video_call_started_at'] ?? ''),
        'video_call_ended_at' => (string) ($row['video_call_ended_at'] ?? ''),
        'video_call_finished' => !empty($row['video_call_ended_at']),
        'chat_expired' => $chatExpired,
        'chat_retention_notice' => $chatNotice,
        'chat_expires_at' => $chatExpiresAt ? $chatExpiresAt->format('Y-m-d H:i:s') : '',
        'viewer_role' => $viewerIsDoctor ? 'doctor' : 'patient',
    ];
}

function getUserConsultations(PDO $pdo, int $userId): array
{
    ensureDoctorBookingProfiles($pdo);
    ensureConsultationVideoColumns($pdo);
    syncConsultationStatuses($pdo);
    purgeExpiredConsultationMessages($pdo);

    $stmt = $pdo->prepare(
        "SELECT
            sk.idKonsultasi AS consultation_id,
            sk.tanggal AS scheduled_at,
            sk.status::text AS status,
            sk.jitsi_room_name,
            sk.jitsi_room_url,
            sk.video_call_provider,
            sk.video_call_started_at,
            sk.video_call_ended_at,
            sk.patient_rating,
            d.idDokter AS doctor_id,
            d.spesialisasi AS specialization,
            d.nomorSTR AS license,
            d.consultation_fee AS doctor_fee,
            d.rating AS doctor_rating,
            du.id AS doctor_user_id,
            du.nama AS doctor_name,
            du.email AS doctor_email,
            du.profile_photo AS doctor_photo,
            p.idPasien AS patient_id,
            pu.id AS patient_user_id,
            pu.nama AS patient_name,
            (
                SELECT COUNT(DISTINCT sk2.idPasien)
                FROM SesiKonsultasi sk2
                WHERE sk2.idDokter = d.idDokter
            ) AS doctor_total_patients,
            (
                SELECT dg.deskripsi
                FROM Diagnosis dg
                WHERE dg.idKonsultasi = sk.idKonsultasi
                ORDER BY dg.idDiagnosis DESC
                LIMIT 1
            ) AS diagnosis_description,
            (
                SELECT pc.isiPesan
                FROM PesanChat pc
                WHERE pc.idKonsultasi = sk.idKonsultasi
                ORDER BY pc.waktu DESC, pc.idPesan DESC
                LIMIT 1
            ) AS last_message,
            (
                SELECT pc.waktu
                FROM PesanChat pc
                WHERE pc.idKonsultasi = sk.idKonsultasi
                ORDER BY pc.waktu DESC, pc.idPesan DESC
                LIMIT 1
            ) AS last_message_at
         FROM SesiKonsultasi sk
         INNER JOIN Dokter d ON d.idDokter = sk.idDokter
         INNER JOIN users du ON du.id = d.id_user
         INNER JOIN Pasien p ON p.idPasien = sk.idPasien
         INNER JOIN users pu ON pu.id = p.id_user
         WHERE du.id = :user_id OR pu.id = :user_id
         ORDER BY
            CASE sk.status::text
                WHEN 'Berjalan' THEN 0
                WHEN 'Menunggu' THEN 1
                ELSE 2
            END,
            COALESCE((
                SELECT pc.waktu
                FROM PesanChat pc
                WHERE pc.idKonsultasi = sk.idKonsultasi
                ORDER BY pc.waktu DESC, pc.idPesan DESC
                LIMIT 1
            ), sk.tanggal) DESC"
    );
    $stmt->execute([':user_id' => $userId]);

    $consultations = [];

    foreach ($stmt->fetchAll() as $index => $row) {
        $videoCall = ensureConsultationVideoCall($pdo, $row);
        $row['jitsi_room_name'] = $videoCall['room_name'];
        $row['jitsi_room_url'] = $videoCall['room_url'];
        $row['video_call_provider'] = $videoCall['provider'];
        $consultations[] = buildConsultationSummary($pdo, $row, $index, $userId);
    }

    return $consultations;
}

function getPatientPrescriptions(PDO $pdo, int $userId): array
{
    $patientId = ensurePatientProfile($pdo, $userId);

    $stmt = $pdo->prepare(
        "SELECT
            r.idResep,
            r.idPemesanan,
            r.catatan,
            r.tanggalPembuatan,
            sk.idKonsultasi,
            sk.tanggal AS consultation_date,
            sk.status AS consultation_status,
            d.spesialisasi,
            u.nama AS doctor_name,
            dg.hasil AS diagnosis
         FROM Resep r
         INNER JOIN SesiKonsultasi sk ON sk.idKonsultasi = r.idKonsultasi
         INNER JOIN Dokter d ON d.idDokter = sk.idDokter
         INNER JOIN users u ON u.id = d.id_user
         LEFT JOIN Diagnosis dg ON dg.idKonsultasi = sk.idKonsultasi
         WHERE sk.idPasien = :idPasien
         ORDER BY COALESCE(r.tanggalPembuatan, sk.tanggal) DESC, r.idResep DESC"
    );
    $stmt->execute([':idPasien' => $patientId]);

    $itemStmt = $pdo->prepare(
        "SELECT
            ir.idItemResep,
            ir.jumlah,
            ir.aturanPakai,
            o.idObat,
            o.nama,
            o.harga,
            COALESCE(o.dosis, '') AS dosis,
            COALESCE(o.stok, 0) AS stok
         FROM ItemResep ir
         INNER JOIN Obat o ON o.idObat = ir.idObat
         WHERE ir.idResep = :idResep
         ORDER BY ir.idItemResep ASC"
    );

    $prescriptions = [];

    foreach ($stmt->fetchAll() as $row) {
        $itemStmt->execute([':idResep' => $row['idresep']]);
        $items = [];
        $subtotal = 0;

        foreach ($itemStmt->fetchAll() as $item) {
            $itemSubtotal = (int) $item['harga'] * (int) $item['jumlah'];
            $subtotal += $itemSubtotal;
            $items[] = [
                'id' => (int) $item['iditemresep'],
                'medicine_id' => (int) $item['idobat'],
                'name' => (string) $item['nama'],
                'price' => (int) $item['harga'],
                'qty' => (int) $item['jumlah'],
                'stock' => (int) $item['stok'],
                'dose' => (string) $item['dosis'],
                'instruction' => (string) $item['aturanpakai'],
                'subtotal' => $itemSubtotal,
            ];
        }

        $orderStatus = 'Belum ditebus';
        $statusTone = 'amber';

        if (!empty($row['idpemesanan'])) {
            $orderStatus = 'Pesanan apotek dibuat';
            $statusTone = 'blue';
        }

        $prescriptions[] = [
            'id' => (int) $row['idresep'],
            'code' => sprintf('RSP-%s-%03d', date('Ymd', strtotime((string) ($row['tanggalpembuatan'] ?: $row['consultation_date']))), (int) $row['idresep']),
            'consultation_id' => (int) $row['idkonsultasi'],
            'consultation_date' => (string) ($row['consultation_date'] ?? ''),
            'issued_at' => (string) ($row['tanggalpembuatan'] ?? $row['consultation_date'] ?? ''),
            'issued_label' => !empty($row['tanggalpembuatan']) || !empty($row['consultation_date'])
                ? date('d F Y, H:i', strtotime((string) ($row['tanggalpembuatan'] ?? $row['consultation_date']))) . ' WIB'
                : '-',
            'consultation_status' => (string) ($row['consultation_status'] ?? 'Menunggu'),
            'doctor_name' => (string) ($row['doctor_name'] ?? 'Dokter CareSync'),
            'specialization' => (string) ($row['spesialisasi'] ?? 'Dokter Umum'),
            'diagnosis' => trim((string) ($row['diagnosis'] ?? 'Belum ada diagnosis tertulis.')),
            'notes' => trim((string) ($row['catatan'] ?? '')),
            'order_id' => (int) ($row['idpemesanan'] ?? 0),
            'status_label' => $orderStatus,
            'status_tone' => $statusTone,
            'subtotal' => $subtotal,
            'items' => $items,
        ];
    }

    return $prescriptions;
}

function getPatientPrescriptionDetail(PDO $pdo, int $userId, int $prescriptionId): ?array
{
    foreach (getPatientPrescriptions($pdo, $userId) as $prescription) {
        if ((int) $prescription['id'] === $prescriptionId) {
            return $prescription;
        }
    }

    return null;
}

function findAccessibleConsultation(PDO $pdo, int $consultationId, int $userId): ?array
{
    ensureDoctorBookingProfiles($pdo);
    ensureConsultationVideoColumns($pdo);
    syncConsultationStatuses($pdo);
    purgeExpiredConsultationMessages($pdo, $consultationId);

    $stmt = $pdo->prepare(
        "SELECT
            sk.idKonsultasi AS consultation_id,
            sk.tanggal AS scheduled_at,
            sk.status::text AS status,
            sk.jitsi_room_name,
            sk.jitsi_room_url,
            sk.video_call_provider,
            sk.video_call_started_at,
            sk.video_call_ended_at,
            sk.patient_rating,
            d.idDokter AS doctor_id,
            d.spesialisasi AS specialization,
            d.nomorSTR AS license,
            d.consultation_fee AS doctor_fee,
            d.rating AS doctor_rating,
            du.id AS doctor_user_id,
            du.nama AS doctor_name,
            du.email AS doctor_email,
            du.profile_photo AS doctor_photo,
            p.idPasien AS patient_id,
            pu.id AS patient_user_id,
            pu.nama AS patient_name,
            (
                SELECT COUNT(DISTINCT sk2.idPasien)
                FROM SesiKonsultasi sk2
                WHERE sk2.idDokter = d.idDokter
            ) AS doctor_total_patients,
            (
                SELECT dg.deskripsi
                FROM Diagnosis dg
                WHERE dg.idKonsultasi = sk.idKonsultasi
                ORDER BY dg.idDiagnosis DESC
                LIMIT 1
            ) AS diagnosis_description,
            (
                SELECT pc.isiPesan
                FROM PesanChat pc
                WHERE pc.idKonsultasi = sk.idKonsultasi
                ORDER BY pc.waktu DESC, pc.idPesan DESC
                LIMIT 1
            ) AS last_message,
            (
                SELECT pc.waktu
                FROM PesanChat pc
                WHERE pc.idKonsultasi = sk.idKonsultasi
                ORDER BY pc.waktu DESC, pc.idPesan DESC
                LIMIT 1
            ) AS last_message_at
         FROM SesiKonsultasi sk
         INNER JOIN Dokter d ON d.idDokter = sk.idDokter
         INNER JOIN users du ON du.id = d.id_user
         INNER JOIN Pasien p ON p.idPasien = sk.idPasien
         INNER JOIN users pu ON pu.id = p.id_user
         WHERE sk.idKonsultasi = :consultation_id
           AND (du.id = :user_id OR pu.id = :user_id)
         LIMIT 1"
    );
    $stmt->execute([
        ':consultation_id' => $consultationId,
        ':user_id' => $userId,
    ]);

    $row = $stmt->fetch();
    if (!$row) {
        return null;
    }

    $videoCall = ensureConsultationVideoCall($pdo, $row);
    $row['jitsi_room_name'] = $videoCall['room_name'];
    $row['jitsi_room_url'] = $videoCall['room_url'];
    $row['video_call_provider'] = $videoCall['provider'];

    return buildConsultationSummary($pdo, $row, 0, $userId);
}

function ensureConsultationIntroMessage(PDO $pdo, array $consultation): void
{
    ensureConsultationVideoColumns($pdo);

    $consultationId = (int) ($consultation['id'] ?? $consultation['consultation_id'] ?? 0);
    $doctorUserId = (int) ($consultation['doctor_user_id'] ?? 0);

    if ($consultationId <= 0 || $doctorUserId <= 0) {
        return;
    }

    $countStmt = $pdo->prepare('SELECT COUNT(*) FROM PesanChat WHERE idKonsultasi = :idKonsultasi');
    $countStmt->execute([':idKonsultasi' => $consultationId]);

    if ((int) $countStmt->fetchColumn() > 0) {
        return;
    }

    $videoCall = ensureConsultationVideoCall($pdo, $consultation);
    $message = sprintf(
        "Halo, saya %s. Silakan sampaikan keluhan utama Anda untuk konsultasi %s ini, nanti saya bantu tindak lanjuti.\n\nLink video call %s: %s\nNama room: %s",
        $consultation['doctor_name'] ?? 'dokter CareSync',
        strtolower($consultation['specialization'] ?? 'dokter umum'),
        $videoCall['provider'] ?? 'Jitsi Meet',
        $videoCall['room_url'] !== '' ? $videoCall['room_url'] : '-',
        $videoCall['room_name'] !== '' ? $videoCall['room_name'] : '-'
    );

    $insert = $pdo->prepare(
        'INSERT INTO PesanChat (idKonsultasi, idPengirim, isiPesan, waktu)
         VALUES (:idKonsultasi, :idPengirim, :isiPesan, CURRENT_TIMESTAMP)'
    );
    $insert->execute([
        ':idKonsultasi' => $consultationId,
        ':idPengirim' => $doctorUserId,
        ':isiPesan' => $message,
    ]);
}

function ensureConsultationLifecycleMessage(PDO $pdo, int $consultationId, int $senderUserId, string $message): void
{
    $normalizedMessage = trim($message);
    if ($consultationId <= 0 || $senderUserId <= 0 || $normalizedMessage === '') {
        return;
    }

    $existingStmt = $pdo->prepare(
        'SELECT idPesan
         FROM PesanChat
         WHERE idKonsultasi = :idKonsultasi
           AND idPengirim = :idPengirim
           AND isiPesan = :isiPesan
         LIMIT 1'
    );
    $existingStmt->execute([
        ':idKonsultasi' => $consultationId,
        ':idPengirim' => $senderUserId,
        ':isiPesan' => $normalizedMessage,
    ]);

    if ($existingStmt->fetchColumn()) {
        return;
    }

    $insertStmt = $pdo->prepare(
        'INSERT INTO PesanChat (idKonsultasi, idPengirim, isiPesan, waktu)
         VALUES (:idKonsultasi, :idPengirim, :isiPesan, CURRENT_TIMESTAMP)'
    );
    $insertStmt->execute([
        ':idKonsultasi' => $consultationId,
        ':idPengirim' => $senderUserId,
        ':isiPesan' => $normalizedMessage,
    ]);
}

function markConsultationAsStarted(PDO $pdo, int $consultationId): void
{
    ensureConsultationVideoColumns($pdo);

    $stmt = $pdo->prepare(
        "UPDATE SesiKonsultasi
         SET status = 'Berjalan',
             video_call_started_at = COALESCE(video_call_started_at, CURRENT_TIMESTAMP),
             video_call_ended_at = NULL
         WHERE idKonsultasi = :idKonsultasi
           AND status = 'Menunggu'"
    );
    $stmt->execute([
        ':idKonsultasi' => $consultationId,
    ]);
}

function markConsultationVideoEnded(PDO $pdo, int $consultationId): void
{
    ensureConsultationVideoColumns($pdo);

    $stmt = $pdo->prepare(
        "UPDATE SesiKonsultasi
         SET video_call_ended_at = COALESCE(video_call_ended_at, CURRENT_TIMESTAMP)
         WHERE idKonsultasi = :idKonsultasi"
    );
    $stmt->execute([
        ':idKonsultasi' => $consultationId,
    ]);
}

function saveConsultationRating(PDO $pdo, int $consultationId, int $patientUserId, int $rating): array
{
    ensureConsultationVideoColumns($pdo);

    if ($rating < 1 || $rating > 5) {
        throw new RuntimeException('Rating harus antara 1 sampai 5 bintang.');
    }

    $consultation = findAccessibleConsultation($pdo, $consultationId, $patientUserId);
    if (!$consultation || ($consultation['viewer_role'] ?? '') !== 'patient') {
        throw new RuntimeException('Sesi konsultasi tidak ditemukan untuk pasien ini.');
    }

    if (($consultation['status'] ?? '') !== 'Selesai' || empty($consultation['has_diagnosis'])) {
        throw new RuntimeException('Review hanya bisa diberikan setelah diagnosis selesai diterbitkan.');
    }

    if (!empty($consultation['patient_rating'])) {
        throw new RuntimeException('Review untuk sesi ini sudah pernah dikirim.');
    }

    $pdo->beginTransaction();
    try {
        $updateConsultation = $pdo->prepare(
            'UPDATE SesiKonsultasi
             SET patient_rating = :patient_rating, reviewed_at = CURRENT_TIMESTAMP
             WHERE idKonsultasi = :idKonsultasi'
        );
        $updateConsultation->execute([
            ':patient_rating' => $rating,
            ':idKonsultasi' => $consultationId,
        ]);

        $avgStmt = $pdo->prepare(
            "SELECT AVG(patient_rating)::numeric(3,2) AS avg_rating
             FROM SesiKonsultasi
             WHERE idDokter = :idDokter
               AND patient_rating IS NOT NULL"
        );
        $avgStmt->execute([':idDokter' => (int) ($consultation['doctor_id'] ?? 0)]);
        $avgRating = (float) ($avgStmt->fetchColumn() ?: $rating);

        $updateDoctor = $pdo->prepare(
            'UPDATE Dokter SET rating = :rating WHERE idDokter = :idDokter'
        );
        $updateDoctor->execute([
            ':rating' => $avgRating,
            ':idDokter' => (int) ($consultation['doctor_id'] ?? 0),
        ]);

        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }

    return findAccessibleConsultation($pdo, $consultationId, $patientUserId) ?? $consultation;
}

function isConsultationClosed(array $consultation): bool
{
    return strtolower((string) ($consultation['status'] ?? '')) === 'selesai';
}

function getConsultationChatExpiryAt(array $consultation): ?DateTimeImmutable
{
    $status = strtolower(trim((string) ($consultation['status'] ?? '')));
    if ($status !== 'selesai') {
        return null;
    }

    $baseTime = trim((string) ($consultation['video_call_ended_at'] ?? $consultation['scheduled_at'] ?? $consultation['tanggal'] ?? ''));
    if ($baseTime === '') {
        return null;
    }

    try {
        return (new DateTimeImmutable($baseTime))->modify('+24 hours');
    } catch (Throwable $e) {
        return null;
    }
}

function isConsultationChatExpired(array $consultation): bool
{
    $expiryAt = getConsultationChatExpiryAt($consultation);
    if (!$expiryAt) {
        return false;
    }

    return new DateTimeImmutable('now') >= $expiryAt;
}

function getConsultationChatNotice(array $consultation, bool $expired = false): string
{
    if ($expired) {
        return 'Riwayat chat konsultasi ini sudah dihapus otomatis setelah 24 jam sejak sesi selesai.';
    }

    if (!getConsultationChatExpiryAt($consultation)) {
        return 'Riwayat chat tersedia selama konsultasi berlangsung.';
    }

    return 'Riwayat chat akan dihapus otomatis 24 jam setelah sesi konsultasi selesai.';
}

function purgeExpiredConsultationMessages(PDO $pdo, ?int $consultationId = null): void
{
    $params = [];
    $sql = "DELETE FROM PesanChat pc
            USING SesiKonsultasi sk
            WHERE pc.idKonsultasi = sk.idKonsultasi
              AND sk.status = 'Selesai'
              AND COALESCE(sk.video_call_ended_at, sk.tanggal) <= NOW() - INTERVAL '24 hours'";

    if ($consultationId !== null && $consultationId > 0) {
        $sql .= ' AND sk.idKonsultasi = :idKonsultasi';
        $params[':idKonsultasi'] = $consultationId;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
}

function getConsultationMessages(PDO $pdo, int $consultationId, int $afterMessageId = 0): array
{
    purgeExpiredConsultationMessages($pdo, $consultationId);

    $stmt = $pdo->prepare(
        "SELECT
            pc.idPesan AS message_id,
            pc.idPengirim AS sender_id,
            pc.isiPesan AS body,
            pc.waktu AS sent_at,
            u.nama AS sender_name,
            u.role AS sender_role
         FROM PesanChat pc
         LEFT JOIN users u ON u.id = pc.idPengirim
         WHERE pc.idKonsultasi = :idKonsultasi
           AND pc.idPesan > :after_id
         ORDER BY pc.waktu ASC, pc.idPesan ASC"
    );
    $stmt->execute([
        ':idKonsultasi' => $consultationId,
        ':after_id' => max(0, $afterMessageId),
    ]);

    $messages = [];
    foreach ($stmt->fetchAll() as $row) {
        $messages[] = [
            'id' => (int) $row['message_id'],
            'sender_id' => (int) $row['sender_id'],
            'sender_name' => $row['sender_name'] ?: 'CareSync',
            'sender_role' => $row['sender_role'] ?: '',
            'body' => $row['body'] ?? '',
            'time' => date('H:i', strtotime($row['sent_at'] ?? 'now')),
            'sent_at' => $row['sent_at'],
        ];
    }

    return $messages;
}
