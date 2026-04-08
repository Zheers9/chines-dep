<?php

namespace App\Http\Controllers\Api\TopAdmin;

use App\Http\Controllers\Controller;
use App\Models\StaffMember;
use App\Models\StaffGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AdminStaffMemberController extends Controller
{
    public function index()
    {
        return response()->json(StaffMember::with('gallery')->orderBy('display_order')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:lecturer,staff',
            'name' => 'required|string',
            'title' => 'nullable|string',
            'certificate' => 'nullable|string',
            'role' => 'nullable|string',
            'description' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'image' => 'nullable|image|max:10240',
            'gallery_images.*' => 'nullable|image|max:10240'
        ]);

        return DB::transaction(function () use ($request, $data) {
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('staff', 'public');
            }

            $staff = StaffMember::create($data);

            if ($request->hasFile('gallery_images')) {
                foreach ($request->file('gallery_images') as $file) {
                    $path = $file->store('staff/gallery', 'public');
                    StaffGallery::create([
                        'staff_member_id' => $staff->id,
                        'image_path' => $path
                    ]);
                }
            }

            return response()->json($staff->load('gallery'), 201);
        });
    }

    public function update(Request $request, $id)
    {
        $staff = StaffMember::findOrFail($id);
        $data = $request->validate([
            'type' => 'required|in:lecturer,staff',
            'name' => 'required|string',
            'title' => 'nullable|string',
            'certificate' => 'nullable|string',
            'role' => 'nullable|string',
            'description' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'image' => 'nullable|image|max:10240',
            'gallery_images.*' => 'nullable|image|max:10240'
        ]);

        return DB::transaction(function () use ($request, $data, $staff) {
            if ($request->hasFile('image')) {
                if ($staff->image) Storage::disk('public')->delete($staff->image);
                $data['image'] = $request->file('image')->store('staff', 'public');
            }

            $staff->update($data);

            if ($request->hasFile('gallery_images')) {
                foreach ($request->file('gallery_images') as $file) {
                    $path = $file->store('staff/gallery', 'public');
                    StaffGallery::create([
                        'staff_member_id' => $staff->id,
                        'image_path' => $path
                    ]);
                }
            }

            return response()->json($staff->load('gallery'));
        });
    }

    public function delete($id)
    {
        $staff = StaffMember::findOrFail($id);
        if ($staff->image) Storage::disk('public')->delete($staff->image);
        foreach ($staff->gallery as $img) {
            Storage::disk('public')->delete($img->image_path);
        }
        $staff->delete();
        return response()->json(['message' => 'Deleted']);
    }

    public function deleteGalleryImage($id)
    {
        $img = StaffGallery::findOrFail($id);
        Storage::disk('public')->delete($img->image_path);
        $img->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
