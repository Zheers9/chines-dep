<?php

namespace App\Http\Controllers\Api\TopAdmin;

use App\Http\Controllers\Controller;
use App\Models\AboutSection;
use App\Models\DepartmentStat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminAboutController extends Controller
{
    // Public & Admin: Get all
    public function index()
    {
        return response()->json([
            'sections' => AboutSection::orderBy('display_order')->get(),
            'stats' => DepartmentStat::all()
        ]);
    }

    public function storeSection(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'video_url' => 'nullable|url',
            'image' => 'nullable|image|max:10240',
            'display_order' => 'nullable|integer'
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('about', 'public');
        }

        $section = AboutSection::create($data);
        return response()->json($section, 201);
    }

    public function updateSection(Request $request, $id)
    {
        $section = AboutSection::findOrFail($id);
        $data = $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'video_url' => 'nullable|url',
            'image' => 'nullable|image|max:10240',
            'display_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean'
        ]);

        if ($request->hasFile('image')) {
            if ($section->image) Storage::disk('public')->delete($section->image);
            $data['image'] = $request->file('image')->store('about', 'public');
        }

        $section->update($data);
        return response()->json($section);
    }

    public function deleteSection($id)
    {
        $section = AboutSection::findOrFail($id);
        if ($section->image) Storage::disk('public')->delete($section->image);
        $section->delete();
        return response()->json(['message' => 'Deleted']);
    }

    // Stats
    public function updateStat(Request $request, $id)
    {
        $stat = DepartmentStat::findOrFail($id);
        $data = $request->validate([
            'count' => 'required|integer',
            'label' => 'nullable|string',
            'icon' => 'nullable|string'
        ]);
        $stat->update($data);
        return response()->json($stat);
    }

    public function storeStat(Request $request)
    {
        $data = $request->validate([
            'category' => 'required|string',
            'count' => 'required|integer',
            'label' => 'nullable|string',
            'icon' => 'nullable|string'
        ]);
        $stat = DepartmentStat::create($data);
        return response()->json($stat, 201);
    }
}
