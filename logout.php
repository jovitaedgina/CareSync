<?php
// caresync/logout.php

// (Nanti Farras akan menambahkan kode session_start() dan session_destroy() di baris ini)

// Untuk sekarang, kita buat script ini otomatis melempar user kembali ke halaman Portal Staf
header("Location: pages/portal_staf.php");
exit;
?>