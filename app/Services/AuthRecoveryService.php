<?php

namespace App\Services;

use App\Models\AuthVerificationCode;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthRecoveryService
{
    public function __construct(
        protected AuthService $authService,
    ) {
    }

    public function sendCode(string $email, string $portal): void
    {
        $user = $this->userForPortal($email, $portal);
        $code = (string) random_int(10000, 99999);

        AuthVerificationCode::query()
            ->where('email', $user->email)
            ->where('portal', $portal)
            ->where('purpose', 'password_reset')
            ->delete();

        AuthVerificationCode::query()->create([
            'email' => $user->email,
            'portal' => $portal,
            'purpose' => 'password_reset',
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes(15),
        ]);

        Mail::send(
            'emails.auth-recovery-code',
            ['code' => $code, 'user' => $user],
            static function ($message) use ($user): void {
                $message->to($user->email)->subject('Kode Verifikasi Sarunis');
            },
        );
    }

    public function verifyCode(string $email, string $portal, string $code): string
    {
        $user = $this->userForPortal($email, $portal);
        $verification = $this->activeVerification($user->email, $portal);

        if ($verification->attempts >= 5) {
            throw ValidationException::withMessages([
                'code' => ['Kode sudah terlalu sering dicoba. Kirim ulang kode baru.'],
            ]);
        }

        if (! Hash::check($code, $verification->code_hash)) {
            $verification->increment('attempts');

            throw ValidationException::withMessages([
                'code' => ['Kode verifikasi tidak valid.'],
            ]);
        }

        $token = Str::random(64);

        $verification->forceFill([
            'verified_at' => now(),
            'reset_token_hash' => Hash::make($token),
        ])->save();

        return $token;
    }

    public function resetPassword(string $email, string $portal, string $token, string $password): void
    {
        $user = $this->userForPortal($email, $portal);
        $verification = $this->activeVerification($user->email, $portal);

        if ($verification->verified_at === null || $verification->reset_token_hash === null) {
            throw ValidationException::withMessages([
                'token' => ['Kode belum diverifikasi.'],
            ]);
        }

        if (! Hash::check($token, $verification->reset_token_hash)) {
            throw ValidationException::withMessages([
                'token' => ['Token reset tidak valid.'],
            ]);
        }

        $user->forceFill([
            'password' => $password,
            'email_verified_at' => $user->email_verified_at ?? now(),
        ])->save();

        AuthVerificationCode::query()
            ->where('email', $user->email)
            ->where('portal', $portal)
            ->where('purpose', 'password_reset')
            ->delete();
    }

    protected function userForPortal(string $email, string $portal): User
    {
        $user = User::query()->where('email', $email)->first();

        if ($user === null || ! $this->authService->canAccessPortal($user, $portal)) {
            throw ValidationException::withMessages([
                'email' => ['Email tidak ditemukan atau tidak memiliki akses ke portal ini.'],
            ]);
        }

        return $user;
    }

    protected function activeVerification(string $email, string $portal): AuthVerificationCode
    {
        $verification = AuthVerificationCode::query()
            ->where('email', $email)
            ->where('portal', $portal)
            ->where('purpose', 'password_reset')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if ($verification === null) {
            throw ValidationException::withMessages([
                'code' => ['Kode sudah kedaluwarsa atau belum dikirim.'],
            ]);
        }

        return $verification;
    }
}
