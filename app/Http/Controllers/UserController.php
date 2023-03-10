<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    //register
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
         'name' => 'required|string|max:255',
         'email' => 'required|string|email|max:255|unique:users',
         'password' => 'required|string|min:6|confirmed',
         ]);

        if ($validator->fails()) {
           return response()->json($validator->errors(),400);
         } 
         $user=User::create([
            'name'=>$request->name,
            'email'=> $request->email,
            'password'=>hash::make($request->password)
         ]);
         return response()->json([
            'message'=>'user registerd succesfully ',
            'user'=>$user
         ]);
    }
    //login
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
            ]);
   
           if ($validator->fails()) {
              return response()->json($validator->errors(),400);
            } 
            if(!$token = Auth()->attempt($validator->validated())){
                return response()->json([
                    'error'=>'unauthorized user'
                ]);
            }
            return $this->responseWithToken($token); 
    }
    //token generation
    protected function responseWithToken($token){
        return response()->json([
            'access_token'=>$token,
            'token_type'=>'bearer',
            'expires_in'=>auth()->factory()->getTTL()*60
        ]);
    }
    //profile
    public function profile(){
        return response()->json(auth()->user());
    }
    //token refresh
    public function refresh(){
        return $this->responseWithToken(auth()->refresh());
    }
    //logout
    public function logout(){
        auth()->logout();
        return response()->json([
            'messege'=>'User logout successfully!!!'
        ]);
    }

}

