<?php

namespace App\Http\Controllers\Management;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function __construct(
        private readonly \App\Services\AuditLogService $auditLogService,
    ) {
    }

    public function index(): Response
    {
        return Inertia::render('Dashboard/Users', [
            'users' => User::query()
                ->where('role', '!=', Role::Customer->value)
                ->latest()
                ->paginate(10),
            'roles' => collect(Role::cases())
                ->filter(fn (Role $role) => $role !== Role::Customer)
                ->map(fn (Role $role) => [
                    'value' => $role->value,
                    'label' => $role->label(),
                ])
                ->values(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role' => ['required', 'string', 'in:super_admin,admin,employee'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'gender' => ['nullable', 'string', 'in:Male,Female'],
            'bio' => ['nullable', 'string'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // Keamanan: Hanya SuperAdmin yang bisa membuat SuperAdmin baru
        if ($data['role'] === Role::SuperAdmin->value && $request->user()->role !== Role::SuperAdmin) {
            return back()->with('error', 'Hanya Super Admin yang dapat membuat akun Super Admin baru.');
        }

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        $this->auditLogService->log(auth()->id(), 'user.created', 'user', $user->id, $data, $request);

        return back()->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'role' => ['required', 'string', 'in:super_admin,admin,employee'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'gender' => ['nullable', 'string', 'in:Male,Female'],
            'bio' => ['nullable', 'string'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // Keamanan: Admin tidak bisa mengubah role ke/dari SuperAdmin
        if (($data['role'] === Role::SuperAdmin->value || $user->role === Role::SuperAdmin) && $request->user()->role !== Role::SuperAdmin) {
            return back()->with('error', 'Anda tidak memiliki wewenang untuk mengubah data Super Admin.');
        }

        $user->update($data);

        $this->auditLogService->log(auth()->id(), 'user.updated', 'user', $user->id, $data, $request);

        return back()->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }

        // Keamanan: Admin tidak bisa menghapus SuperAdmin
        if ($user->role === Role::SuperAdmin && $request->user()->role !== Role::SuperAdmin) {
            return back()->with('error', 'Akun Super Admin tidak dapat dihapus oleh Admin.');
        }

        $user->delete();

        return back()->with('success', 'User berhasil dihapus.');
    }
}
