<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use JWTAuth;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;
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
     * Send a SMS with a verification code.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendSMS(User $user)
    {
        if (! $user) {
            return response()->json(['error'=>'User not found'], 404);
        }
        
        $user->update(['code' => random_int(10000, 99999)]);
        $client = new Client(getenv('ACCOUNT_SID'), getenv('AUTH_TOKEN'));

        try {
            $client->messages->create(
                '+' . '506' . $user->phone,
                array(
                    'from' => getenv('TWILIO_NUMBER'),
                    'body' => 'Your verification code is: ' . $user->code
                )
            );
        } catch(Twilio\Exceptions\TwilioException $e) {
            return response()->json(['exception' => 'twilio rest exception'], 500);
        } 

        return response()->json(['message' => 'We sent you a SMS with a verification code.'], 200);
    }

    /**
     * Verify if the code is correct.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyCode(Request $request, User $user)
    {
        if ($request->input('code') === $user->code) {
            $user->update(['code' => '']);
            return response()->json(['message' => 'Phone number verified!'], 200);
        }
        return response()->json(['error' => 'Incorrect code'], 403);
    }

    /**
     * Logout
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout($user)
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
            
            return response()->json(compact('user'), 201);
        } else {
            return response()->json(['error'=>'The user can not be created because the age is less than 18'], 403);
        }
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
