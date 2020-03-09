<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class PromoteSong extends Model
{
    protected $table = 'promoteSong';
    public $timestamps = false;

    protected $fillable = ['promoteId', 'songname','singername','songname','lan','album'];//开启白名单字段

    public function promoteSong(){
        return $this->belongsTo(PromoteSong::class , 'promoteId');
    }
}
