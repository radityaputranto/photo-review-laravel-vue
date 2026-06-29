<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\PhotoSession;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = request()->user();

        $activeSessions = PhotoSession::where('customer_id', $user->id)
            ->where('status', 'active')
            ->orderBy('shoot_date', 'asc')
            ->get();

        $completedSessions = PhotoSession::where('customer_id', $user->id)
            ->whereIn('status', ['completed', 'delivered'])
            ->orderBy('shoot_date', 'desc')
            ->take(5)
            ->get();

        return Inertia::render('Customer/Dashboard', [
            'activeSessions' => $activeSessions,
            'completedSessions' => $completedSessions,
        ]);
    }
}
