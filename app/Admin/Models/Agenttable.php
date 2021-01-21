<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class Agenttable extends Model
{
    protected $table = 'agenttable';
    protected $primaryKey  = 'agentid';
    public $timestamps = false;

    public function province()
    {
        return $this->hasOne(ChinaArea::class,'code','province_code');
    }
    public function city()
    {
        return $this->hasOne(ChinaArea::class,'code','city_code');
    }


}
