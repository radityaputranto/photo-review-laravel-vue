<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhotoSession;
use App\Models\User;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_customers' => User::where('role', 'customer')->count(),
            'active_sessions' => PhotoSession::where('status', 'active')->count(),
            'pending_reviews' => PhotoSession::where('status', 'completed')->count(),
            'delivered_sessions' => PhotoSession::where('status', 'delivered')->count(),
        ];

        $recentSessions = PhotoSession::with('customer')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentSessions' => $recentSessions,
        ]);
    }
}
