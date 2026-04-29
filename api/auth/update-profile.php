<?php
require_once '../../includes/config.php';
require_once '../../includes/marketplace_helpers.php';

if (!isLoggedIn()) {
    jsonResponse(['status' => 'error', 'message' => 'Sesi berakhir, silakan login kembali.'], 401);
}

$data = jsonInput();
$loggedInUser = currentUser();
$userId = (int) ($loggedInUser['id'] ?? 0);

if ($userId <= 0) {
    jsonResponse(['status' => 'error', 'message' => 'Token tidak valid, silakan login kembali.'], 401);
}

try {
    ensureMarketplacePatientSchema($pdo);
    $profileStmt = $pdo->prepare(
        "SELECT
            u.nama,
            u.phone,
            u.dob,
            u.gender,
            p.alamat,
            p.blood_type,
            p.weight,
            p.height,
            p.allergy_notes,
            p.address_label,
            p.recipient_name,
            p.village,
            p.district,
            p.city,
            p.province,
            p.postal_code,
            p.address_notes
         FROM users u
         LEFT JOIN Pasien p ON p.id_user = u.id
         WHERE u.id = :id
         LIMIT 1"
    );
    $profileStmt->execute([':id' => $userId]);
    $currentProfile = $profileStmt->fetch() ?: [];

    $hasKey = static fn(string $key): bool => array_key_exists($key, $data);

    $nama = $hasKey('nama')
        ? sanitizeText($data['nama'] ?? '', 100)
        : sanitizeText((string) ($currentProfile['nama'] ?? ''), 100);
    $phone = $hasKey('phone')
        ? preg_replace('/[^\d+]/', '', (string) ($data['phone'] ?? ''))
        : preg_replace('/[^\d+]/', '', (string) ($currentProfile['phone'] ?? ''));
    $dob = $hasKey('dob')
        ? trim((string) ($data['dob'] ?? ''))
        : trim((string) ($currentProfile['dob'] ?? ''));
    $gender = $hasKey('gender')
        ? sanitizeText($data['gender'] ?? '', 20)
        : sanitizeText((string) ($currentProfile['gender'] ?? ''), 20);
    $alamat = $hasKey('alamat')
        ? sanitizeText($data['alamat'] ?? '', 500)
        : sanitizeText((string) ($currentProfile['alamat'] ?? ''), 500);
    $addressLabel = $hasKey('address_label')
        ? sanitizeText($data['address_label'] ?? '', 50)
        : sanitizeText((string) ($currentProfile['address_label'] ?? ''), 50);
    $recipientName = $hasKey('recipient_name')
        ? sanitizeText($data['recipient_name'] ?? '', 100)
        : sanitizeText((string) ($currentProfile['recipient_name'] ?? ''), 100);
    $village = $hasKey('village')
        ? sanitizeText($data['village'] ?? '', 100)
        : sanitizeText((string) ($currentProfile['village'] ?? ''), 100);
    $district = $hasKey('district')
        ? sanitizeText($data['district'] ?? '', 100)
        : sanitizeText((string) ($currentProfile['district'] ?? ''), 100);
    $city = $hasKey('city')
        ? sanitizeText($data['city'] ?? '', 100)
        : sanitizeText((string) ($currentProfile['city'] ?? ''), 100);
    $province = $hasKey('province')
        ? sanitizeText($data['province'] ?? '', 100)
        : sanitizeText((string) ($currentProfile['province'] ?? ''), 100);
    $postalCode = preg_replace('/[^\d-]/', '', (string) ($currentProfile['postal_code'] ?? ''));
    $addressNotes = $hasKey('address_notes')
        ? sanitizeText($data['address_notes'] ?? '', 500)
        : sanitizeText((string) ($currentProfile['address_notes'] ?? ''), 500);
    $bloodType = $hasKey('blood_type')
        ? sanitizeText($data['blood_type'] ?? '', 5)
        : sanitizeText((string) ($currentProfile['blood_type'] ?? ''), 5);
    $weight = $hasKey('weight')
        ? sanitizeText($data['weight'] ?? '', 20)
        : sanitizeText((string) ($currentProfile['weight'] ?? ''), 20);
    $height = $hasKey('height')
        ? sanitizeText($data['height'] ?? '', 20)
        : sanitizeText((string) ($currentProfile['height'] ?? ''), 20);
    $allergyNotes = $hasKey('allergy_notes')
        ? sanitizeText($data['allergy_notes'] ?? '', 500)
        : sanitizeText((string) ($currentProfile['allergy_notes'] ?? ''), 500);

    if ($nama === '' || $phone === '') {
        jsonResponse(['status' => 'error', 'message' => 'Nama dan Nomor HP wajib diisi!'], 422);
    }

    if ($province !== '' && $city !== '' && RAJAONGKIR_API_KEY !== '') {
        $matchedLocation = searchRajaOngkirCity($city, $province, $district, $village);
        $postalCode = preg_replace('/[^\d-]/', '', (string) ($matchedLocation['zip_code'] ?? ''));
    }

    $pdo->beginTransaction();

    // 1. Update tabel users (Data profil & kontak)
    $stmtUser = $pdo->prepare("UPDATE users SET nama = :nama, phone = :phone, dob = :dob, gender = :gender WHERE id = :id");
    $stmtUser->execute([
        ':nama' => $nama,
        ':phone' => $phone,
        ':dob' => $dob,
        ':gender' => $gender,
        ':id' => $userId
    ]);

    // 2. Update/Insert tabel Pasien (alamat marketplace + ringkasan medis) menggunakan UPSERT
    $stmtPasien = $pdo->prepare("
        INSERT INTO Pasien (
            id_user, alamat, tanggalLahir, blood_type, weight, height, allergy_notes,
            address_label, recipient_name, village, district, city, province, postal_code, address_notes
        ) 
        VALUES (
            :id_user, :alamat, :dob, :blood_type, :weight, :height, :allergy_notes,
            :address_label, :recipient_name, :village, :district, :city, :province, :postal_code, :address_notes
        )
        ON CONFLICT (id_user) 
        DO UPDATE SET
            alamat = EXCLUDED.alamat,
            tanggalLahir = EXCLUDED.tanggalLahir,
            blood_type = EXCLUDED.blood_type,
            weight = EXCLUDED.weight,
            height = EXCLUDED.height,
            allergy_notes = EXCLUDED.allergy_notes,
            address_label = EXCLUDED.address_label,
            recipient_name = EXCLUDED.recipient_name,
            village = EXCLUDED.village,
            district = EXCLUDED.district,
            city = EXCLUDED.city,
            province = EXCLUDED.province,
            postal_code = EXCLUDED.postal_code,
            address_notes = EXCLUDED.address_notes
    ");
    $stmtPasien->execute([
        ':id_user' => $userId,
        ':alamat' => $alamat,
        ':dob' => $dob,
        ':blood_type' => $bloodType !== '' ? $bloodType : null,
        ':weight' => $weight !== '' ? $weight : null,
        ':height' => $height !== '' ? $height : null,
        ':allergy_notes' => $allergyNotes !== '' ? $allergyNotes : null,
        ':address_label' => $addressLabel !== '' ? $addressLabel : null,
        ':recipient_name' => $recipientName !== '' ? $recipientName : null,
        ':village' => $village !== '' ? $village : null,
        ':district' => $district !== '' ? $district : null,
        ':city' => $city !== '' ? $city : null,
        ':province' => $province !== '' ? $province : null,
        ':postal_code' => $postalCode !== '' ? $postalCode : null,
        ':address_notes' => $addressNotes !== '' ? $addressNotes : null
    ]);

    $pdo->commit();
    writeAuditLog($pdo, 'profile.update', 'success', $userId, 'users', (string) $userId, [
        'updated_fields' => ['nama', 'phone', 'dob', 'gender', 'alamat', 'blood_type'],
    ]);
    jsonResponse(['status' => 'success', 'message' => 'Profil berhasil diperbarui!']);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    writeAuditLog($pdo, 'profile.update', 'failed', $userId, 'users', (string) $userId, [
        'reason' => 'server_error',
    ]);
    handleServerException($e, 'Profil gagal diperbarui.');
}
