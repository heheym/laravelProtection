<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class OrderCount extends Model
{
    public $table  = 'ordersn';

    public function settopbox(){
        return $this->belongsTo(SetTopBox::class, 'KtvBoxid' , 'KtvBoxid');
    }
    public function place(){
        return $this->belongsTo(Place::class, 'key' , 'key');
    }

    public function province()
    {
        return $this->hasManyThrough(
            ChinaArea::class,
            Place::class,
            'key', // place表外键...
            'code', // ChinaArea表外键...
            'key', // ordercount表本地键...
            'province' // place本地键...
        );
    }
    public function city()
    {
        return $this->hasManyThrough(
            ChinaArea::class,
            Place::class,
            'key', // place表外键...
            'code', // ChinaArea表外键...
            'key', // ordercount表本地键...
            'city' // place本地键...
        );
    }

}
