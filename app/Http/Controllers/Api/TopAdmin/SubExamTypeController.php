<?php

namespace App\Http\Controllers\Api\TopAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TopAdmin\SubExamType\StoreSubExamTypeRequest;
use App\Http\Requests\Api\TopAdmin\SubExamType\UpdateSubExamTypeRequest;
use App\Models\ExamSubType;
use App\Models\TypeExam;
use Illuminate\Http\Request;

class SubExamTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subExamTypes = ExamSubType::query()->with('typeExam:id,name')->select('id', 'name','type_exam_id')->get();
        return response()->json($subExamTypes);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $examTypes = TypeExam::query()->select('id', 'name')->get();
        return response()->json($examTypes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubExamTypeRequest $request)
    {
        ExamSubType::create($request->validated());
        return response()->json([
            'message' => 'Sub exam type created successfully',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubExamTypeRequest $request, string $id)
    {
        $subExamType = ExamSubType::findOrFail($id);
        $subExamType->update($request->validated());
        return response()->json([
            'message' => 'Sub exam type updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $subExamType = ExamSubType::findOrFail($id);
        $subExamType->delete();
        return response()->json([
            'message' => 'Sub exam type deleted successfully',
        ]);
    }
}
