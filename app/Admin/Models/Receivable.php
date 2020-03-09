<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class Receivable extends Model
{
    protected $table = 'receivable';
    public $timestamps = false;
    public $primaryKey = 'id';
}
