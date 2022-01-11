<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    //

    public function __construct()
    {
        $this->middleware('auth.api', [
            'except' => [
                'login', 'register'
            ]
        ]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!$token = auth()->attempt($validator->validate())) {
            return response()->json([
                'error' => 'Unauthorized'
            ], 401);
        }

        return $this->createNewToken($token);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create(array_merge(
            $validator->validate(),
            ['password' => bcrypt($request->password)]
        ));

        return response()->json([
            'message' => 'User successfully registerd',
            'user' => $user
        ], 201);
    }

    public function logout()
    {
        auth()->logout();
        return response()->json([
            'message' => "Logout Successfully"
        ]);
    }

    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }

    public function userProfile()
    {
        return response()->json(auth()->user());
    }

    public function createNewToken($token){
        return response() ->json([
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => '86400',
        'user' =>auth()->user()
        ]);
    }
}
