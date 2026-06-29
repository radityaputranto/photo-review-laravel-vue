<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhotoSession;
use App\Models\User;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SessionController extends Controller
{
    protected GoogleDriveService $driveService;

    public function __construct(GoogleDriveService $driveService)
    {
        $this->driveService = $driveService;
    }

    public function index()
    {
        $sessions = PhotoSession::with('customer')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Admin/Sessions/Index', [
            'sessions' => $sessions
        ]);
    }

    public function create()
    {
        $customers = User::where('role', 'customer')->orderBy('name')->get();

        return Inertia::render('Admin/Sessions/Create', [
            'customers' => $customers
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'shoot_date' => 'required|date',
            'drive_folder_url' => 'required|url',
            'max_selections' => 'required|integer|min:1',
        ]);

        $folderId = $this->driveService->extractFolderId($request->drive_folder_url);

        PhotoSession::create([
            'customer_id' => $request->customer_id,
            'title' => $request->title,
            'shoot_date' => $request->shoot_date,
            'drive_folder_url' => $request->drive_folder_url,
            'drive_folder_id' => $folderId,
            'max_selections' => $request->max_selections,
            'status' => 'active',
        ]);

        return redirect()->route('admin.sessions.index')->with('success', 'Photo Session created successfully.');
    }

    public function show(PhotoSession $session)
    {
        $session->load('customer', 'photoSelections');
        
        // Coba fetch dari Drive untuk memastikan bisa diakses dan menghitung total foto
        try {
            $photos = $this->driveService->getPhotosFromFolder($session->drive_folder_id);
            $totalPhotos = count($photos);
        } catch (\Exception $e) {
            $totalPhotos = 0;
            // Optionally log error
        }

        return Inertia::render('Admin/Sessions/Show', [
            'session' => $session,
            'totalPhotos' => $totalPhotos,
        ]);
    }

    public function edit(PhotoSession $session)
    {
        $customers = User::where('role', 'customer')->orderBy('name')->get();

        return Inertia::render('Admin/Sessions/Edit', [
            'session' => $session,
            'customers' => $customers
        ]);
    }

    public function update(Request $request, PhotoSession $session)
    {
        $request->validate([
            'customer_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'shoot_date' => 'required|date',
            'drive_folder_url' => 'required|url',
            'max_selections' => 'required|integer|min:1',
            'status' => 'required|in:active,completed,delivered',
            'download_link' => 'nullable|url',
        ]);

        $folderId = $this->driveService->extractFolderId($request->drive_folder_url);

        $session->update([
            'customer_id' => $request->customer_id,
            'title' => $request->title,
            'shoot_date' => $request->shoot_date,
            'drive_folder_url' => $request->drive_folder_url,
            'drive_folder_id' => $folderId,
            'max_selections' => $request->max_selections,
            'status' => $request->status,
            'download_link' => $request->download_link,
        ]);

        return redirect()->route('admin.sessions.index')->with('success', 'Photo Session updated successfully.');
    }

    public function destroy(PhotoSession $session)
    {
        $session->delete();
        return redirect()->route('admin.sessions.index')->with('success', 'Photo Session deleted successfully.');
    }

    public function validateDriveUrl(Request $request)
    {
        $request->validate(['url' => 'required|url']);
        
        $folderId = $this->driveService->extractFolderId($request->url);
        $folderInfo = $this->driveService->getFolderInfo($folderId);

        if ($folderInfo) {
            return response()->json([
                'success' => true,
                'message' => 'Folder found: ' . $folderInfo['name'],
                'folder_id' => $folderId
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Cannot access folder. Please check URL and permissions.'
        ], 400);
    }
}
