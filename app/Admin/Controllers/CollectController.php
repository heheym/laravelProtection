<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Collect;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

use App\Admin\Extensions\Collect\Export;
use App\Admin\Extensions\Collect\Collect as ColectExport;

use Encore\Admin\Facades\Admin;

class CollectController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Collect());
        $grid->disableActions();
        $grid->disableCreateButton();
        $grid->column('name','姓名');
        $grid->column('phone','手机号');
        $grid->column('address','地址');
        $grid->column('time','时间');
        $grid->column('message','备注');

        $postsExporter =  new ColectExport();
        $postsExporter->fileName = date('Y-m-d H:i:s').'.xlsx';
        $grid->exporter($postsExporter);
        $url = url()->current();
        $url .= "?_export_=all";
        $grid->tools(function ($tools)use($grid,$url){
            $tools->append(new Export($grid,$url));
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
        $show = new Show(UsersOpenclose::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('srvkey', __('Srvkey'));
        $show->field('KtvBoxid', __('KtvBoxid'));
        $show->field('opendate', __('Opendate'));
        $show->field('closedate', __('Closedate'));
        $show->field('feesmode', __('Feesmode'));

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
