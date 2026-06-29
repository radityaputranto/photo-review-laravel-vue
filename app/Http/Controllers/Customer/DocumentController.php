<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $documents = Document::with('session')
            ->where('customer_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Customer/Documents', [
            'documents' => $documents
        ]);
    }

    public function preview(Document $document)
    {
        if ($document->customer_id !== request()->user()->id) {
            abort(403);
        }

        return Inertia::render('Customer/DocumentPreview', [
            'document' => $document
        ]);
    }
}
