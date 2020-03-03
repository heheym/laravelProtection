<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class SetTopBox extends Model
{
    protected $table = 'settopbox';
    public $timestamps = false;

    public function place(){
        return $this->belongsTo(Place::class, 'key' , 'key');
    }
}
