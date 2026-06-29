<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\PhotoSession;
use App\Services\GoogleDriveService;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class GalleryController extends Controller
{
    protected GoogleDriveService $driveService;

    public function __construct(GoogleDriveService $driveService)
    {
        $this->driveService = $driveService;
    }

    public function show(PhotoSession $session)
    {
        Gate::authorize('view', $session);

        // Fetch photos from Google Drive
        $photos = [];
        try {
            $photos = $this->driveService->getPhotosFromFolder($session->drive_folder_id);
        } catch (\Exception $e) {
            // Silently handle or log
        }

        // Fetch user's current selections
        $selections = $session->photoSelections()->where('customer_id', request()->user()->id)->get();
        $selectedIds = $selections->pluck('drive_file_id')->toArray();

        return Inertia::render('Customer/Gallery', [
            'session' => $session,
            'photos' => $photos,
            'selectedIds' => $selectedIds,
            'submitted' => !is_null($session->submitted_at) || $session->status !== 'active',
        ]);
    }
}
