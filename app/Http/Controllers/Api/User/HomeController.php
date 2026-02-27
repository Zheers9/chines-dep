<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $settings = Setting::query()->orderby('academic_year','desc')->first();
        return response()->json([
            'settings' => $settings,
        ]);
    }
}
