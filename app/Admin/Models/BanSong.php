<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class BanSong extends Model
{
    protected $table = 'song_banned';
    protected $primaryKey  = 'musicdbpk';
    public $timestamps = false;
}
