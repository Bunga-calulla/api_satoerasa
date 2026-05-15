<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // REGISTER
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed', // butuh password_confirmation
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'  => true,
            'message' => 'Register Berhasil',
            'data'    => $user,
            'token'   => $token
        ], 201);
    }

    // LOGIN
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Email atau Password salah'
            ], 401);
        }

        // Hapus token lama, buat baru (opsional: bisa dihapus kalau mau multi device)
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'  => true,
            'message' => 'Login Berhasil',
            'data'    => $user,
            'token'   => $token
        ], 200);
    }

    // LOGOUT
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Logout Berhasil'
        ], 200);
    }

    // GET PROFILE
    public function profile(Request $request)
    {
        $user = $request->user();
        $user->recipes_count = $user->recipes()->count();
        $user->favorites_count = $user->favorites()->count();

        return response()->json([
            'status'  => true,
            'message' => 'Profil User',
            'data'    => $user
        ]);
    }

    // UPDATE PROFILE
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name'         => 'sometimes|string|max:255',
            'password'     => 'sometimes|min:6|confirmed',
        ]);

        $user = $request->user();

        if ($request->name) {
            $user->name = $request->name;
        }

        if ($request->password) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return response()->json([
            'status'  => true,
            'message' => 'Profil berhasil diupdate',
            'data'    => $user
        ]);
    }
}