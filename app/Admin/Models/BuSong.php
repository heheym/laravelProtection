<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class BuSong extends Model
{
    protected $table = 'busong';
    protected $primaryKey = 'serialid';
    public $timestamps = false;

    public function place()
    {
        return $this->belongsTo(Place::class, 'svrkey' , 'key');
    }
}
