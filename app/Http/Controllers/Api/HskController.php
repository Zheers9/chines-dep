<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HskSchedule;
use App\Models\Setting;
use App\Models\ExamSubType;
use App\Models\ExamsRoadmapStep;
use App\Models\ExamsUserProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HskController extends Controller
{
    public function getInfo()
    {
        $activeYear = Setting::where('active', true)->first();
        $schedules = HskSchedule::where('is_active', true)
            ->orderBy('exam_date', 'asc')
            ->get();

        $levels = ExamSubType::whereHas('typeExam', function($query) {
                $query->where('name', 'HSK');
            })->get();

        return response()->json([
            'status' => true,
            'academic_year' => $activeYear ? $activeYear->academic_year : date('Y'),
            'schedules' => $schedules,
            'levels' => $levels
        ]);
    }

    public function getRoadmap($sub_type_id)
    {
        $user = Auth::guard('sanctum')->user();
        
        $steps = ExamsRoadmapStep::where('exam_sub_type_id', $sub_type_id)
            ->with(['files'])
            ->orderBy('order')
            ->get();

        if ($user) {
            $completedIds = ExamsUserProgress::where('user_id', $user->id)
                ->pluck('roadmap_step_id')
                ->toArray();

            $steps->map(function($step) use ($completedIds) {
                $step->is_completed = in_array($step->id, $completedIds);
                return $step;
            });
        }

        return response()->json(['status' => true, 'data' => $steps]);
    }

    public function getStep($id)
    {
        $step = ExamsRoadmapStep::with(['sections.questions.options', 'sections.questions.answers', 'questions.options', 'questions.answers', 'files'])->findOrFail($id);
        return response()->json(['status' => true, 'data' => $step]);
    }

    public function completeStep(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $progress = ExamsUserProgress::updateOrCreate(
            ['user_id' => $user->id, 'roadmap_step_id' => $id],
            [
                'score' => $request->input('score'),
                'status' => 'completed',
                'completed_at' => now()
            ]
        );

        return response()->json(['status' => true, 'data' => $progress]);
    }
}
