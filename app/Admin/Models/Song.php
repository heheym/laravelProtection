<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    protected $table = 'song';
//    public $timestamps = false;
    protected $primaryKey  = 'musicdbpk';

    const CREATED_AT = null;
    const UPDATED_AT = 'UpdateDate';

}
