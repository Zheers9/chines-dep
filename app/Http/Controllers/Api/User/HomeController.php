<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $settings = Setting::where('academic_year', 'desc')->first();
        return response()->json([
            'settings' => $settings,
        ]);
    }
}
