<?php

namespace App\Http\Controllers\Api\TopAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TopAdmin\SubExamLevel\StoreSubExamLevelRequest;
use App\Http\Requests\Api\TopAdmin\SubExamLevel\UpdateSubExamLevelRequest;
use App\Models\ExamSubLevel;
use App\Models\ExamSubType;

class ExamSubLevelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subExamLevels = ExamSubLevel::query()->with(['examSubType:id,name,type_exam_id','examSubType.typeExam:id,name'])
        ->select('id','name','exam_sub_type_id')->orderby('id','desc')->get();
        return response()->json([
            'subExamLevels' => $subExamLevels,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $examSubTypes = ExamSubType::query()->with('typeExam:id,name')->select('id','name','type_exam_id')->get();
        return response()->json([
            'examSubTypes' => $examSubTypes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubExamLevelRequest $request)
    {
        ExamSubLevel::create($request->all());
        return response()->json([
            'message' => 'Sub Exam Level created successfully',
        ]);
    }  
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubExamLevelRequest $request, string $id)
    {
        $subExamLevel = ExamSubLevel::findOrFail($id);
        $subExamLevel->update($request->all());
        return response()->json([
            'message' => 'Sub Exam Level updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $subExamLevel = ExamSubLevel::findOrFail($id);
        $subExamLevel->delete();
        return response()->json([
            'message' => 'Sub Exam Level deleted successfully',
        ]);
    }
}
