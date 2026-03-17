<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
 public function register(Request $request)
 {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        //'password' => Hash::make($request->password),
        'password' => $request->password,
    ]);
    $token = $user->createToken('auth_token')->plainTextToken;
    return response()->json([
        'token' => $token,
        'user'  => $user,
    ], 201);
 }   


 public function login(Request $request){
    $request->validate([
        'email' => 'required|string|email',
        'password' => 'required|string',
    ]);

    $user = User::where('email', $request->email)->first();

    if(!$user || !Hash::check($request->password, $user->password)){
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $user->update([
        'status' => 'online',
        'last_seen_at' => now(),
    ]);

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'token' => $token,
        'user'  => $user,
    ], 200);

 }

 public function logout(Request $request)
 {
     // Set user offline
     $request->user()->update([
         'status'       => 'offline',
         'last_seen_at' => now(),
     ]);
     $request->user()->currentAccessToken()->delete();
     return response()->json(['message' => 'Logged out successfully']);
 }
 public function me(Request $request)
 {
     return response()->json($request->user());
 }
}
