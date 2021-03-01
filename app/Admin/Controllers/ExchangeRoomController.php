<?php

namespace App\Admin\Controllers;

use App\Admin\Models\ExchangeRoom;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

use Encore\Admin\Facades\Admin;

class ExchangeRoomController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '开关房记录';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ExchangeRoom);
        $grid->disableActions();
        $grid->disableBatchActions();
        $grid->disableCreateButton();

        // $grid->filter(function ($filter){
        //     $filter->disableIdFilter();
        //     $filter->like('srvkey','srvkey');
        //     $filter->like('KtvBoxid','KtvBoxid');
        //     //$filter->between('UploadDate', '上传时间')->datetime();
        // });
        // $grid->disableCreateButton();
        // $grid->actions(function ($actions) {
        //     $actions->disableView();
        //     $actions->disableEdit();
        //     if (!Admin::user()->can('歌曲点播查询删除')) {
        //         $actions->disableDelete();
        //     }
        // });
        // $where = [];
        // if(!empty(request('placename'))){
        //     $placename =request('placename');
        //     $where[] = ['placename','like','%'.$placename.'%'];
        // }
        // $grid->model()->whereHas('place', function ($query) use($where){
        //     $query->where($where);
        // });
        //
        // $settopbox = [];
        // if(!empty(request('roomno'))){
        //     $roomno =request('roomno');
        //     $settopbox[] = ['roomno','like','%'.$roomno.'%'];
        // }
        // if(!empty(request('roomno'))){
        //     $grid->model()->whereHas('settopbox', function ($query) use($settopbox){
        //         $query->where($settopbox);
        //     });
        // }


       // $grid->column('id', __('Id'));
        $grid->column('oldKtvBoxid', '原机器码');
        $grid->column('newKtvBoxid', '新机器码');
        $grid->column('ordersn.key', 'key');
        $grid->column('ordersn.leshua_order_id', '乐刷单号');
        $grid->column('ordersn.pay_time', '支付时间');
        $grid->column('exchangDate', '更换日期');
        $grid->column('remarks', '说明');


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
        $show = new Show(UsersOpenclose::findOrFail($id));



        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new UsersOpenclose);

        $form->text('srvkey', __('srvkey'));
        $form->text('KtvBoxid', __('机器码'));
        $form->datetime('opendate', __('开房时间'))->default(date('Y-m-d H:i:s'));
        $form->datetime('closedate', __('关房时间'))->default(date('Y-m-d H:i:s'));
        $form->select('feesmode', __('收费模式'))->options([0=>'非扫码收费',1=>'扫码收费']);

        return $form;
    }
}
