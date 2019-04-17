<?php

namespace App\Http\Controllers;

use App\Playlist;
use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    /**
     * Retrieve a playlist or create it if does not exists.
     *
     * @return \Illuminate\Http\Response
     */
    public function findOrCreate($user_id)
    {
        $playlist = Playlist::firstOrCreate(['user_id' => $user_id], ['name' => 'General']);
        return response()->json($playlist, 200);
    }
}
