<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::whereIn('role', ['admin', 'photographer']);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $admins = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return Inertia::render('Admin/Admins/Index', [
            'admins' => $admins,
            'filters' => $request->only('search')
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', Rule::in(['admin', 'photographer'])],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Staff member created successfully.');
    }

    public function update(Request $request, User $admin)
    {
        // Check if the user tries to update a non-staff user
        if (!in_array($admin->role, ['admin', 'photographer', 'super_admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $admin->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', Rule::in(['admin', 'photographer'])],
        ]);

        // Prevent updating super_admin's role through this interface, just in case
        if ($admin->role === 'super_admin') {
            abort(403, 'Cannot modify super admin role.');
        }

        // Prevent self role downgrade
        if (auth()->id() === $admin->id && $request->role !== 'admin') {
            abort(403, 'You cannot downgrade your own role.');
        }

        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->role = $request->role;

        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }

        $admin->save();

        return redirect()->back()->with('success', 'Staff member updated successfully.');
    }

    public function destroy(User $admin)
    {
        if ($admin->role === 'super_admin') {
            abort(403, 'Cannot delete super admin.');
        }
        
        if (auth()->id() === $admin->id) {
            abort(403, 'You cannot delete yourself.');
        }

        if (!in_array($admin->role, ['admin', 'photographer'])) {
             abort(403, 'Unauthorized action.');
        }

        $admin->delete();
        return redirect()->back()->with('success', 'Staff member deleted successfully.');
    }

    public function toggleActive(User $admin)
    {
        if ($admin->role === 'super_admin') {
             abort(403, 'Cannot modify super admin.');
        }

        if (auth()->id() === $admin->id) {
             abort(403, 'You cannot deactivate yourself.');
        }

        $admin->is_active = !$admin->is_active;
        $admin->save();

        return redirect()->back()->with('success', 'Staff member status updated.');
    }
}
