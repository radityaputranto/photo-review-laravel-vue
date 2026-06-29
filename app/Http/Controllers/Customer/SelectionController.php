<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\PhotoSelection;
use App\Models\PhotoSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use App\Services\GoogleDriveService;

class SelectionController extends Controller
{
    protected GoogleDriveService $driveService;

    public function __construct(GoogleDriveService $driveService)
    {
        $this->driveService = $driveService;
    }
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
        
        $photos = [];
        try {
            $photos = $this->driveService->getPhotosFromFolder($session->drive_folder_id);
        } catch (\Exception $e) {
            //
        }
        
        $photosById = collect($photos)->keyBy('id');
        
        $selections = $selections->map(function ($sel) use ($photosById) {
            $sel->thumbnail_url = $photosById->get($sel->drive_file_id)['thumbnail'] ?? null;
            return $sel;
        });

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

        if ($currentCount === 0) {
            return redirect()->back()->withErrors(['message' => "Anda harus memilih setidaknya 1 foto sebelum mengirim."]);
        }

        // Lock the session
        $session->update([
            'submitted_at' => now(),
            'status' => 'completed',
        ]);

        return redirect()->route('customer.dashboard')->with('success', 'Thank you! Your selections have been submitted for processing.');
    }
}
