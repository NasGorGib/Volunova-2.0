<?php
require 'config.php';
$d = input();
$id = trim($d['login'] ?? '');
$pw = $d['password'] ?? '';
if ($id === '' || $pw === '') out(false, 'Username/email dan kata sandi wajib diisi.', [], 422);

try {
    $st = $conn->prepare('SELECT id, nama, password FROM users WHERE nama = ? OR email = ? LIMIT 1');
    $st->bind_param('ss', $id, $id);
    $st->execute();
    $u = $st->get_result()->fetch_assoc();

    if (!$u || !password_verify($pw, $u['password']))
        out(false, 'Username/email atau kata sandi salah.', [], 401);

    session_regenerate_id(true);
    $_SESSION['user_id'] = $u['id'];
    $_SESSION['nama'] = $u['nama'];
    out(true, 'Berhasil masuk.');
} catch (Throwable $e) {
    out(false, 'Terjadi kesalahan server.', [], 500);
}
