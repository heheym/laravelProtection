<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class RechargeList extends Model
{
    protected $table = 'rechargeList';
    public $timestamps  = false;

    // public function place()
    // {
    //     return $this->belongsTo(Place::class,'svrkey','key');
    // }
    //
    // public function settopbox()
    // {
    //     return $this->belongsTo(SetTopBox::class,'ktvboxid','KtvBoxid');
    // }


}
