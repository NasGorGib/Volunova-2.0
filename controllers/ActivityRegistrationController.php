<?php
// CONTROLLER: pendaftaran kegiatan oleh Volunteer
class ActivityRegistrationController
{
    private Profile $profile;

    public function __construct(Profile $profile)
    {
        $this->profile = $profile;
    }

    public function register(array $data): void
    {
        // 1. Harus sudah login
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            out(false, 'Silakan login terlebih dahulu.', [], 401);
        }

        // 2. ID kegiatan harus bilangan bulat >= 1
        $activityId = filter_var($data['activity_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($activityId === false) {
            out(false, 'Kegiatan tidak valid.', [], 422);
        }

        // 3. Profil harus sudah lengkap
        if (!$this->profile->isComplete((int) $userId)) {
            out(false, 'Profil Anda belum lengkap. Lengkapi profil terlebih dahulu sebelum mendaftar kegiatan.', ['code' => 'PROFILE_INCOMPLETE'], 422);
        }

        // 4. Penyimpanan pendaftaran belum dibuat
        out(false, 'Pendaftaran kegiatan belum tersedia.', [], 501);
    }
}
