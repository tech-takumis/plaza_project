<?php

namespace App\Http\Controllers;

use App\Events\AnnouncementEvent;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\DB;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::all();
        return response()->json($announcements);
    }

    public function latest()
    {
        $announcement = DB::select('select * from announcements order by created_at desc limit 1');

        return response()->json($announcement);
    }


    // Store a new announcement
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:255',
            'event_type' => 'required|string|max:255',
            'when' => 'required|date',
            'where' => 'nullable|string|max:255',
        ]);

        $announcement = Announcement::create($request->all());

        Broadcast(new AnnouncementEvent($announcement));

        return response()->noContent();
    }
    // Show a single announcement
    public function show($id)
    {
        $announcement = Announcement::findOrFail($id);
        return response()->json($announcement);
    }

    // Update an existing announcement
    public function update(Request $request, $id)
    {
        $request->validate([
            'message' => 'sometimes|string|max:255',
            'event_type' => 'sometimes|string|max:255',
            'when' => 'sometimes|date',
            'where' => 'nullable|string|max:255',
        ]);

        $announcement = Announcement::findOrFail($id);
        $announcement->update($request->all());

        return response()->json($announcement);
    }

    // Delete an announcement
    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        return response()->json(['message' => 'Announcement deleted successfully']);
    }

}
