<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'email'    => 'required|max:255|string|email|unique:users',
            'name'     => 'required|max:30|string',
            'password' => 'required|max:30|string|confirmed',
            'role'     => 'required|string|in:applicant,recruiter,admin',
        ]);

        $user = User::create([
            'name'     => $validatedData['name'],
            'email'    => $validatedData['email'],
            'password' => $validatedData['password'],
            'role'     => $validatedData['role'],
        ]);

        /* OTP AUTHENTICATION (Commented for future use)
        $otp = rand(100000, 999999);
        $user->update(['otp_code' => $otp]);
        \Illuminate\Support\Facades\Log::info("OTP for newly registered user {$user->email}: {$otp}");
        return response()->json([
            'message'      => 'User created successfully. Please verify your account with the OTP sent to your email.',
            'requires_otp' => true,
            'email'        => $user->email,
        ], 201);
        */

        // Direct login if OTP is disabled
        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'message' => 'User created successfully',
            'user'    => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'role'       => $user->role,
                'avatar_url' => $user->avatar_url,
            ],
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);
        
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials. Please check your email and password.',
            ], 401);
        }

        /** @var User $user */
        $user = Auth::user();

        /* OTP AUTHENTICATION (Commented for future use)
        $otp = rand(100000, 999999);
        $user->update(['otp_code' => $otp]);
        \Illuminate\Support\Facades\Log::info("OTP for {$user->email}: {$otp}");
        Auth::logout();
        return response()->json([
            'message'      => 'Login successful. Please verify the OTP sent to your email.',
            'requires_otp' => true,
            'email'        => $user->email,
        ]);
        */

        // Direct login success if OTP is disabled
        $request->session()->regenerate();
        
        return response()->json([
            'message' => 'Login successful',
            'user'    => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'role'       => $user->role,
                'avatar_url' => $user->avatar_url,
            ],
        ]);
    }

    /* OTP AUTHENTICATION (Commented for future use)
    public function verifyOtp(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp'   => 'required|string',
        ]);

        $user = User::where('email', $validatedData['email'])
                    ->where('otp_code', $validatedData['otp'])
                    ->first();

        if (!$user) {
            return response()->json([
                'message' => 'Invalid or expired OTP. Please try again.',
            ], 401);
        }

        $user->update(['otp_code' => null]);
        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'message' => 'OTP verified. Welcome back!',
            'user'    => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'role'       => $user->role,
                'avatar_url' => $user->avatar_url,
            ],
        ]);
    }
    */

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function me(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        return response()->json([
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'role'       => $user->role,
            'avatar_url' => $user->avatar_url,
        ]);
    }
}
