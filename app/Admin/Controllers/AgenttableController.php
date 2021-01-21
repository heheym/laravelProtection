<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Agenttable;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;


use Encore\Admin\Facades\Admin;

class AgenttableController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '代理商';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        Admin::style(
            '
    table{table-layout:fixed ;}
    table thead th,td{
    max-width: 10px;
    min-width: 10px;
    overflow:hidden;
    white-space:nowrap;
    text-overflow:ellipsis;}'
        );

        $grid = new Grid(new Agenttable());
        // $grid->disableActions();
        $grid->disableCreateButton();
        $grid->actions(function($actions){
            $actions->disableDelete();
            $actions->disableView();
        });
        $grid->column('agentNo','代理商编号');
        $grid->column('agentName','代理商名称');
        $grid->column('country','国家');
        $grid->column('province.name','省');
        $grid->column('city.name','市');
        $grid->column('agentLevel','代理商级别')->display(function($agentLevel){
            return [1=>'一级',2=>'二级'][$agentLevel];
        });
        $grid->column('parentAgentid','上一级代理商');
        $grid->column('isappChanne','是否启用专属app渠道号')->display(function($isappChanne){
            return [0=>'否',1=>'是'][$isappChanne];
        });
        $grid->column('ChannelNo','专属app渠道号');
        $grid->column('WechatPublicHttp','专属微信公众号');
        $grid->column('agentboxVer','专属app渠道号机顶盒升级版本号');
        $grid->column('agentboxFile','专属app渠道号机顶盒升级版本最新文件名');

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
        // $show = new Show(UsersOpenclose::findOrFail($id));
        //
        // $show->field('id', __('Id'));
        // $show->field('srvkey', __('Srvkey'));
        // $show->field('KtvBoxid', __('KtvBoxid'));
        // $show->field('opendate', __('Opendate'));
        // $show->field('closedate', __('Closedate'));
        // $show->field('feesmode', __('Feesmode'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Agenttable);

        $form->text('agentNo','代理商编号')->disable();
        $form->text('agentName','代理商名称')->disable();
        $form->text('country','国家')->disable();
        $form->text('province.name','省')->disable();
        $form->text('city.name','市')->disable();
        $form->select('agentLevel','代理商级别')->options([1=>'一级',2=>'二级'])->readOnly();
        $form->text('parentAgentid','上一级代理商')->disable();
        $form->text('isappChanne','是否启用专属app渠道号')->disable();
        $form->text('ChannelNo','专属app渠道号')->disable();
        $form->text('WechatPublicHttp','专属微信公众号');
        $form->text('agentboxVer','专属app渠道号机顶盒升级版本号');
        $form->text('agentboxFile','专属app渠道号机顶盒升级版本最新文件名');

        return $form;
    }
}
