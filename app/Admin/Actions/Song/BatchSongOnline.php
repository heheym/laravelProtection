<?php

namespace App\Admin\Actions\Song;

use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class BatchSongOnline extends BatchAction
{
    public $name = '批量修改';
    protected $selector = '.report-posts';

    public function handle(Collection $collection, Request $request)
    {
        $OnlineStatus = $request->OnlineStatus;
        foreach ($collection as $model) {
            $model->OnlineStatus = $OnlineStatus;
            $model->save();
        }
        return $this->response()->success('修改成功')->refresh();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->select('OnlineStatus','上下架')
            ->options([0=>'下架',1=>'上架'])->rules('required');
    }

    public function html()
    {
        return "<a class='report-posts btn btn-sm btn-dropbox'><i class='fa fa-info-circle'></i>修改状态</a>";
    }

}