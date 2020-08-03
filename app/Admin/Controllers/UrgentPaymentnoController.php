<?php

namespace App\Admin\Controllers;

use App\Admin\Models\UrgentPaymentno;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class UrgentPaymentnoController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Admin\Models\UrgentPaymentno';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new UrgentPaymentno);
        $grid->setView('urgentpaymentno.index');
        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->like('paymentno','paymentno');
        });

//        $grid->column('id', __('Id'));
        $grid->column('notype', __('账号类型'))->display(function($notype){
            return [0=>'微信',1=>'支付宝',2=>'其它'][$notype];
        });
        $grid->column('paymentno', __('账号'));
        $grid->column('createdate', __('时间'));

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
        $show = new Show(UrgentPaymentno::findOrFail($id));

//        $show->field('id', __('Id'));
        $show->field('notype', __('账号类型'));
        $show->field('paymentno', __('账号'));
        $show->field('createdate', __('时间'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new UrgentPaymentno);

        $form->select('notype', __('账号类型'))->options([0=>'微信',1=>'支付宝',2=>'其它']);
        $form->text('paymentno', __('账号'));
        $form->datetime('createdate', __('时间'))->default(date('Y-m-d H:i:s'));

        return $form;
    }
}
