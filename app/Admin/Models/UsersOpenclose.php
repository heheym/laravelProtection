<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class UsersOpenclose extends Model
{
    protected $table = 'users_openclose';
    public $primaryKey = 'id';
    public $timestamps  = false;

    public function place()
    {
        return $this->hasOne(Place::class,'key','srvkey');
    }
    public function settopbox()
    {
        return $this->hasOne(SetTopBox::class,'KtvBoxid','KtvBoxid');
    }
}
