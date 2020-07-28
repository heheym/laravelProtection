<?php

namespace App\Admin\Controllers;

use App\Admin\Models\warningmode;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Facades\Admin;

class WarningModeController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
//            ->header('Index')
//            ->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
//            ->header('Detail')
//            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
//            ->header('Edit')
//            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
//            ->header('Create')
//            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new warningmode);

        $grid->id('Id');
        $grid->warningName('名称');
        $grid->warningTime('等级一预警时间内');
        $grid->warningCountRoom('房间等级一预警数量');
//        $grid->warningCountPlace('WarningCountPlace');
        $grid->warningTime1('等级二预警时间内');
        $grid->warningCountRoom1('房间等级二预警数量');
//        $grid->warningCountPlace1('WarningCountPlace1');

        $grid->actions(function ($actions) {
            $actions->disableView();
            if (!Admin::user()->can('预警模式删除')) {
                $actions->disableDelete();  //预警模式删除的权限
            }
            if (!Admin::user()->can('预警模式修改')) {
                $actions->disableEdit();  //预警模式删除的权限
            }
        });
        if (!Admin::user()->can('预警模式添加')) {
            $grid->disableCreateButton();  //预警模式添加的权限
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
        $show = new Show(warningmode::findOrFail($id));

        $show->id('Id');
        $show->warningName('WarningName');
        $show->warningTime('WarningTime');
        $show->warningCountRoom('WarningCountRoom');
        $show->warningCountPlace('WarningCountPlace');
        $show->warningTime1('WarningTime1');
        $show->warningCountRoom1('WarningCountRoom1');
        $show->warningCountPlace1('WarningCountPlace1');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new warningmode);

        $form->text('warningName', '名称');
        $form->number('warningTime', '等级一预警时间内');
        $form->number('warningCountRoom', '房间等级一预警数量');
//        $form->number('warningCountPlace', 'WarningCountPlace');
        $form->number('warningTime1', '等级二预警时间内');
        $form->number('warningCountRoom1', '房间等级二预警数量');
//        $form->number('warningCountPlace1', 'WarningCountPlace1');

        return $form;
    }
}
