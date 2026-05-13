<?php

namespace App\Http\Controllers\Api\TopAdmin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => true,
            'data' => Slider::with('announcement')->orderBy('order')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'image' => 'required_without:announcement_id|image|mimes:jpeg,png,jpg,webp|max:2048',
            'link_url' => 'nullable|string',
            'announcement_id' => 'nullable|exists:announcements,id',
            'is_active' => 'sometimes',
            'order' => 'nullable|integer',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('sliders', 'public');
            $data['image'] = $path;
        } elseif ($request->announcement_id) {
            $announcement = \App\Models\Announcement::find($request->announcement_id);
            if ($announcement && $announcement->main_image) {
                $data['image'] = $announcement->main_image;
            }
        }
        
        // Handle boolean is_active
        $data['is_active'] = $request->has('is_active') ? filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN) : true;

        $slider = Slider::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Slider created successfully',
            'data' => $slider,
        ]);
    }

    public function update(Request $request, $id)
    {
        $slider = Slider::findOrFail($id);
        
        $data = $request->validate([
            'title' => 'string',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'link_url' => 'nullable|string',
            'announcement_id' => 'nullable|exists:announcements,id',
            'is_active' => 'sometimes',
            'order' => 'integer',
        ]);

        if ($request->hasFile('image')) {
            // Delete old custom image if it wasn't inherited from an announcement
            if ($slider->image && $slider->image !== $slider->announcement?->main_image) {
                Storage::disk('public')->delete($slider->image);
            }
            $path = $request->file('image')->store('sliders', 'public');
            $data['image'] = $path;
        } elseif ($request->announcement_id && $request->announcement_id != $slider->announcement_id) {
            // Inherit from new announcement if no image uploaded
            $announcement = \App\Models\Announcement::find($request->announcement_id);
            if ($announcement && $announcement->main_image) {
                $data['image'] = $announcement->main_image;
            }
        }

        if ($request->has('is_active')) {
            $data['is_active'] = filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN);
        }

        $slider->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Slider updated successfully',
            'data' => $slider,
        ]);
    }

    public function destroy($id)
    {
        $slider = Slider::findOrFail($id);
        if ($slider->image) {
            Storage::disk('public')->delete($slider->image);
        }
        $slider->delete();

        return response()->json([
            'status' => true,
            'message' => 'Slider deleted successfully',
        ]);
    }
}
