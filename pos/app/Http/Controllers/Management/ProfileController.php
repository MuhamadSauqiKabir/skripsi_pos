<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class ProfileController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLogService,
    ) {
    }

    public function update(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        // Pastikan hanya Admin atau Super Admin
        if (!in_array($user->role->value, ['super_admin', 'admin'])) {
            return back()->with('error', 'Hanya Admin dan Super Admin yang dapat mengelola profil.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'gender' => ['nullable', 'string', 'in:Male,Female'],
            'bio' => ['nullable', 'string'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        $updateData = [
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'gender' => $request->gender,
            'bio' => $request->bio,
        ];

        if ($request->hasFile('avatar')) {
            $updateData['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($updateData);

        $this->auditLogService->log($user->id, 'profile.updated', 'user', $user->id, $updateData, $request);

        return back()->with('success', 'Profil Anda berhasil diperbarui.');
    }
}
