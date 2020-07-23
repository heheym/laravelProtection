<?php

namespace App\Admin\Controllers;

use App\Admin\Models\WarningCompany;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class WarningCompanyController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Admin\Models\WarningCompany';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new WarningCompany);
        $grid->disableCreateButton();

        $grid->model()->orderBy('startdatetime', 'desc');

        $grid->column('svrkey', __('svrkey'));
        $grid->column('place.placename', __('场所名称'));
        $grid->column('ktvboxid', __('机器码'));
        $grid->column('settopbox.roomno', __('房号'));
        $grid->column('RecordCompany', __('唱片公司'));
        $grid->column('startdatetime', __('开始时间'));
        $grid->column('enddatetime', __('结束时间'));
        $grid->column('creatdate', __('创建时间'));
        $grid->column('Clickcount', __('点击量'));

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
        $show = new Show(WarningCompany::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('svrkey', __('Svrkey'));
        $show->field('ktvboxid', __('Ktvboxid'));
        $show->field('RecordCompany', __('RecordCompany'));
        $show->field('startdatetime', __('Startdatetime'));
        $show->field('enddatetime', __('Enddatetime'));
        $show->field('creatdate', __('Creatdate'));
        $show->field('Clickcount', __('Clickcount'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new WarningCompany);

        $form->text('svrkey', __('svrkey'));
        $form->text('ktvboxid', __('机器码'));
        $form->text('RecordCompany', __('唱片公司'));
        $form->datetime('startdatetime', __('开始时间'))->default(date('Y-m-d H:i:s'));
        $form->datetime('enddatetime', __('结束时间'))->default(date('Y-m-d H:i:s'));
        $form->date('creatdate', __('创建时间'))->default(date('Y-m-d'));
        $form->number('Clickcount', __('点击量'));

        return $form;
    }
}
