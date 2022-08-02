<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PassportAuthController extends Controller
{

    public function index(Request $request)
    {
        return response()->json(['message' => auth()->user()], 200);
    }

    /**
     * Registration
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:4',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role' => 1
            ]);
            try {
                $token = $user->createToken('LaravelAuthApp')->accessToken;
                return response()->json(['message' => ['token' => $token, 'role' => $user->role]], 200);
            } catch (Exception $e) {
                return response()->json(['message' => 'No application encryption key has been specified.'], 401);
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'Toks vartotojas jau egzistuoja'], 401);
        }
    }

    /**
     * Login
     */
    public function login(Request $request)
    {
        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (auth()->attempt($data)) {
            $token = auth()->user()->createToken('LaravelAuthApp')->accessToken;
            return response()->json(['message' => ['token' => $token, 'role' => auth()->user()->role]], 200);
        } else {
            return response()->json(['message' => 'Unauthorised'], 401);
        }
    }

    // Logout

    public function logout(Request $request)
    {
        auth()->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });

        return response()->json(['message' =>  'Sėkmingai atsijungėte'], 200);
    }
}