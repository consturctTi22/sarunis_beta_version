<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Services\AuthService;

class ProfileController extends PortalDashboardController
{
    public function edit(Request $request, AuthService $authService): View
    {
        /** @var \App\Models\User $user */
        $user = $request->user()->load(['teacherProfile', 'studentProfile']);
        $profile = $user->teacherProfile ?? $user->studentProfile;

        // Determine portal for the sidebar and layout
        $portalKey = session('portalKey') ?? $authService->defaultPortal($user) ?? 'siswa';

        // Load menu sections using parent method
        $menuSections = $this->menuForPortalPage($portalKey, 'Profil Saya');
        
        return view('dashboard.profile', [
            'pageTitle' => 'Profil Saya',
            'user' => $user,
            'profile' => $profile,
            'portalKey' => $portalKey,
            'menuSections' => $menuSections,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', Password::defaults(), 'confirmed'],
        ]);

        $user->fill([
            'email' => $validated['email'],
        ]);

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        if ($user->teacherProfile) {
            $user->teacherProfile->update([
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
            ]);
        } elseif ($user->studentProfile) {
            $user->studentProfile->update([
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
            ]);
        }

        return back()->with('status', 'profil-diperbarui');
    }
}
