<?php

namespace App\Http\Controllers\Api\TopAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TopAdmin\Fee\StoreFeeRequest;
use App\Http\Requests\Api\TopAdmin\Fee\UpdateFeeRequest;
use App\Models\ExamSubType;
use App\Models\fee;
use App\Models\Setting;

class FeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fees = fee::query()->with('examSubType:id,name', 'setting:id,academic_year')->select('id', 'payment_amount', 'exam_sub_type_id', 'setting_id', 'payment_amount')->get();
        return response()->json($fees);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $examSubTypes = ExamSubType::query()->with('typeExam:id,name')->select('id', 'name', 'type_exam_id')->get();
        $settings = Setting::query()->select('id', 'academic_year')->get();
        return response()->json([
            'examSubTypes' => $examSubTypes,
            'settings' => $settings,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFeeRequest $request)
    {
        $request->user()->fees()->create($request->validated());
        return response()->json([
            'message' => 'Fee created successfully',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFeeRequest $request, string $id)
    {
        $fee = fee::findOrFail($id);
        $request->user()->fees()->update($request->validated());
        return response()->json([
            'message' => 'Fee updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $fee = fee::findOrFail($id);
        $fee->delete();
        return response()->json([
            'message' => 'Fee deleted successfully',
        ]);
    }
}
