<?php

namespace App\Http\Controllers\Api\TopAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TopAdmin\Setting\StoreSettingRequest;
use App\Http\Requests\Api\TopAdmin\Setting\UpdateSettingRequest;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = Setting::query()
        ->select('academic_year','active','start_date','end_date')->orderBy('id','desc')
        ->cursorPaginate(10);
        return response()->json([
            'status' => true,
            'message' => 'Settings retrieved successfully',
            'data' => $settings,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSettingRequest $request)
    {
        Setting::create($request->validated());
        
        return response()->json([
            'status' => true,
            'message' => 'Setting created successfully',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSettingRequest $request, string $id)
    {
        $setting = Setting::findOrFail($id); 
        $setting->update($request->validated()); 
        return response()->json([
            'status' => true,
            'message' => 'Setting updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $setting = Setting::findOrFail($id); 
        $setting->delete(); 
        return response()->json([
            'status' => true,
            'message' => 'Setting deleted successfully',
        ]);
    }
}
