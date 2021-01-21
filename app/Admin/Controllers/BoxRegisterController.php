<?php

namespace App\Admin\Controllers;

use App\Admin\Models\BoxRegister;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\DB;
use Encore\Admin\Facades\Admin;

class BoxRegisterController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '机顶盒预登记';


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new BoxRegister);
        $grid->setView('boxregister.index');
        $grid->disableCreateButton();
        $grid->model()->orderBy('id','desc');
        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->like('KtvBoxid', 'KtvBoxid');
            $filter->like('machineCode', 'machineCode');
        });

//        $grid->column('id', __('Id'));
        $grid->column('KtvBoxid', __('机器码'));
        $grid->column('machineCode', '机顶盒MAC');
        $grid->column('agentid', '代理商');
        $grid->column('barcode', '产品条码');
        $grid->column('isOem', '是否OEM')->display(function($isOem){
            return [0=>'否',1=>'是'][$isOem];
        });
        $grid->column('channelNo', '产品渠道号');
        $grid->column('CreateDate', '生成时间');
        $grid->status('状态')->display(function () {
            $exists = DB::table('settopbox')->where('KtvBoxid',$this->KtvBoxid)->exists();
            if($exists){
                return '已注册';
            }
        });

        $grid->actions(function ($actions) {
            $actions->disableView();
            if (!Admin::user()->can('机顶盒预登记删除')) {
                $actions->disableDelete();
            }
            if (!Admin::user()->can('机顶盒预登记修改')) {
                $actions->disableEdit();
            }
        });
        if (!Admin::user()->can('机顶盒预登记添加')) {
            $grid->disableCreateButton();  //场所添加的权限
        }

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
        $form = new Form(new BoxRegister);

        $form->text('KtvBoxid', __('KtvBoxid'));
        $form->text('machineCode', __('机顶盒MAC'));
        $form->datetime('CreateDate', __('生成时间'))->default(date('Y-m-d H:i:s'));

        return $form;
    }
}
