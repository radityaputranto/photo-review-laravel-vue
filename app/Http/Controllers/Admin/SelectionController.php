<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhotoSession;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SelectionController extends Controller
{
    public function show(PhotoSession $session)
    {
        $session->load('customer', 'photoSelections');
        
        return Inertia::render('Admin/Selections/Show', [
            'session' => $session
        ]);
    }

    public function export(PhotoSession $session)
    {
        $selections = $session->photoSelections()->orderBy('file_name')->get();
        
        if ($selections->isEmpty()) {
            return back()->withErrors(['message' => 'No selections found to export.']);
        }

        $content = "Session: {$session->title}\nCustomer: {$session->customer->name}\nDate: {$session->shoot_date}\n\nSelected Files:\n";
        
        foreach ($selections as $sel) {
            $content .= $sel->file_name . "\n";
        }

        $filename = "selections_" . str_replace(' ', '_', strtolower($session->title)) . ".txt";

        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    public function reset(PhotoSession $session)
    {
        $session->photoSelections()->delete();
        
        $session->update([
            'status' => 'active',
            'submitted_at' => null
        ]);

        return redirect()->back()->with('success', 'Selections reset successfully. Session unlocked.');
    }
}
