<?php

namespace App\Http\Controllers;

use App\Services\AuthRecoveryService;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AuthRecoveryController extends Controller
{
    public function __construct(
        protected AuthRecoveryService $authRecoveryService,
    ) {
    }

    public function sendCode(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'portal' => ['required', 'string', Rule::in(array_keys(AuthService::portalMap()))],
            'email' => ['required', 'email'],
        ]);

        $this->authRecoveryService->sendCode($payload['email'], $payload['portal']);

        return redirect()
            ->route('auth.page.verify-email', [
                'portal' => $payload['portal'],
                'email' => $payload['email'],
            ])
            ->with('status', 'Tautan pengaturan ulang kata sandi telah dikirim ke email Anda.');
    }

    public function verifyCode(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'portal' => ['required', 'string', Rule::in(array_keys(AuthService::portalMap()))],
            'email' => ['required', 'email'],
            'code' => ['required'],
        ]);

        $code = is_array($payload['code'])
            ? implode('', $payload['code'])
            : (string) $payload['code'];

        $token = $this->authRecoveryService->verifyCode(
            $payload['email'],
            $payload['portal'],
            strtoupper(trim($code)),
        );

        return redirect()->route('auth.page.reset-password', [
            'portal' => $payload['portal'],
            'email' => $payload['email'],
            'token' => $token,
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'portal' => ['required', 'string', Rule::in(array_keys(AuthService::portalMap()))],
            'email' => ['required', 'email'],
            'token' => ['required', 'string'],
            'password' => ['required', 'string', Password::min(8)->letters()->numbers(), 'confirmed'],
        ]);

        $this->authRecoveryService->resetPassword(
            $payload['email'],
            $payload['portal'],
            $payload['token'],
            $payload['password'],
        );

        return redirect()->route('auth.page.login', [
            'portal' => $payload['portal'],
            'email' => $payload['email'],
            'reset' => 'success',
        ]);
    }
}
