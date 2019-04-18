<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'type', 'url', 'playlist_id'];

    /**
     * Get the profiles.
     */
    public function playlist()
    {
        return $this->belongsTo('App\Playlist');
    }
}
