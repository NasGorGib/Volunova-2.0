<?php
// ENTITY: User (mewakili tabel `users`)
class User
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    // Cek apakah username atau email sudah dipakai
    public function isExist(string $nama, string $email): bool
    {
        $st = $this->conn->prepare('SELECT id FROM users WHERE nama = ? OR email = ? LIMIT 1');
        $st->bind_param('ss', $nama, $email);
        $st->execute();
        return $st->get_result()->num_rows > 0;
    }

    // Simpan akun baru (kata sandi sudah dalam bentuk hash)
    public function save(string $nama, string $email, string $passwordHash): bool
    {
        try {
            $st = $this->conn->prepare('INSERT INTO users (nama, email, password) VALUES (?, ?, ?)');
            $st->bind_param('sss', $nama, $email, $passwordHash);
            $st->execute();
            return true;
        } catch (Throwable $e) {
            return false;
        }
    }

    // Ambil username dan email berdasarkan id akun
    public function findById(int $id): ?array
    {
        $st = $this->conn->prepare('SELECT nama, email FROM users WHERE id = ? LIMIT 1');
        $st->bind_param('i', $id);
        $st->execute();
        return $st->get_result()->fetch_assoc() ?: null;
    }
}
