<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class DangerSong extends Model
{
    protected $table = 'song_rights';
    protected $primaryKey  = 'musicdbpk';
    public $timestamps = false;
}
