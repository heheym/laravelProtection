<?php
/**
 * Created by PhpStorm.
 * User: xiaobaidaren
 * Date: 22/7/2020
 * Time: 16:16
 */
namespace App\Admin\Extensions\Adminpermission;

use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;

class Replicate extends BatchAction
{
    public $name = '批量复制';

    public function handle(Collection $collection)
    {
        foreach ($collection as $model) {
            $model->replicate()->save();
        }

        // 返回一个`复制成功`的成功信息，并且刷新页面
        return $this->response()->success('复制成功.')->refresh();
    }
}
