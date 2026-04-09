<?php

namespace App\Http\Controllers\Api\TopAdmin;

use App\Http\Controllers\Controller;
use App\Models\fee_payment;
use App\Models\Register;
use Illuminate\Http\Request;

class FeePaymentController extends Controller
{
    /**
     * Display a listing of all fee payments.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $show   = $request->input('show', 20);

        $payments = fee_payment::with([
            'user:id,full_name,email',
            'register:id,setting_id,exam_sub_type_id',
            'register.setting:id,academic_year',
            'register.examSubType:id,name',
            'fee:id,payment_amount'
        ])
        ->when($search, function ($q) use ($search) {
            $q->whereHas('user', fn($u) => 
                $u->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
            )->orWhere('voucher_num', 'like', "%{$search}%");
        })
        ->orderBy('id', 'desc')
        ->paginate($show);

        return response()->json([
            'status' => true,
            'data'   => $payments,
        ]);
    }

    /**
     * Store a newly created fee payment.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'register_id' => 'required|exists:registers,id',
            'pay'         => 'required|string',
            'voucher_num' => 'nullable|string',
            'comment'     => 'nullable|string',
        ]);

        $register = Register::findOrFail($data['register_id']);
        
        $payment = fee_payment::create([
            'register_id'      => $register->id,
            'user_id'          => $register->user_id,
            'exam_sub_type_id' => $register->exam_sub_type_id,
            'pay'              => $data['pay'],
            'voucher_num'      => $data['voucher_num'],
            'comment'          => $data['comment'],
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Payment recorded successfully',
            'data'    => $payment,
        ]);
    }

    /**
     * Remove a fee payment record.
     */
    public function destroy(string $id)
    {
        $payment = fee_payment::findOrFail($id);
        $payment->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Payment record deleted',
        ]);
    }
}
