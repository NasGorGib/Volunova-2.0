<?php
require 'config.php';
$d = input();
$nama  = trim($d['name'] ?? '');
$email = trim($d['email'] ?? '');
$pw    = $d['password'] ?? '';
$cf    = $d['password_confirmation'] ?? '';

// Validasi sisi server (sama dengan aturan di form)
if (!preg_match('/^[A-Za-z0-9_]{6,30}$/', $nama))
    out(false, 'Username harus 6-30 karakter: huruf, angka, underscore.', [], 422);
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100)
    out(false, 'Format email tidak valid.', [], 422);
if (strlen($pw) < 8 || preg_match('/\s/', $pw) || !preg_match('/[A-Z]/', $pw) || !preg_match('/[^A-Za-z0-9\s]/', $pw))
    out(false, 'Kata sandi minimal 8 karakter, tanpa spasi, ada huruf kapital dan karakter spesial.', [], 422);
if ($pw !== $cf)
    out(false, 'Konfirmasi kata sandi tidak cocok.', [], 422);

try {
    // Cek username/email sudah dipakai
    $st = $conn->prepare('SELECT id FROM users WHERE nama = ? OR email = ? LIMIT 1');
    $st->bind_param('ss', $nama, $email);
    $st->execute();
    if ($st->get_result()->num_rows > 0)
        out(false, 'Username atau email sudah terdaftar.', [], 409);

    $hash = password_hash($pw, PASSWORD_DEFAULT);
    $st = $conn->prepare('INSERT INTO users (nama, email, password) VALUES (?, ?, ?)');
    $st->bind_param('sss', $nama, $email, $hash);
    $st->execute();
    out(true, 'Pendaftaran berhasil! Mengalihkan ke halaman login...');
} catch (Throwable $e) {
    out(false, 'Gagal menyimpan akun.', [], 500);
}
