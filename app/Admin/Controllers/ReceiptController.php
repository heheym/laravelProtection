<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Receipt;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ReceiptController extends Controller
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
        $grid = new Grid(new Receipt);

        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
        });

//        $grid->id('Id');
        $grid->svrkey('场所key');
        $grid->placehd('场所服务器id');
        $grid->receipt_no('收款单号');
        $grid->receipt_date('收款日期');
        $grid->receipt_name('收款人');
        $grid->receipt_currency('币种');
        $grid->receipt_rate('汇率');
        $grid->createDate('建立时间');
        $grid->Remarks('备注');

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
        $show = new Show(Receipt::findOrFail($id));

        $show->id('Id');
        $show->svrkey('Svrkey');
        $show->placehd('Placehd');
        $show->receipt_no('Receipt no');
        $show->receipt_date('Receipt date');
        $show->receipt_name('Receipt name');
        $show->receipt_currency('Receipt currency');
        $show->receipt_rate('Receipt rate');
        $show->createDate('CreateDate');
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
        $form = new Form(new Receipt);

        $form->text('svrkey', '场所key');
        $form->text('placehd', '场所服务器id');
        $form->text('receipt_no', '收款单号');
        $form->datetime('receipt_date', '收款日期')->default(date('Y-m-d H:i:s'));
        $form->text('receipt_name', '收款人');
        $form->text('receipt_currency', '币种')->default('人民币');
        $form->decimal('receipt_rate', '汇率')->default(1.000);
        $form->datetime('createDate', '建立时间')->default(date('Y-m-d H:i:s'));
        $form->text('Remarks', '备注');

        return $form;
    }
}
