<?php

namespace App\Http\Controllers\Api\AccountingRegister;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AccountingRegister\StoreFeePaymentRequest;
use App\Http\Requests\Api\AccountingRegister\UpdateFeePaymentRequest;
use App\Models\ExamSubType;
use App\Models\fee;
use App\Models\fee_payment;
use App\Models\Register;
use App\Models\Setting;
use App\Models\User;

class FeePaymentController extends Controller
{
    public function index()
    {
        $feePayments = fee_payment::query()
        ->with('fee:id,payment_amount', 'user:id,first_name,second_name,third_name,fourth_name', 'examSubType:id,name')
        ->select('id', 'fee_id', 'user_id', 'exam_sub_type_id', 'voucher_number', 'pay')
        ->paginate(10);
        return response()->json([
            'feePayments' => $feePayments,
        ]);
    }
    public function show(string $id)
    {
        $feePayment = fee_payment::query()->findOrFail($id);
        return response()->json([
            'feePayment' => $feePayment,
        ]);
    }
    public function destroy(string $id)
    {
        $feePayment = fee_payment::query()->findOrFail($id);
        $feePayment->delete();
        return response()->json([
            'message' => 'Fee payment deleted successfully',
        ]);
    }
    public function create()
    {
        $settings = Setting::query()->select('id', 'academic_year')->get();
        $fees = fee::query()->with('examSubType:id,name', 'setting:id,academic_year')->select('id', 'payment_amount', 'exam_sub_type_id', 'setting_id', 'payment_amount')->get();
        $users = User::query()->with('registers:id,user_id,exam_sub_type_id,paid_status')->select('id', 'first_name', 'second_name', 'third_name', 'fourth_name','email')->get();
        $examSubTypes = ExamSubType::query()->with('typeExam:id,name')->select('id', 'name', 'type_exam_id')->get();

        return response()->json([
            'settings' => $settings,
            'fees' => $fees,
            'users' => $users,
            'examSubTypes' => $examSubTypes,
        ]);
    }
    public function store(StoreFeePaymentRequest $request)
    {
        $setting = Setting::query()->orderBy('id', 'desc')->first(); 
        fee_payment::create($request->validated());

        Register::where('user_id', $request->user_id)
        ->where('exam_sub_type_id', $request->exam_sub_type_id)
        ->where('paid_status', 0)
        ->where('setting_id', $setting->id)
        ->update([
            'paid_status' => true,
        ]);
        return response()->json([
            'message' => 'Fee payment created successfully',
        ]);
    }
    public function update(UpdateFeePaymentRequest $request, string $id)
    {
        $setting = Setting::query()->orderBy('id', 'desc')->first();
        $feePayment = fee_payment::query()->findOrFail($id);
        $feePayment->update($request->validated());

        Register::where('user_id', $request->user_id)
        ->where('exam_sub_type_id', $request->exam_sub_type_id) 
        ->where('setting_id', $setting->id)
        ->update([
            'paid_status' => true,
        ]);

        return response()->json([
            'message' => 'Fee payment updated successfully',
        ]);
    }
}
