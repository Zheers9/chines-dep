<?php

namespace App\Http\Controllers\Api\TopAdmin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Course;
use Illuminate\Http\Request;

class AdminProgramController extends Controller
{
    public function index()
    {
        return response()->json(Program::with('courses')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'goals' => 'required|string',
            'stage_count' => 'nullable|string'
        ]);
        $program = Program::create($data);
        return response()->json($program, 201);
    }

    public function update(Request $request, $id)
    {
        $program = Program::findOrFail($id);
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'goals' => 'required|string',
            'stage_count' => 'nullable|string'
        ]);
        $program->update($data);
        return response()->json($program);
    }

    public function delete($id)
    {
        Program::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted']);
    }

    // Courses
    public function storeCourse(Request $request)
    {
        $data = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'title' => 'required|string',
            'code' => 'nullable|string',
            'stage' => 'required|integer|in:1,2,3,4',
            'semester' => 'nullable|integer|in:1,2',
            'description' => 'nullable|string',
            'credits' => 'nullable|integer'
        ]);
        $course = Course::create($data);
        return response()->json($course, 201);
    }

    public function updateCourse(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        $data = $request->validate([
            'title' => 'required|string',
            'code' => 'nullable|string',
            'stage' => 'required|integer|in:1,2,3,4',
            'semester' => 'nullable|integer|in:1,2',
            'description' => 'nullable|string',
            'credits' => 'nullable|integer'
        ]);
        $course->update($data);
        return response()->json($course);
    }

    public function deleteCourse($id)
    {
        Course::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
