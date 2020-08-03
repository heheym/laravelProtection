<?php

namespace App\Admin\Actions\SetTopBox;

use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Encore\Admin\Facades\Admin;

class BatchChange extends BatchAction
{
//    public $name = '批量操作';
    protected $selector = '.report-posts';

    public function authorize($user, $model)
    {
//        if (Admin::user()->can('机顶盒修改')) {
            return true;
//        }
    }

    public function handle(Collection $collection, Request $request)
    {
        $KtvBoxState = $request->KtvBoxState;
        foreach ($collection as $model) {
            $model->KtvBoxState = $KtvBoxState;
            $model->save();
        }

        return $this->response()->success('修改成功')->refresh();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->select('KtvBoxState','状态修改')
            ->options([0=>'待审核',1=>'正常',2=>'返修',3=>'过期',4=>'作废'])->rules('required');
    }

    public function html()
    {
        return "<a class='report-posts btn btn-sm btn-dropbox'><i class='fa fa-info-circle'></i>修改状态</a>";
    }

}