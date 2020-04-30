<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class Receipt extends Model
{
    protected $table = 'receipt';
    public $timestamps = false;
    public $primaryKey = 'id';
    protected $appends = ['hedging'];
    protected $sql = "select * from receipt UNION select * from receiptvoid";

    public function receiptlist()
    {
        return $this->hasOne(ReceiptList::class,'receipt_id','id');
    }

    public function getHedgingAttribute()
    {
        if($this->receiptlist){
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


    public function paginate()
    {
        $perPage = Request::get('receipt_per_page', 10);
        $page = Request::get('receipt_page', 1);
        $receipt_svrkey = Request::get('receipt_svrkey','');
        $receipt_no = Request::get('receipt_no','');

        $start = ($page-1)*$perPage;

        // 运行sql获取数据数组

        $totalsql = "select count(*) as total from ($this->sql) a where a.svrkey='$receipt_svrkey' and a.receipt_no like '%$receipt_no%' ";
        $total = DB::select($totalsql);
        $total = $total[0]->total;

        $sql1 = "select * from ($this->sql) a where a.svrkey='$receipt_svrkey' and a.receipt_no like '%$receipt_no%' order by receipt_no desc limit $start,$perPage";
        $result = DB::select($sql1);
//        var_dump($result);

        $result = static::hydrate($result);

        $paginator = new LengthAwarePaginator($result, $total, $perPage,$page);

//        var_dump(url()->current());
        $paginator->setPageName('receipt_page');
//        $paginator->setCurrentPage($page);
        $paginator->setPath(url()->current());

        return $paginator;

    }

    public static function with($relations)
    {
        return new static;
    }

    public function findOrFail($id)
    {
        $sql = "select * from ($this->sql) a where a.id=".$id;
        $data = DB::select($sql);
        return static::newFromBuilder($data[0]);

//        $data = DB::table('receipt')->where('id',$id)->first();
//        if(!empty($data->id)){
//            return static::newFromBuilder($data);
//        }else{
//            $data = DB::table('receiptvoid')->where('id',$id)->first();
//            return static::newFromBuilder($data);
//        }

    }

    // 覆盖`where`来收集筛选的字段和条件
    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        return $this;
    }

}
