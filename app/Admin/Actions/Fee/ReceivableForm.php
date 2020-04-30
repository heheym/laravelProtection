<?php

namespace App\Admin\Actions\Fee;

use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Widgets\Form;

class ReceivableForm extends BatchAction
{
//    public $name = '批量复制';
    protected $selector = '.report-posts';

    public function handle(Collection $collection,Request $request)
    {

        $receipt_name = $request->get('receipt_name');
        $receipt_date = $request->get('receipt_date');
        $receipt_currency = $request->get('receipt_currency');
        $receipt_rate = $request->get('receipt_rate');
        $original_money = $request->get('original_money');
        $local_money = $request->get('local_money');
        $payment_type = $request->get('payment_type');
        $Remarks = $request->get('Remarks');

        $receipt = [];
        $receipt['receipt_no'] = time().rand(0,999);
        $receipt['receipt_date'] = $receipt_date;
        $receipt['receipt_name'] = $receipt_name;
        $receipt['receipt_currency'] = $receipt_currency;
        $receipt['receipt_rate'] = $receipt_rate;
        $receipt['createDate'] = date('Y-m-d H:i:s');
        $receipt['Remarks'] = $Remarks;
        foreach ($collection as $model) {
            $receipt['svrkey'] = $model->svrkey;
            $receipt['placehd'] = $model->placehd;
        }

        $ReceiptList = [];
        $ReceiptList['original_money'] = $original_money;
        $ReceiptList['local_money'] = $local_money;
        $ReceiptList['payment_type'] = $payment_type;
        $ReceiptList['payment_billno'] = $receipt['receipt_no'];
        $ReceiptList['createDate'] = date('Y-m-d H:i:s');

        $totalOwedPrice = 0;
        foreach($collection as $model){
            $model->owedPrice = $model->item_totalprice-$model->completion_money; //欠收额
            $totalOwedPrice += $model->owedPrice;
        }
        if($local_money>$totalOwedPrice){
            return $this->response()->error('收款金额大于应收金额');
        }
        DB::transaction(function () use ($receipt,$ReceiptList,$collection,$local_money){
            $id = DB::table('receipt')->insertGetId($receipt);  //收款单id
            $ReceiptList['receipt_id'] = $id;
            $receiptListId = DB::table('receiptlist')->insertGetId($ReceiptList); //收款单子表id
            foreach($collection as $models){
                if($models->owedPrice>0 && $local_money>0){
                    if($local_money>$models->owedPrice){
                        $models->completion_money = $models->completion_money+$models->owedPrice;
                        $local_money = $local_money-$models->owedPrice;
                        DB::table('receipthedging')->insert(['receivable_id'=>$models->id,'receiptlist_id'=>$receiptListId,'hedging_money'=>$models->owedPrice]);
                        unset($models->owedPrice);
                        $models->save();
                     }else{
                        $models->completion_money = $models->completion_money+$local_money;
                        unset($models->owedPrice);
                        $models->save();
                        DB::table('receipthedging')->insert(['receivable_id'=>$models->id,'receiptlist_id'=>$receiptListId,'hedging_money'=>$local_money]);
                        $local_money = 0;
                    }
                }
            }
        });

        return $this->response()->success('成功')->refresh();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->text('receipt_name', '收款人')->default(Admin::user()->name)->readonly();
        $this->text('receipt_currency', '币种')->default('人民币')->rules('required');
        $this->text('receipt_rate', '汇率')->default(1)->rules('required');
        $this->datetime('receipt_date', '收款时间')->default(date('Y-m-d H:i:s'))->required();
        $this->text('receivableMoney', '应收金额')->rules('required');
        $this->text('original_money', '原币金额')->rules('required');
        $this->text('local_money', '本币金额')->rules('required');
        $this->select('payment_type', '收款方式')->options([0=>'现金',1=>'微信',2=>'支付宝',3=>'转帐']);
        $this->textarea('Remarks', '备注');

    }

    public function html()
    {
        return "<a class='report-posts btn btn-sm btn-dropbox pull-right'><i class='fa fa-info-circle'></i>收款</a>";
    }

    public function data()
    {
        return [
            'receivableMoney'       => 'John Doe',
        ];
    }

}