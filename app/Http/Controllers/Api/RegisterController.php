<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CreateUser;
use App\Http\Resources\UserResource;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class RegisterController extends Controller
{
    public function addAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:2',
            'email' => 'required|email|unique:admins',
            'password' => 'required|min:6',
        ]);
        Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        return response()->json(['message' => 'Registered successfully']);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = Admin::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => new UserResource($user),
            'token' => $token
        ]);
    }

public function createUser(Request $request)
{
    $validated = $request->validate([
        'firstname' => 'required|string',
        'lastname' => 'required|string',
        'dob' => 'required|date',
        'gender' => 'required',
        'phone' => 'required|digits:10',
        'profile' => 'required|image|mimes:jpg,png,jpeg|max:2048',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6',
    ]);

    if ($request->hasFile('profile')) {
        $file = $request->file('profile');
        $filename = time() . '_' . $file->getClientOriginalName();
        $validated['profile'] = $file->storeAs('uploads', $filename, 'public');
    }

    $validated['password'] = Hash::make($validated['password']);

    $user = User::create($validated);

    return response()->json([
        'success' => true,
        'message' => 'Registered successfully',
        'user' => new CreateUser($user),
    ]);
}

    public function getUsers(){
    return response()->json([
        'success'=>'user fetched succesfully',
        'users'=>User::all(),
    ]);
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
public function deleteUser(Request $request,$id)
{
    $user = User::find($id);

    if (!$user) {
        return response()->json(['message' => 'User not found or already deleted'], 404);
    }
        if ($user->profile && Storage::disk('public')->exists($user->profile)) {
            Storage::disk('public')->delete($user->profile);
        }


    $user->delete();
    return response()->json([
        'success' => true,
        'message' => 'User Deleted Successfully',
    ]);
}
public function updateUser(Request $request,$id)
{
    $user = User::find($id);

    if (!$user) {
        return response()->json(['message' => 'User not found or already deleted'], 404);
    }
    $validated = $request->validate([
        'firstname' => 'required|string',
        'lastname' => 'required|string',
        'dob' => 'required|date',
        'gender' => 'required|string',
        'phone' => 'required|digits:10',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'profile' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);
    if ($request->hasFile('profile')) {
        if ($user->profile && Storage::disk('public')->exists($user->profile)) {
            Storage::disk('public')->delete($user->profile);
        }
        $file = $request->file('profile');
        $filename = time() . '_' . $file->getClientOriginalName();
        $validated['profile'] = $file->storeAs('uploads', $filename, 'public');
    }
    $user->update($validated);
    return response()->json([
        'success' => true,
        'message' => 'Profile updated successfully',
        'user' => new CreateUser($user)
    ]);
}
}
