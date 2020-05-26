<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'admin_roles';
    public $timestamps = false;


    public function getNameAttribute($value)
    {
        return 123;
    }
    
    public static function boot()
    {
        var_dump(123);
        return 123;
    }

}
