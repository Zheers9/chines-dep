<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /**
     * Get the authenticated user's registration history with status.
     */
    public function myRegistrations(Request $request)
    {
        $registers = $request->user()
            ->registers()
            ->with([
                'setting:id,academic_year,start_date,end_date,active',
                'examSubType:id,name,type_exam_id,is_image',
                'examSubType.typeExam:id,name',
            ])
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($register) {
                return [
                    'id'           => $register->id,
                    'paid_status'  => $register->paid_status,
                    'confirmed'    => $register->paid_status, // alias for clarity
                    'image'        => $register->image,
                    'created_at'   => $register->created_at,
                    'academic_year' => $register->setting?->academic_year,
                    'exam_period'  => [
                        'start_date' => $register->setting?->start_date,
                        'end_date'   => $register->setting?->end_date,
                    ],
                    'exam_type'    => $register->examSubType?->typeExam?->name,
                    'exam_sub_type' => $register->examSubType?->name,
                    'requires_image' => $register->examSubType?->is_image,
                    'status_label' => $register->paid_status ? 'Confirmed ✅' : 'Pending ⏳',
                ];
            });

        return response()->json([
            'status' => true,
            'data'   => $registers,
        ]);
    }
}
