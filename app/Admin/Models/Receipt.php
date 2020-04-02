<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        $receiptlistId = $this->receiptlist->id;  //子表id
        $hedging = DB::table('receipthedging')
            ->join('receivable', 'receivable.id', '=', 'receipthedging.receivable_id')
            ->select('receipthedging.hedging_money', 'receivable.*')
            ->where('receipthedging.receiptlist_id','=',$receiptlistId)
            ->get()
            ->map(function ($value) {return (array)$value;})->toArray();

        return $hedging;
    }


}
