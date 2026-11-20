<?php
// CONTROLLER: melihat dan menyimpan profil pengguna
class ProfileController
{
    private Profile $profile;
    private User $user;

    public function __construct(Profile $profile, User $user)
    {
        $this->profile = $profile;
        $this->user = $user;
    }

    // GET = tampilkan profil, POST = simpan profil
    public function handle(string $method, array $data): void
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            out(false, 'Silakan login terlebih dahulu.', [], 401);
        }
        try {
            if ($method === 'POST') {
                $this->update((int) $userId, $data);
            }
            $this->show((int) $userId);
        } catch (Throwable $e) {
            out(false, 'Terjadi kesalahan server.', [], 500);
        }
    }

    private function show(int $userId): void
    {
        $acc = $this->user->findById($userId);
        $profile = $this->profile->findByUser($userId);
        out(true, '', [
            'account'  => ['username' => $acc['nama'] ?? '', 'email' => $acc['email'] ?? ''],
            'profile'  => $profile,
            'complete' => $profile !== null,
        ]);
    }

    private function update(int $userId, array $data): void
    {
        $d = [];
        foreach (['nama_lengkap', 'nim', 'universitas', 'jurusan', 'telepon', 'domisili'] as $k) {
            $d[$k] = is_string($data[$k] ?? null) ? trim($data[$k]) : '';
        }

        $errors = $this->validate($d);
        if ($errors) {
            out(false, 'Data profil belum valid. Periksa kolom yang ditandai.', ['errors' => $errors], 422);
        }
        if ($this->profile->isNimTaken($d['nim'], $userId)) {
            out(false, 'NIM sudah dipakai akun lain.', ['errors' => ['nim' => 'NIM sudah dipakai akun lain.']], 409);
        }
        if (!$this->profile->save($userId, $d)) {
            out(false, 'Gagal menyimpan profil.', [], 500);
        }
        out(true, 'Profil berhasil disimpan.', ['profile' => $d, 'complete' => true]);
    }

    // Mengembalikan daftar pesan error per kolom (kosong jika semua valid)
    private function validate(array $d): array
    {
        $e = [];
        if ($d['nama_lengkap'] === '') {
            $e['nama_lengkap'] = 'Nama lengkap wajib diisi.';
        } elseif (!preg_match('/^[A-Za-z .\'\-]{3,50}$/', $d['nama_lengkap'])) {
            $e['nama_lengkap'] = 'Nama lengkap 3-50 karakter dan hanya berisi huruf, spasi, titik, apostrof, atau tanda hubung.';
        }
        if ($d['nim'] === '') {
            $e['nim'] = 'NIM wajib diisi.';
        } elseif (!preg_match('/^[0-9]{8,15}$/', $d['nim'])) {
            $e['nim'] = 'NIM harus berupa 8-15 angka.';
        }
        foreach (['universitas' => 'Universitas', 'jurusan' => 'Jurusan'] as $k => $label) {
            $len = $this->len($d[$k]);
            if ($len === 0) {
                $e[$k] = $label . ' wajib diisi.';
            } elseif ($len < 3 || $len > 60) {
                $e[$k] = $label . ' harus 3-60 karakter.';
            }
        }
        if ($d['telepon'] === '') {
            $e['telepon'] = 'Nomor telepon wajib diisi.';
        } elseif (!preg_match('/^08[0-9]{8,11}$/', $d['telepon'])) {
            $e['telepon'] = 'Nomor telepon harus diawali 08 dan terdiri dari 10-13 angka.';
        }
        $len = $this->len($d['domisili']);
        if ($len === 0) {
            $e['domisili'] = 'Domisili wajib diisi.';
        } elseif ($len < 3 || $len > 50) {
            $e['domisili'] = 'Domisili harus 3-50 karakter.';
        }
        return $e;
    }

    // Hitung jumlah karakter (tanpa bergantung pada ekstensi mbstring)
    private function len(string $s): int
    {
        $n = preg_match_all('/./su', $s);
        return $n === false ? strlen($s) : $n;
    }
}
