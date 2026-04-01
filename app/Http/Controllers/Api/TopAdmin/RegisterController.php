<?php

namespace App\Http\Controllers\Api\TopAdmin;

use App\Http\Controllers\Controller;
use App\Models\Register;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /**
     * Display a listing of all registrations.
     */
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $setting_id = $request->input('setting_id');
        $paid       = $request->input('paid');
        $show       = $request->input('show', 20);

        $registers = Register::with([
            'user:id,full_name,email,nation_code,code_id',
            'setting:id,academic_year',
            'examSubType:id,name,type_exam_id',
            'examSubType.typeExam:id,name',
        ])
        ->when($search, function ($q) use ($search) {
            $q->whereHas('user', fn($u) =>
                $u->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nation_code', 'like', "%{$search}%")
                  ->orWhere('code_id', 'like', "%{$search}%")
            );
        })
        ->when($setting_id, fn($q) => $q->where('setting_id', $setting_id))
        ->when(!is_null($paid), fn($q) => $q->where('paid_status', filter_var($paid, FILTER_VALIDATE_BOOLEAN)))
        ->orderBy('id', 'desc')
        ->cursorPaginate($show);

        return response()->json([
            'status' => true,
            'data'   => $registers,
        ]);
    }

    /**
     * Toggle the paid/confirmed status of a registration.
     */
    public function togglePaid(string $id)
    {
        $register = Register::findOrFail($id);
        $register->paid_status = !$register->paid_status;
        $register->save();

        return response()->json([
            'status'      => true,
            'message'     => 'Registration status updated successfully',
            'paid_status' => $register->paid_status,
        ]);
    }
}
