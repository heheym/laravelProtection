<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class SongDownload extends Model
{
    protected $table = 'songdownload';
    public $timestamps = false;

    public function place()
    {
        return $this->belongsTo(Place::class,'srvkey','key');
    }
}
