<?php
/**
 * Created by PhpStorm.
 * User: xiaobaidaren
 * Date: 2/9/2020
 * Time: 18:13
 */

namespace App\Admin\Extensions\OrderCount;

use Encore\Admin\Grid\Exporters\ExcelExporter;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\DB;

class PostsExporter extends ExcelExporter implements WithMapping
{
    protected $fileName = '文章列表.xlsx';

    protected $headings = [ '场所名称', '房号','省市','订单号','订单状态','订单金额(元)','创建时间','支付时间','乐刷订单号','支付方式','用户号','备注' ];

    public function map($row) : array
    {
//        dd($row);
        return [
            data_get($row, 'place.placename'),
            data_get($row, 'settopbox.roomno'),
            DB::table('china_area')->where('code',data_get($row, 'place.province'))->value('name').
            DB::table('china_area')->where('code',data_get($row, 'place.city'))->value('name'),
            $row->order_sn_submit,
            $row->order_status == 1? '已支付':'未支付',
            $row->amount,
            $row->submit_time,
            $row->pay_time,
            $row->leshua_order_id,
            $row->pay_way== 'WXZF'?'微信':'支付宝',
            $row->openid,
            $row->note,
        ];
    }
}