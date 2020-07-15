<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class WarningCompany extends Model
{
    protected $table = 'warningcompany';
    public $timestamps  = false;

    public function place()
    {
        return $this->belongsTo(Place::class,'svrkey','key');
    }

    public function settopbox()
    {
        return $this->belongsTo(SetTopBox::class,'ktvboxid','KtvBoxid');
    }
}
