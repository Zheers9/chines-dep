<?php

namespace App\Http\Controllers\Api\TopAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TopAdmin\Notion\StoreNotionRequest;
use App\Http\Requests\Api\TopAdmin\Notion\UpdateNotionRequest;
use App\Models\Notion;
use Illuminate\Http\Request;

class NotionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notions = Notion::query()->select('id','name_en','name_ku','name_ar')->get();
        return response()->json([
            'status' => true,
            'message' => 'Notions fetched successfully',
            'data' => $notions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNotionRequest $request)
    {
        Notion::create($request->validated());
        return response()->json([
            'status' => true,
            'message' => 'Notion created successfully'
        ]);
    } 

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNotionRequest $request, string $id)
    {
        $notion = Notion::findOrFail($id);
        $notion->update($request->validated());
        return response()->json([
            'status' => true,
            'message' => 'Notion updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $notion = Notion::findOrFail($id);
        $notion->delete();
        return response()->json([
            'status' => true,
            'message' => 'Notion deleted successfully'
        ]);
    }
}
