<?php

namespace App\Admin\Controllers;

use App\Admin\Models\BoxRegister;
use App\Admin\Models\Wssend;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\DB;
use Encore\Admin\Facades\Admin;

class WssendController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'ws定时';


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Wssend());
        // $grid->setView('boxregister.index');
        $grid->disableCreateButton();
        $grid->model()->orderBy('id','desc');
        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->like('KtvBoxid', 'KtvBoxid');
            $filter->like('machineCode', 'machineCode');
        });

        $grid->column('arr', __('发送数据'));
        $grid->column('sendTime', '发送时间');


        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(BoxRegister::findOrFail($id));

//        $show->field('id', __('Id'));
        $show->field('KtvBoxid', __('KtvBoxid'));
        $show->field('machineCode', '机顶盒MAC');
        $show->field('CreateDate', __('CreateDate'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Wssend);

        $form->textarea('arr', __('发送数据'));
        $form->datetime('sendTime', __('发送时间'));

        return $form;
    }
}
