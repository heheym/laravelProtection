<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class ChinaArea extends Model
{
    protected $table = 'china_area';
    public $timestamps = false;


    public function place()
    {
        return $this->belongsTo(Place::class,'code','province');
    }

}
