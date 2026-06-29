<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\PhotoSelection;
use App\Models\PhotoSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class SelectionController extends Controller
{
    public function toggle(Request $request, PhotoSession $session)
    {
        Gate::authorize('select', $session);

        $request->validate([
            'drive_file_id' => 'required|string',
            'file_name' => 'required|string',
        ]);

        $user = $request->user();

        $existing = PhotoSelection::where('session_id', $session->id)
            ->where('customer_id', $user->id)
            ->where('drive_file_id', $request->drive_file_id)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['status' => 'detached']);
        }

        $currentCount = PhotoSelection::where('session_id', $session->id)
            ->where('customer_id', $user->id)
            ->count();

        if ($currentCount >= $session->max_selections) {
            return response()->json(['error' => 'Max selections reached'], 400);
        }

        PhotoSelection::create([
            'session_id' => $session->id,
            'customer_id' => $user->id,
            'drive_file_id' => $request->drive_file_id,
            'file_name' => $request->file_name,
        ]);

        return response()->json(['status' => 'attached']);
    }

    public function review(PhotoSession $session)
    {
        Gate::authorize('view', $session);

        $selections = $session->photoSelections()->where('customer_id', request()->user()->id)->get();

        return Inertia::render('Customer/SelectionReview', [
            'session' => $session,
            'selections' => $selections,
            'submitted' => !is_null($session->submitted_at) || $session->status !== 'active',
        ]);
    }

    public function submit(Request $request, PhotoSession $session)
    {
        Gate::authorize('select', $session);

        $user = $request->user();
        
        $currentCount = PhotoSelection::where('session_id', $session->id)
            ->where('customer_id', $user->id)
            ->count();

        if ($currentCount < $session->max_selections) {
            return redirect()->back()->withErrors(['message' => "You must select {$session->max_selections} photos before submitting."]);
        }

        // Lock the session
        $session->update([
            'submitted_at' => now(),
            'status' => 'completed',
        ]);

        return redirect()->route('customer.dashboard')->with('success', 'Thank you! Your selections have been submitted for processing.');
    }
}
