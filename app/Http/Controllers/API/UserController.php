<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum', ['except' => ['register', 'verifyOtp', 'login']]);
    }

    /**
     * Register user with phone (OTP)
     */
    public function register(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:15|unique:users,phone',
        ]);

        // Create user
        $user = User::create([
            'phone' => $request->phone,
        ]);

        // Generate 6-digit OTP
        $otp = rand(100000, 999999);

        // Save OTP temporarily in cache (5 minutes)
        cache()->put('otp_' . $user->phone, $otp, now()->addMinutes(5));

        // TODO: Send SMS here in production

        return response()->json([
            'status' => true,
            'message' => 'OTP sent to phone',
            'phone' => $user->phone,
            'otp_for_demo' => $otp  // REMOVE in production
        ]);
    }

    /**
     * Verify OTP and generate token
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|exists:users,phone',
            'otp' => 'required|digits:6'
        ]);

        $otp = cache()->get('otp_' . $request->phone);

        if (!$otp || $otp != $request->otp) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired OTP'
            ], 422);
        }

        $user = User::where('phone', $request->phone)->first();

        // OTP verified → create token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Remove OTP from cache
        cache()->forget('otp_' . $request->phone);

        return response()->json([
            'status' => true,
            'message' => 'OTP verified successfully',
            'user' => $user,
            'token' => $token
        ]);
    }

    /**
     * Login user (phone only) – optional if OTP
     */
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|exists:users,phone',
        ]);

        $user = User::where('phone', $request->phone)->first();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Optional: list all users
     */
    public function index()
    {
        $users = User::all();

        return response()->json([
            'status' => true,
            'users' => $users
        ]);
    }
}
