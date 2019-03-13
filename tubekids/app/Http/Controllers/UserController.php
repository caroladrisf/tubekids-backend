<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    /**
     * Authenticate an user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error'=>'Invalid credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error'=>'Could not create token'], 500);
        }
        $user = JWTAuth::user();
        return response()->json(compact('token', 'user'));
    }

    public function logout()
    {
        JWTAuth::invalidate();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function register(UserRequest $request)
    {
        $user = new User();
        $user->fill($request->except('password'));
        $user->fill(['password' => Hash::make($request->input('password'))])->save();
        
        $token = JWTAuth::fromUser($user);
        return response()->json(compact('user','token'),201);
    }

    public function getAuthenticatedUser()
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
           return response()->json(['token_expired'], 401);
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], 401);
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent'], 401);
        }
        return response()->json(compact('user'));
    }
}
