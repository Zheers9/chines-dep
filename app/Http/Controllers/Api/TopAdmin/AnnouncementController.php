<?php

namespace App\Http\Controllers\Api\TopAdmin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AnnouncementImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AnnouncementController extends Controller
{
    public function index()
    {
        return response()->json(Announcement::with('images')->latest()->get());
    }

    public function show($id)
    {
        return response()->json(Announcement::with('images')->findOrFail($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'type' => 'required|in:activity,visit,event',
            'event_date' => 'nullable|date',
            'main_image' => 'nullable|image|max:20480', // 20MB
            'gallery_images.*' => 'nullable|image|max:20480'
        ]);

        return DB::transaction(function () use ($request, $data) {
            $mainPath = null;
            if ($request->hasFile('main_image')) {
                $mainPath = $request->file('main_image')->store('announcements', 'public');
            }

            $announcement = Announcement::create([
                'title' => $data['title'],
                'content' => $data['content'],
                'type' => $data['type'],
                'event_date' => $data['event_date'],
                'main_image' => $mainPath,
            ]);

            if ($request->hasFile('gallery_images')) {
                foreach ($request->file('gallery_images') as $file) {
                    $path = $file->store('announcements/gallery', 'public');
                    AnnouncementImage::create([
                        'announcement_id' => $announcement->id,
                        'image_path' => $path
                    ]);
                }
            }

            return response()->json($announcement->load('images'), 201);
        });
    }

    public function update(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);

        $data = $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'type' => 'required|in:activity,visit,event',
            'event_date' => 'nullable|date',
            'main_image' => 'nullable|image|max:20480',
            'gallery_images.*' => 'nullable|image|max:20480'
        ]);

        return DB::transaction(function () use ($request, $data, $announcement) {
            $updateData = [
                'title' => $data['title'],
                'content' => $data['content'],
                'type' => $data['type'],
                'event_date' => $data['event_date'],
            ];

            if ($request->hasFile('main_image')) {
                if ($announcement->main_image) Storage::disk('public')->delete($announcement->main_image);
                $updateData['main_image'] = $request->file('main_image')->store('announcements', 'public');
            }

            $announcement->update($updateData);

            if ($request->hasFile('gallery_images')) {
                foreach ($request->file('gallery_images') as $file) {
                    $path = $file->store('announcements/gallery', 'public');
                    AnnouncementImage::create([
                        'announcement_id' => $announcement->id,
                        'image_path' => $path
                    ]);
                }
            }

            return response()->json($announcement->load('images'));
        });
    }

    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        
        // Clean up storage
        if ($announcement->main_image) Storage::disk('public')->delete($announcement->main_image);
        foreach ($announcement->images as $img) {
            Storage::disk('public')->delete($img->image_path);
        }

        $announcement->delete();
        return response()->json(['message' => 'Announcement deleted']);
    }
}
