<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    //

    public function __construct(){
        $this->middleware('auth.api', [
            'except' =>[
                'login' ,'register'
            ]
        ]);
    }

    public function login(Request $request){
      $validator = Validator::make($request->all(),[
           'email' => 'required|email',
           'password' => 'required|string|6'
      ]);

      if($validator->fails()){
        return response()->json($validator->errors(),422);
      }

      if(!$token = auth()->attempt($validator->validate())){
       return response()->json([
           'error' => 'Unauthorized'
       ],401);
      }

      return $this->createNewToken($token);
    }

    public function register(Request $request){

    }


}
