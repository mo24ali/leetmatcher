<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
class AuthController extends Controller
{
    public function register(Request $request){
        $validatedData = $request->validate([
            "email" => "required|max:255|string|unique:users",
            "name" => "required|max:30|string",
            "password" => "required|max:30|string",
            "password_confirmation" => "required|max:30|string",
        ]);
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => $validatedData['password']
        ]);
        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ], 201);
    }
}
