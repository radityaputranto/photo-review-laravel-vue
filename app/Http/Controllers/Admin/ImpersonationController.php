<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    /**
     * Start impersonating a user.
     */
    public function start(Request $request, User $user)
    {
        // Only allow impersonating customers
        if ($user->role !== 'customer') {
            abort(403, 'You can only impersonate customers.');
        }

        // Store the original admin's ID in the session
        session()->put('impersonated_by', auth()->id());

        // Log in as the target user
        auth()->login($user);

        return redirect()->route('customer.dashboard')->with('success', "You are now logged in as {$user->name}.");
    }

    /**
     * Stop impersonating and revert back to original admin.
     */
    public function stop(Request $request)
    {
        if (!session()->has('impersonated_by')) {
            abort(403, 'You are not impersonating anyone.');
        }

        $adminId = session()->pull('impersonated_by');
        $admin = User::findOrFail($adminId);

        // Log back in as the admin
        auth()->login($admin);

        return redirect()->route('admin.customers.index')->with('success', 'Welcome back to your admin account.');
    }
}
