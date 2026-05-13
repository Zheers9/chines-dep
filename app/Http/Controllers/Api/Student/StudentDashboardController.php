<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\Register;
use App\Models\fee_payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    /**
     * Get the student dashboard data (registrations and payments).
     */
    public function index()
    {
        $user = Auth::user();

        // Fetch registration history with relations
        $registrations = Register::where('user_id', $user->id)
            ->with([
                'examSubType:id,name,type_exam_id',
                'examSubType.typeExam:id,name',
                'setting:id,academic_year',
                'payments'
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        // Fetch payment history
        $payments = fee_payment::where('user_id', $user->id)
            ->with([
                'examSubType:id,name',
                'fee:id,payment_amount'
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => [
                'user' => [
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'nation_code' => $user->nation_code,
                ],
                'registrations' => $registrations,
                'payments' => $payments,
            ]
        ]);
    }
}
