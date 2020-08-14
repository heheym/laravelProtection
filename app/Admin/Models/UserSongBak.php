<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class UserSongBak extends Model
{
    protected $table = 'users_songs_bak';
    public $primaryKey = 'id';
    public $timestamps  = false;

    public function place(){
        return $this->belongsTo(Place::class, 'srvkey' , 'key');
    }
//
    public function settopbox()
    {
        return $this->belongsTo(SetTopBox::class, 'KtvBoxid' , 'KtvBoxid');
    }

    public function song()
    {
        return $this->belongsTo(Song::class, 'musicdbpk' , 'musicdbpk');
    }



}
