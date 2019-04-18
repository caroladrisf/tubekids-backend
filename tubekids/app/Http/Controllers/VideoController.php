<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateVideoRequest;
use App\Http\Requests\VideoRequest;
use App\Playlist;
use App\User;
use App\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use JWTAuth;

class VideoController extends Controller
{
    /**
     * Return a list of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        
        if ($request->query('name')) {
            $name = '%' . $request->query('name') . '%';

            $playlist = Playlist::where('user_id', '=', $user->id)->get();
            foreach ($playlist as $p) {
                $p->videos = Video::where('playlist_id', $p->id)->where('name', 'ilike', $name)->get();
            }

        } else {
            $playlist = Playlist::where('user_id', '=', $user->id)->with('videos')->get();
        }
        
        return response()->json(compact('playlist'), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\ProfileRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(VideoRequest $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $playlist = User::find($user->id)->playlist;

        if (! $playlist) {
            return response()->json(['error' => 'Playlist not found'], 404);
        }

        if ($request->input('type') === 'Youtube Video') {
            
            if (! Str::startsWith($request->input('url'), 'https://www.youtube.com/watch')) {
                return response()->json(['error' => 'The url is not accepted'], 400);
            }
            $video = $playlist->videos()->create($request->all());
            
        } else if ($request->input('type') === 'Uploaded Video') {
            
            $url = Storage::disk('public')->put('uploads/'.$playlist->id, $request->file('file'));
            $video = new Video($request->except('url'));
            $video->url = $url;
            $playlist->videos()->save($video);
            
        }

        if (! $video) {
            return response()->json(['error' => 'There was an error storing the video'], 500);
        }

        return response()->json(compact('video'), 201)->header('Location', "http://localhost:8000/api/videos/$video->id");
    }

    /**
     * Return the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $video = Video::find($id);
        if (! $video) {
            return response()->json(['error' => 'Video not found'], 404);
        }
        return response()->json(compact('video'), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateVideoRequest $request, $id)
    {
        $video = Video::find($id);
        
        if ($request->hasFile('file')) {
            if ($video->type === 'Uploaded Video') {
                Storage::disk('public')->delete($video->url);
            }
            $url = Storage::disk('public')->put('uploads/'.$playlist->id, $request->file('file'));
            $video->fill(['url' => $url]);
            $video->fill($request->except('url'));
        } else {
            $video->fill($request->all());
        }

        if ($video->save()) {
            return response()->json(compact('video'), 200);
        }

        return response()->json(['error' => 'There was an error updating the profile'], 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Producto  $producto
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $video = Video::find($id);
        if ($video->type === 'Uploaded Video') {
            Storage::disk('public')->delete($video->url);
        }
        $video->delete();
        return response()->json('', 204);
    }
}
