<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Profile;
use App\User;
use Illuminate\Http\Request;
use JWTAuth;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $profiles = Profile::where('user_id', '=', $user->id)->get();
        return response()->json($profiles, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\ProfileRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProfileRequest $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $profile = $user->profiles()->create($request->all());
        if ($profile) {
            return response()->json(compact('profile'), 201)->header('Location', "http://localhost:8000/api/profiles/$profile->id");
        }
        return response()->json(['error' => 'There was an error creating the profile'], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $profile = Profile::find($id);
        return response()->json($profile, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $profile = Profile::find($id);
        if ($profile) {
            $profile->update($request->all());
            return response()->json($profile, 200);
        }
        return response()->json(['error' => 'There was an error updating the profile'], 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $profile = Profile::destroy($id);
        if ($profile) {
            return response()->json('', 204);
        }
        return response()->json(['error' => 'There was an error deleting the profile'], 500);
    }
}
