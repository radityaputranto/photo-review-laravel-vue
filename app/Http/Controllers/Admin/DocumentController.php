<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\PhotoSession;
use App\Models\User;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DocumentController extends Controller
{
    protected GoogleDriveService $driveService;

    public function __construct(GoogleDriveService $driveService)
    {
        $this->driveService = $driveService;
    }

    public function index()
    {
        $documents = Document::with(['customer', 'session'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $customers = User::where('role', 'customer')->orderBy('name')->get();
        $sessions = PhotoSession::orderBy('title')->get(['id', 'title', 'customer_id']);

        return Inertia::render('Admin/Documents/Index', [
            'documents' => $documents,
            'customers' => $customers,
            'sessions' => $sessions,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:users,id',
            'session_id' => 'nullable|exists:photo_sessions,id',
            'type' => 'required|in:invoice,contract,other',
            'title' => 'required|string|max:255',
            'drive_url' => 'required|url',
        ]);

        // Ekstrak File ID dari URL (biasanya file/d/ID/view)
        $fileId = $this->extractFileId($request->drive_url);

        Document::create([
            'customer_id' => $request->customer_id,
            'session_id' => $request->session_id,
            'type' => $request->type,
            'title' => $request->title,
            'drive_file_id' => $fileId,
        ]);

        return redirect()->back()->with('success', 'Document added successfully.');
    }

    public function destroy(Document $document)
    {
        $document->delete();
        return redirect()->back()->with('success', 'Document deleted successfully.');
    }

    private function extractFileId(string $url): string
    {
        if (preg_match('/file\/d\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return $matches[1];
        }
        // Fallback: If they provided just the ID
        return $url;
    }
}
