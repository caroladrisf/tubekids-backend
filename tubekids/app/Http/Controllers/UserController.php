<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\User;
use Carbon\Carbon;
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

    /**
     * Logout
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        JWTAuth::invalidate();
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Register an user.
     *
     * @param UserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(UserRequest $request)
    {
        // Validate if the user's age is greater than or equal to 18
        $age = Carbon::createFromFormat('d/m/Y', $request->input('birthdate'))->age;
        if ($age >= 18) {
            $user = new User();
            $user->fill($request->except('password'));
            $user->fill(['password' => Hash::make($request->input('password'))])->save();
            
            $token = JWTAuth::fromUser($user);
            return response()->json(compact('user','token'), 201);
        } else {
            return response()->json(['error'=>'The user can not be created because the age is less than 18'], 403);
        }
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

    /**
     * Confirm the user's email address.
     *
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirmEmailAddress(User $user) {
        if ($user) {
            $user->fill(['email_verified_at' => now()]);
            $user->save();
            return response()->json(['message' => 'Email address confirmed successfully']);
        }
        return response()->json(['error' => 'The user does not exist']);
    }
}
