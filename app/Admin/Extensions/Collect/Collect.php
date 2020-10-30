<?php
/**
 * Created by PhpStorm.
 * User: xiaobaidaren
 * Date: 2/9/2020
 * Time: 18:13
 */

namespace App\Admin\Extensions\Collect;

use Encore\Admin\Grid;
use Encore\Admin\Grid\Exporters\ExcelExporter;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\DB;

class Collect extends ExcelExporter
{
    public $fileName = '导出.xlsx';

    protected $columns = [
        'name'   => '姓名',
        'phone' => '手机号',
        'address' => '地址',
        'time' => '时间',
        'message' => '备注',
    ];

}