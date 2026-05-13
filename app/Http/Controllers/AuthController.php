<?php
namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller {
   
    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);


        $user = User::where('email', $request->email)->first();


        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Email atau Password salah'
            ], 401);
        }


        $token = $user->createToken('auth_token')->plainTextToken;


        return response()->json([
            'status' => true,
            'message' => 'Login Berhasil',
            'data' => $user,
            'token' => $token
        ], 200);
    }


    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => true,
            'message' => 'Logout Berhasil'
        ], 200);
    }
}