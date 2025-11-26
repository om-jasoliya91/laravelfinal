<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CreateUser;
use App\Http\Resources\UserResource;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
        'password' => 'required|min:6', // Added minimum length validation
    ]);

    // Handle profile image upload efficiently
    if ($request->hasFile('profile')) {
        $file = $request->file('profile');
        $filename = time() . '_' . $file->getClientOriginalName();
        // The storeAs method returns the path
        $validated['profile'] = $file->storeAs('uploads', $filename, 'public');
    }

    // Hash the password before creation
    $validated['password'] = Hash::make($validated['password']);

    // Create the user using the validated data array
    $user = User::create($validated);

    return response()->json([
        'success' => true,
        'message' => 'Registered successfully',
        'user' => new CreateUser($user), // Use your resource if needed
        // 'user' => $user, // Or return the user object directly
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
}