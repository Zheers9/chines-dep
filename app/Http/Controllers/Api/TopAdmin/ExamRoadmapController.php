<?php

namespace App\Http\Controllers\Api\TopAdmin;

use App\Http\Controllers\Controller;
use App\Models\RoadmapStep;
use App\Traits\UploadsFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExamRoadmapController extends Controller
{
    use UploadsFile;

    public function indexBySubType(string $subTypeId)
    {
        $steps = RoadmapStep::query()
            ->where('exam_sub_type_id', $subTypeId)
            ->with(['questions.options', 'questions.answers'])
            ->orderBy('order')
            ->get()
            ->map(function (RoadmapStep $step) {
                if ($step->video_url && !str_starts_with($step->video_url, 'http')) {
                    $step->video_url = Storage::url($step->video_url);
                }
                if ($step->file_path && !str_starts_with($step->file_path, 'http')) {
                    $step->file_path = Storage::url($step->file_path);
                }
                return $step;
            });

        return response()->json(['data' => $steps]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'exam_sub_type_id' => ['required', 'exists:exam_sub_types,id'],
            'pre_node_id' => ['nullable', 'exists:roadmap_steps,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:resource,exam'],
            'difficulty' => ['required', 'in:easy,medium,hard'],
            'total_marks' => ['nullable', 'integer', 'min:0'],
            'order' => ['nullable', 'integer', 'min:0'],
            'color' => ['nullable', 'string', 'max:20'],
            'video_file' => ['nullable', 'file', 'mimes:mp4,mov,avi,webm', 'max:51200'],
            'resource_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:20480'],
        ]);

        if ($request->hasFile('video_file')) {
            $stored = $this->storeUploadedFile($request->file('video_file'), 'roadmap/videos');
            $data['video_url'] = $stored['path'];
        }
        if ($request->hasFile('resource_file')) {
            $stored = $this->storeUploadedFile($request->file('resource_file'), 'roadmap/resources');
            $data['file_path'] = $stored['path'];
        }

        $step = RoadmapStep::create($data);

        return response()->json([
            'message' => 'Roadmap step created successfully',
            'data' => $step,
        ]);
    }

    public function destroy(string $id)
    {
        $step = RoadmapStep::findOrFail($id);
        $step->delete();

        return response()->json(['message' => 'Roadmap step deleted successfully']);
    }
}
