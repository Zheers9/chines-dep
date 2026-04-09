<?php

namespace App\Http\Controllers\Api\TopAdmin;

use App\Http\Controllers\Controller;
use App\Models\HskSchedule;
use Illuminate\Http\Request;

class AdminHskScheduleController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => true,
            'data' => HskSchedule::orderBy('exam_date', 'desc')->get()
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'exam_date' => 'required|date',
            'deadline' => 'required|date',
            'is_active' => 'required|boolean',
        ]);

        $schedule = HskSchedule::create($data);
        return response()->json(['status' => true, 'data' => $schedule]);
    }

    public function update(Request $request, $id)
    {
        $schedule = HskSchedule::findOrFail($id);
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'exam_date' => 'required|date',
            'deadline' => 'required|date',
            'is_active' => 'required|boolean',
        ]);

        $schedule->update($data);
        return response()->json(['status' => true, 'data' => $schedule]);
    }

    public function destroy($id)
    {
        $schedule = HskSchedule::findOrFail($id);
        $schedule->delete();
        return response()->json(['status' => true, 'message' => 'Schedule deleted']);
    }
}
