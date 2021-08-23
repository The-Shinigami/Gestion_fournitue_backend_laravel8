<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use \Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
   public function checkUtilisateurRole(Request $request){
        $user = Auth::guard('utilisateur-api')->user();       
        return  ["utilisateur" => $user];
    }
    public function authenticate(Request $request)
    {
        $credentials = $request->only('login', 'password');

        //valid credential
        $validator = Validator::make($credentials, [
            'login' => 'required',
            'password' => 'required'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['message' => 'Donneés incorrect ,le login et le password sont obligatoire',], 200);
        }

        //Request is validated
        //Crean token
        
        try {
            if (!$token = Auth::guard('utilisateur-api')->attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Donneés incorrect , Verfier votre login et password !!',
                ]);
            }
        } catch (JWTException $e) {
            return $credentials;
            return response()->json([
                'success' => false,
                'message' => 'Donneés incorrect , Verfier votre login et password',
            ], 500);
        }

        //Token created, return with success response and jwt token
        return response()->json([
            'success' => true,
            'token' => $token,
            'utilisateur' => Auth::guard('utilisateur-api')->user()
        ]);
    }


    public function logout(Request $request)
    {
        //valid credential
        $validator = Validator::make($request->only('token'), [
            'token' => 'required'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Request is validated, do logout        
        try {
            JWTAuth::invalidate($request->token);

            return response()->json([
                'success' => true,
                'message' => 'User has been logged out'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, user cannot be logged out'
            ]);
        }
    }

    public function get_user(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);

        $user = JWTAuth::authenticate($request->token);

        return response()->json(['user' => $user]);
    }

}
