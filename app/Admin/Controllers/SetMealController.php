<?php

namespace App\Admin\Controllers;

use App\Admin\Models\SetMeal;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class SetMealController extends Controller
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
        $grid = new Grid(new SetMeal);

        $grid->filter(function($filter){

            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            // 在这里添加字段过滤器
            $filter->like('setMeal_name', '套餐名称');


         });

//        $grid->setMeal_id('SetMeal id');
        $grid->setMeal_name('套餐名称');
        $grid->setMeal_days('套餐时长');
        $grid->setMeal_mode('套餐计费方式')->display(function ($setMeal_mode) {
            if(!is_null($setMeal_mode)){
                $arra = [1=>'按有效机顶盒数',2=>'固定费用'];
                return $arra[$setMeal_mode];
            }
        });
        $grid->setMeal_enabled('是否停用')->display(function ($setMeal_enabled) {
            if(!is_null($setMeal_enabled)){
                $arra = [0=>'正常',1=>'停用'];
                return $arra[$setMeal_enabled];
            }
        });
        $grid->setMeal_price('套餐单价');
        $grid->setMeal_discount('套餐折扣');
        $grid->setMeal_giveDays('套餐增送天数');
        $grid->createDate('创建时间');
        $grid->validDate('有效截止日期');

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
        $show = new Show(SetMeal::findOrFail($id));

        $show->setMeal_id('SetMeal id');
        $show->setMeal_name('SetMeal name');
        $show->setMeal_days('SetMeal days');
        $show->setMeal_mode('SetMeal mode');
        $show->setMeal_enabled('SetMeal enabled');
        $show->setMeal_price('SetMeal price');
        $show->setMeal_discount('SetMeal discount');
        $show->setMeal_giveDays('SetMeal giveDays');
        $show->createDate('CreateDate');
        $show->validDate('ValidDate');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new SetMeal);

//        $form->number('setMeal_id', '套餐名称');
        $form->text('setMeal_name', '套餐名称');
        $form->number('setMeal_days', '套餐时长');
        $form->select('setMeal_mode', '套餐计费方式')->options( [1=>'按有效机顶盒数',2=>'固定费用']);
        $form->select('setMeal_enabled', '是否停用')->options([0=>'正常',1=>'停用']);
        $form->decimal('setMeal_price', '套餐单价');
        $form->decimal('setMeal_discount', '套餐折扣');
        $form->number('setMeal_giveDays', '套餐增送天数');
        $form->datetime('createDate', '创建时间')->default(date('Y-m-d H:i:s'));
        $form->datetime('validDate', '有效截止日期')->default(date('Y-m-d H:i:s'));

        return $form;
    }
}
