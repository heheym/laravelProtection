<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class RechargeMoney extends Model
{
    protected $table = 'rechargeMoney';
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
