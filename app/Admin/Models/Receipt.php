<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    protected $table = 'receipt';
    public $timestamps = false;
    public $primaryKey = 'id';
    protected $appends = ['hedging'];

    public function receiptlist()
    {
        return $this->hasOne(ReceiptList::class,'receipt_id','id');
    }

    public function getHedgingAttribute()
    {


        $arr = [['receivable_id'=>123],['receivable_id'=>123]];
        $arr = array_values($arr);

        var_dump($arr);
        return $arr;

    }

}
