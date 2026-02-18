<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\LevelExam;
use App\Models\Notion;
use App\Models\Role;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;

class authController extends Controller
{
    public function signUp(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'second_name' => 'required|string|max:255',
            'third_name' => 'required|string|max:255',
            'fourth_name' => 'required|string|max:255',
            'full_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'birth_date' => 'required|date',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create($validated);
        $role = Role::where('name', 'user')->first();
        $user->roles()->attach($role->id);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User created successfully',
            'token' => $token,
            'user' => $user,
            'role' => $role,
        ]);
    }

    public function registerForExam(Request $request)
    {
        $rules = [
            'notion_id' => 'required|exists:notions,id',
            'specialization' => 'required',
            'occupation' => 'required',
            'place' => 'required',
            'nation_code' => 'required|unique:users,nation_code',
            'level_exam_id' => 'required|exists:level_exams,id',
        ];

        if ($request->level_exam_id == 1) {
            $rules['image'] = 'nullable|image|mimes:jpeg,png,jpg,pdf|max:2048';
        } else {
            $rules['image'] = 'required|image|mimes:jpeg,png,jpg,pdf|max:2048';
        }

        $validated = $request->validate($rules);

        $imageName = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
        }

        $request->user()->update($validated);
        $request->user()->registers()->create([
            'level_exam_id' => $validated['level_exam_id'],
            'image' => $imageName,
        ]);

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $request->user(),
        ]);
    }

    public function register()
    {
        $notions = Notion::query()->select('id', 'name')->get();
        $levelExams = LevelExam::query()->select('id', 'name')->get();
        return response()->json([
            'notions' => $notions,
            'levelExams' => $levelExams,
        ]);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        if (!Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid password',
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User logged in successfully',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'User logged out successfully',
        ]);
    }

    public function information_user(Request $request)
    {
        $notions = Notion::query()->select('id', 'name')->get();
        $user = $request->user()->with('notion')->first();
        return response()->json([
            'user' => $user,
            'notions' => $notions,
        ]);
    }

    public function profile(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'second_name' => 'required|string|max:255',
            'third_name' => 'required|string|max:255',
            'fourth_name' => 'required|string|max:255',
            'full_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'birth_date' => 'required|date',
            'email' => 'required|email|unique:users,email,' . $request->user()->id,
            'notion_id' => 'required|exists:notions,id',
            'specialization' => 'required',
            'occupation' => 'required',
            'place' => 'required',
            'nation_code' => 'required|unique:users,nation_code,' . $request->user()->id,
        ]);

        $request->user()->update($validated);

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $request->user(),
        ]);
    }
}