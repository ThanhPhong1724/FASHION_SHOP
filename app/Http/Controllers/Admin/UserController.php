<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = User::with('roles');

        // Filter by role
        if ($request->filled('role')) {
            $query->role($request->role);
        }

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by registration date
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $users = $query->latest()->paginate(20);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): View
    {
        $user->load(['roles', 'orders', 'reviews', 'addresses']);
        
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): View
    {
        $roles = Role::all();
        
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'is_active' => $request->has('is_active'),
        ]);

        // Update password if provided
        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        // Update roles
        if ($request->has('roles')) {
            // Convert role IDs to role names
            $roleNames = Role::whereIn('id', $request->roles)->pluck('name')->toArray();
            $user->syncRoles($roleNames);
        } else {
            $user->syncRoles([]);
        }

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'Đã cập nhật thông tin người dùng!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Prevent deleting admin users
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Không thể xóa tài khoản admin!');
        }

        // Check if user has orders
        if ($user->orders()->count() > 0) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Không thể xóa người dùng đã có đơn hàng!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Đã xóa người dùng!');
    }

    /**
     * Toggle user status (active/inactive)
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        // Prevent deactivating admin users
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Không thể vô hiệu hóa tài khoản admin!');
        }

        $user->update([
            'is_active' => !$user->is_active,
        ]);

        $status = $user->is_active ? 'kích hoạt' : 'vô hiệu hóa';
        
        return redirect()->route('admin.users.index')
            ->with('success', "Đã {$status} người dùng!");
    }
}
