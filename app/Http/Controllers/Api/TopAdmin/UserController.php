<?php

namespace App\Http\Controllers\Api\TopAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TopAdmin\User\StoreUserRequest;
use App\Http\Requests\Api\TopAdmin\User\UpdateUserRequest;
use App\Models\Notion;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filter = $request->input('filter');
        $show = $request->input('show');

        $users = User::with('roles:id,name','notions:id,name')->search($search)
        ->filter($filter)->orderBy('id', 'asc')->cursorPaginate($show);

        $usersDeactive = User::onlyTrashed()
        ->select('id', 'email', 'full_name', 'first_name', 'second_name', 'third_name', 'fourth_name', 'nation_code', 'deleted_at')
        ->orderBy('id', 'asc')->get();

        return response()->json([
            'users' => $users,
            'users_deactive' => $usersDeactive,
        ]);
    }

    public function create()
    {
        $roles = Role::query()->select('id', 'name')->get();
        $notions = Notion::query()->select('id', 'name')->get();

        return response()->json([
            'roles' => $roles,
            'notions' => $notions,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->validated());
        $user->roles()->sync($request->roles);
         
        return response()->json([
            'message' => 'User created successfully',
            'user' => $user, 
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with(['roles:id,name', 'registers.examSubType.typeExam','notions:id,name'])
        ->findOrFail($id); 
       
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->validated());
        $user->roles()->sync($request->roles);
        
        $roles = Role::whereIn('id', $request->roles)->select('id', 'name')->get();
        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json($user);
    }

    public function restore(string $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();
        return response()->json($user);
    }
}
