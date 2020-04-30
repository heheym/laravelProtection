<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class PosterSet extends Model
{
    protected $table = 'PosterSet';
    public $timestamps = false;
    public $primaryKey = false;

    public function postertab()
    {
        return $this->belongsToMany(PosterTab::class,'Poster_id','Poster_id');
    }

}
