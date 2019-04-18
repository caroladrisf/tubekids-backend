<?php

namespace App\Http\Controllers;

use App\Playlist;
use Illuminate\Http\Request;
use JWTAuth;

class PlaylistController extends Controller
{
    /**
     * Retrieve a playlist or create it if does not exists.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $playlist = Playlist::where(['user_id' => $user->id], ['name' => 'General']);
        return response()->json(compact('playlist'), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $playlist = Playlist::create(['user_id' => $user->id, 'name' => 'General']);
        return response()->json(compact('playlist'), 201);
    }
}
