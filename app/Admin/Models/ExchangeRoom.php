<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRoom extends Model
{
    protected $table = 'exchangroom';
    public $primaryKey = 'id';
    public $timestamps  = false;

    public function ordersn()
    {
       return $this->hasOne(Ordersn::class,'id','ordersnId');
    }

}
