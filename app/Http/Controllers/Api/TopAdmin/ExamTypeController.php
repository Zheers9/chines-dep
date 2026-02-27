<?php

namespace App\Http\Controllers\Api\TopAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TopAdmin\ExamType\StoreExamTypeRequest;
use App\Http\Requests\Api\TopAdmin\ExamType\UpdateExamTypeRequest;
use App\Models\TypeExam;
use Illuminate\Http\Request;

class ExamTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $examTypes = TypeExam::query()->select('id', 'name')->get();
        return response()->json($examTypes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExamTypeRequest $request)
    { 
        TypeExam::create($request->validated());
        return response()->json([
            'message' => 'Exam type created successfully',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExamTypeRequest $request, string $id)
    {
        $examType = TypeExam::findOrFail($id);
        $examType->update($request->validated());
        return response()->json([
            'message' => 'Exam type updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $examType = TypeExam::findOrFail($id);
        $examType->delete();
        return response()->json([
            'message' => 'Exam type deleted successfully',
        ]);
    }
}
