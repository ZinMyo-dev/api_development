<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Api\HttpResponseTrait;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    use HttpResponseTrait;
    public function register(Request $request)  
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users,email',
            'password' => 'required|string|max:12',
        ]);

        if($validator->fails()){
            return $this->errorResponse(400, $validator->errors());
        }

        $user = User::create([
            'name' => $request->name,
            "email" => $request->email,
            "password" => bcrypt($request->password),
        ]);

        // $token = Auth::user()->createToken("register token");

        return $this->successResponse(201, "user registered successfully", ["user" => $user]);
    }

    public function login(Request $request)
    {
        $cred = $request->only('email', "password");

        // if user tries to login with wrong email or password
        if(! Auth::attempt($cred)){
            return $this->errorResponse(400, "invalid email or password");
        }

        // if user if authenticated
        $user = Auth::user();

        // storing token into database 
        $token = Auth::user()->createToken('login token');

        return $this->successResponse(200, "user logged in successfully", ['token' => $token, 'user' => new UserResource($user)]);
    }

    public function logout()
    {
            $user = Auth::user();
            $user->tokens()->delete();
            return $this->successResponse(200, "user logged out successfully", );
    }
}
