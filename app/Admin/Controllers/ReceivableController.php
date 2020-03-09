<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Receivable;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ReceivableController extends Controller
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
        $grid = new Grid(new Receivable);

        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
        });


//        $grid->id('Id');
        $grid->svrkey('场所key');
        $grid->placehd('场所服务器id');
        $grid->item_no('单号');
        $grid->item_name('应收项目名称');
        $grid->item_unit('单位');
        $grid->item_num('数量');
        $grid->item_price('单价');
        $grid->item_discount('折扣');
        $grid->item_totalprice('总价');
        $grid->completion_money('已收');
        $grid->item_date('应收日期');
        $grid->createDate('产生时间');
        $grid->operation('操作人');
        $grid->item_source('来源');
//        $grid->sourceId('来源id');
        $grid->setMeal_startdate('套餐产生的应收时的期限开始日期');
        $grid->setMeal_enddate('套餐产生的应收时的期限结束日期');
        $grid->Remarks('备注');

        $grid->actions(function ($actions) {
            $actions->disableView();
        });

        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
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
        $show = new Show(Receivable::findOrFail($id));

//        $show->id('Id');
        $show->svrkey('场所key');
        $show->placehd('场所服务器id');
        $show->item_no('单号');
        $show->item_name('应收项目名称');
        $show->item_unit('Item unit');
        $show->item_num('Item num');
        $show->item_price('Item price');
        $show->item_discount('Item discount');
        $show->item_totalprice('Item totalprice');
        $show->completion_money('Completion money');
        $show->item_date('Item date');
        $show->createDate('CreateDate');
        $show->operation('Operation');
        $show->item_source('Item source');
        $show->sourceId('SourceId');
        $show->setMeal_startdate('SetMeal startdate');
        $show->setMeal_enddate('SetMeal enddate');
        $show->Remarks('Remarks');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Receivable);

        $form->text('svrkey', '场所key')->required();
        $form->text('placehd', '场所服务器id')->required();
        $form->text('item_no', '单号')->required();
        $form->text('item_name', '应收项目名称')->required();
        $form->text('item_unit', '单位')->required();
        $form->decimal('item_num', '数量')->required();
        $form->decimal('item_price', '单价')->required();
        $form->decimal('item_discount', '折扣')->required();
        $form->decimal('item_totalprice', '总价')->required();
        $form->decimal('completion_money', '已收')->required();
        $form->date('item_date', '应收日期')->required();
        $form->datetime('createDate', '产生时间')->required();
        $form->text('operation', '操作人')->required();
        $form->number('item_source', '来源')->required();
        $form->number('sourceId', '来源id')->required();
        $form->datetime('setMeal_startdate', '套餐产生的应收时的期限开始日期')->default(date('Y-m-d H:i:s'));
        $form->datetime('setMeal_enddate', '套餐产生的应收时的期限结束日期')->default(date('Y-m-d H:i:s'));
        $form->text('Remarks', '备注');

        return $form;
    }
}
