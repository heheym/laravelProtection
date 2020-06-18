<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class Ordersn extends Model
{
    public $table  = 'ordersn';

    public function settopbox(){
        return $this->belongsTo(SetTopBox::class, 'KtvBoxid' , 'KtvBoxid');
    }
}
