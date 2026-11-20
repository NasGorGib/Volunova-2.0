<?php
// ENTITY: Profile (mewakili tabel `profiles`, satu baris per akun)
class Profile
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    // Ambil profil milik akun, atau null jika belum pernah diisi
    public function findByUser(int $userId): ?array
    {
        $st = $this->conn->prepare('SELECT nama_lengkap, nim, universitas, jurusan, telepon, domisili FROM profiles WHERE user_id = ? LIMIT 1');
        $st->bind_param('i', $userId);
        $st->execute();
        return $st->get_result()->fetch_assoc() ?: null;
    }

    // Profil dianggap lengkap jika sudah tersimpan (data hanya tersimpan jika valid)
    public function isComplete(int $userId): bool
    {
        try {
            return $this->findByUser($userId) !== null;
        } catch (Throwable $e) {
            return false;
        }
    }

    // Cek apakah NIM sudah dipakai akun lain
    public function isNimTaken(string $nim, int $userId): bool
    {
        $st = $this->conn->prepare('SELECT user_id FROM profiles WHERE nim = ? AND user_id <> ? LIMIT 1');
        $st->bind_param('si', $nim, $userId);
        $st->execute();
        return $st->get_result()->num_rows > 0;
    }

    // Simpan profil baru atau perbarui yang sudah ada
    public function save(int $userId, array $d): bool
    {
        try {
            $st = $this->conn->prepare(
                'INSERT INTO profiles (user_id, nama_lengkap, nim, universitas, jurusan, telepon, domisili) VALUES (?, ?, ?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE nama_lengkap = VALUES(nama_lengkap), nim = VALUES(nim), universitas = VALUES(universitas),
                 jurusan = VALUES(jurusan), telepon = VALUES(telepon), domisili = VALUES(domisili)'
            );
            $st->bind_param('issssss', $userId, $d['nama_lengkap'], $d['nim'], $d['universitas'], $d['jurusan'], $d['telepon'], $d['domisili']);
            $st->execute();
            return true;
        } catch (Throwable $e) {
            return false;
        }
    }
}
