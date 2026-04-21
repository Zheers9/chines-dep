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
        $is_accepted = $request->input('is_accepted');
        $paid       = $request->input('paid');
        $exam_sub_type_id = $request->input('exam_sub_type_id');
        $show       = $request->input('show', 20);

        $registers = Register::with([
            'user',
            'setting:id,academic_year',
            'examSubType:id,name,type_exam_id',
            'examSubType.typeExam:id,name',
            'payments'
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
        ->when(!is_null($is_accepted), fn($q) => $q->where('is_accepted', filter_var($is_accepted, FILTER_VALIDATE_BOOLEAN)))
        ->when(!is_null($paid), function($q) use ($paid) {
            if (filter_var($paid, FILTER_VALIDATE_BOOLEAN)) {
                $q->whereHas('payments');
            } else {
                $q->whereDoesntHave('payments');
            }
        })
        ->when($exam_sub_type_id, fn($q) => $q->where('exam_sub_type_id', $exam_sub_type_id))
        ->orderBy('id', 'desc')
        ->cursorPaginate($show);

        return response()->json([
            'status' => true,
            'data'   => $registers,
        ]);
    }

    /**
     * Toggle the accepted/confirmed status of a registration.
     */
    public function toggleAccepted(string $id)
    {
        $register = Register::findOrFail($id);
        $register->is_accepted = !$register->is_accepted;
        $register->save();

        return response()->json([
            'status'      => true,
            'message'     => 'Registration status updated successfully',
            'is_accepted' => $register->is_accepted,
        ]);
    }
}
