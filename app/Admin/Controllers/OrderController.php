<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Ordersn;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class OrderController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Admin\Models\Ordersn';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Ordersn);
        $grid->setView('order.grid');
        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->like('key','key');
            $filter->like('KtvBoxid','机器码');
            $filter->like('leshua_order_id','乐刷订单号');
            $filter->equal('order_status','状态')->select([0=>'未支付',1=>'已支付']);
        });

//        $grid->column('id', __('Id'));
        $grid->model()->orderBy('id', 'desc');
        $grid->column('key', __('Key'));
        $grid->column('KtvBoxid', __('机器码'));
//        $grid->column('order_sn', __('Order sn'));
        $grid->column('order_sn_submit', __('订单号'));
        $grid->column('order_status', __('订单状态'))->display(function($order_status){
            return [0=>'未支付',1=>'已支付'][$order_status];
        });
        $grid->column('amount', __('订单金额(元)'));
        $grid->column('submit_time', __('创建时间'));
//        $grid->column('o_status', __('有效状态'));
        $grid->column('pay_time', __('支付时间'));
        $grid->column('leshua_order_id', __('乐刷订单号'));
        $grid->column('pay_way', __('支付方式'))->display(function($pay_way){
            if(!empty($pay_way)){
                return ['WXZF'=>'微信','ZFBZF'=>'支付宝'][$pay_way];
            }
        });
        $grid->column('note', __('备注'));
        $grid->column('send_message', __('已发送信息'))->display(function($send_message){
            if(isset($send_message)){
                return ['0'=>'未发送','1'=>'已发送'][$send_message];
            }
        });
        $grid->column('confirm_order', __('已处理信息'))->display(function($confirm_order){
            if(isset($confirm_order)){
                return ['0'=>'未处理','1'=>'已处理'][$confirm_order];
            }
        });

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
        $show = new Show(Ordersn::findOrFail($id));

//        $show->field('id', __('Id'));
        $show->field('key', __('Key'));
        $show->field('KtvBoxid', __('机器码'));
//        $show->field('order_sn', __('订单号'));
        $show->field('order_sn_submit', __('订单号'));
        $show->field('order_status', __('订单状态'));
        $show->field('amount', __('订单金额(元)'));
        $show->field('submit_time', __('创建时间'));
//        $show->field('o_status', __('O status'));
        $show->field('pay_time', __('支付时间'));
        $show->field('leshua_order_id', __('乐刷订单号'));
        $show->field('pay_way', __('支付方式'));
        $show->field('note', __('备注'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Ordersn);
        $form->column(5/6, function ($form) {
            $form->text('key', __('Key'));
            $form->text('KtvBoxid', __('机器码'));
//        $form->text('order_sn', __('Order sn'));
            $form->text('order_sn_submit', __('订单号'));
            $form->select('order_status', __('订单状态'))->options([0 => '未支付', 1 => '已支付']);
            $form->decimal('amount', __('订单金额(元)'))->default(0.00);
            $form->datetime('submit_time', __('创建时间'));
//        $form->switch('o_status', __('O status'))->default(1);
            $form->datetime('pay_time', __('支付时间'));
            $form->text('leshua_order_id', __('乐刷订单号'));
            $form->text('pay_way', __('支付方式'));
            $form->text('note', __('备注'));
        });
        return $form;
    }
}
