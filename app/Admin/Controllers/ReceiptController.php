<?php

namespace App\Admin\Controllers;


use App\Admin\Models\Receipt;
use App\Admin\Models\Place;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Admin;
use Illuminate\Support\Facades\DB;

use App\Admin\Extensions\Receivable\ReceivableEdit;
use App\Admin\Extensions\Receipt\Invalid;

use Encore\Admin\Grid\Displayers\Actions;

use Illuminate\Support\Facades\Request;

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
            ->body($this->grid())
            ->body($this->grid1());

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

    protected function grid()
    {
//        Admin1::disablePjax();
//        Admin::js('js/hide.js');
        Admin::script('receipt();');
        $grid = new Grid(new Place);
        $grid->paginate(5);
        $grid->setName('place');
        $grid->disableFilter(false);
        $grid->disableBatchActions();
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableEdit();
            $actions->disableDelete();
        });


        $grid->filter(function($filter){

            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->column(1/3, function ($filter) {
//                $filter->like('key', 'key');
                $filter->like('placename', '场所名称');
//                $wangMode = DB::table('warningmode')->pluck('warningName','id')->toArray();
//                $filter->equal('wangMode','预警模式')->select($wangMode);
//                $setMeal = DB::table('setMeal')->pluck('setMeal_name','setMeal_id')->toArray();
//                $filter->equal('setMeal','套餐')->select($setMeal);
//                $filter->equal('status','状态')->select([0=>'未启用',1=>'已启用']);
            });

            $filter->column(1/3, function ($filter) {
//                $filter->where(function ($query) {
//                    $query->where('province', 'like', "%{$this->input}%")
//                        ->orWhere('city', 'like', "%{$this->input}%");
//                }, '省市');
//                $filter->like('province', '省');
//                $filter->like('city', '市');
                $filter->like('contacts', '联系人');
//                $filter->between('expiredata', '场所有效时间')->datetime();
            });

            $filter->column(1/3, function ($filter) {
                $filter->like('phone', '手机号');
            });
        });



//        $grid->id('Id');
        $grid->userno('场所编号');
        $grid->key('key');
        $grid->placehd('场所服务器ID');
        $grid->placename('场所名称');
//        $grid->mailbox('邮箱');
        $grid->phone('手机号');
        $grid->contacts('联系人');
        $grid->tel('联系电话');
        $grid->placeaddress('地址');
        $grid->roomtotal('机顶盒数量');
        $grid->expiredata('场所有效时间');
        $grid->country('国家');
        $grid->province('省')->display(function ($province) {
            if(!is_null($province)){
                return DB::table('china_area')->where('code',$province)->value('name');
            }
        });
        $grid->city('市')->display(function ($city) {
            if(!is_null($city)){
                return DB::table('china_area')->where('code',$city)->value('name');
            }
        });

        $grid->status('状态')->display(function ($status) {
            if(!is_null($status)){
                $arra = [0=>'未启用',1=>'已启用'];
                return $arra[$status];
            }
        });
        $grid->wangMode('预警模式')->display(function ($wangMode) {
            if(!is_null($wangMode)){
                return DB::table('warningmode')->where('id',$wangMode)->value('warningName');
            }
        });
//        $grid->setMeal('套餐')->display(function ($setMeal) {
//            if(!is_null($setMeal)){
//                return DB::table('setMeal')->where('setMeal_id',$setMeal)->value('setMeal_name');
//            }
//        });

        return $grid;
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid1()
    {
        $grid = new Grid(new Receipt);
        $grid->setName('receipt');
        $grid->setView('receipt.receipt');
        $grid->disableColumnSelector();
        $grid->disableExport();
        $grid->disableCreateButton();
//        $grid->paginate(10);
//        $grid->disableFilter(false);

//        $grid->setActionClass(Actions::class);

        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->like('svrkey','svrkey');
            $filter->like('receipt_no','收款单号');
        });
        if(!app('request')->get('receipt_svrkey')){  //默认不显示应收纪录
            $grid->model()->where('svrkey', '');
        }

//        $grid->id('Id');
//
        $grid->placename('场所名称')->display(function () {
            return DB::table('place')->where('key',$this->svrkey)->value('placename');
        });
//        $grid->svrkey('场所key');
//        $grid->placehd('场所服务器id');
        $grid->receipt_no('收款单号');
        $grid->receipt_date('收款日期');
        $grid->receipt_name('收款人');

        $grid->receipt_currency('币种');
        $grid->local_money('收款金额(元)')->display(function () {
            if($this->id){
                return DB::table('receiptlist')->where('receipt_id',$this->id)->value('local_money');
            }
        });
        $grid->payment_type('收款方式')->display(function () {
            if($this->id){
                $arr = [0=>'现金',1=>'微信',2=>'支付宝',3=>'转帐'];
                $value = DB::table('receiptlist')->where('receipt_id',$this->id)->value('payment_type');
                if($value){
                    return $arr[$value];
                }
            }
        });
//        $grid->receipt_rate('汇率');
        $grid->createDate('建立时间');
        $grid->Remarks('备注');

        $grid->status('状态')->display(function () {
            if($this->id){
                $exist =  DB::table('receiptvoid')->where('id',$this->id)->exists();
                if($exist){
                    return '已作废';
                }
            }
        });

        $grid->actions(function ($actions){
            $actions->disableDelete();
            $actions->disableView();
            $id = $actions->row->id;
            $exist =  DB::table('receipt')->where('id',$id)->exists();
            if($exist){
                $actions->append(new Invalid($actions->getKey()));
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
        $form->disableSubmit();
        $form->disableReset();
        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
            $tools->disableDelete();
        });

        $id = request()->route()->parameters();
        $placename = '';
        if($id){
            $key = DB::table('receipt')->where('id',$id)->value('svrkey');
            if(empty($key)){
                $key = DB::table('receiptvoid')->where('id',$id)->value('svrkey');
            }
            $placename = DB::table('place')->where('key',$key)->value('placename');
        }
        $form->text('placename', '场所名称')->default($placename)->required()->readonly();
        $form->text('receipt_no', '收款单号')->readonly();
        $form->datetime('receipt_date', '收款日期')->default(date('Y-m-d H:i:s'));
        $form->text('receipt_name', '收款人');
        $form->text('receipt_currency', '币种')->default('人民币');
        $form->decimal('receipt_rate', '汇率')->default(1.000);
        $form->datetime('createDate', '建立时间')->default(date('Y-m-d H:i:s'));
        $form->text('Remarks', '备注');

        $form->text('receiptlist.local_money','收款金额');
        $form->select('receiptlist.payment_type','收款方式')->options([0=>'现金',1=>'微信',2=>'支付宝',3=>'转帐']);
        $form->text('receiptlist.payment_billno','帐单号');

        $form->table('hedging','', function ($table) {
            $table->text('item_no','单号');
            $table->text('hedging_money','对冲金额');
            $table->text('item_date','应收日期');
        }) ->disableCreate()->disableDelete();;

        return $form;
    }

    //收款单作废
    public function Invalid(Request $request)
    {
        $id = $_POST['id'];
        $receipt = DB::table('receipt')->where('id',$id)->first();
        if(!$receipt->id){
            return response()->json([
                'status'  => false,
                'message' => '收款单不存在',
            ]);
        }
        $receipt=get_object_vars($receipt);
        DB::transaction(function () use($receipt,$id){
            $receiptlistId = DB::table('receiptlist')->where('receipt_id',$id)->value('id');
            $receivableIds = DB::table('receipthedging')->where('receiptlist_id',$receiptlistId)->select('receivable_id','hedging_money')->get();
            foreach($receivableIds as $v){
                DB::table('receivable')->where('id',$v->receivable_id)->decrement('completion_money',$v->hedging_money);
            }
            DB::table('receiptvoid')->insert($receipt);
            DB::table('receipt')->where('id',$id)->delete($id);

        });
        return response()->json([
            'status'  => true,
            'message' => '操作成功',
        ]);
    }



}
