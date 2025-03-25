<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validateData = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:8',
        ]);

        $user = User::create($validateData);
        $token = $user->createToken('token')->plainTextToken;
        return response()->json([
            'message' => 'Usuario creado',
            'data' => [
                'user' => $user,
                'token' => $token
            ],
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!Hash::check($request->password, $user->password))
            return response()->json(['message' => 'Contrasena incorrecta.'], 401);

        $token = $user->createToken('token')->plainTextToken;
        return response()->json([
            'message' => 'Sesion iniciada.',
            'data' => [
                'user' => $user,
                'token' => $token
            ],
        ], 200);
    }

    public function checkAuth(Request $request){
        $user = $request->user();
        return response()->json([
            'message'=> 'Usuario autenticado.',
            'data'=> [
                'user'=> $user
            ]
        ],201);
    }
}
