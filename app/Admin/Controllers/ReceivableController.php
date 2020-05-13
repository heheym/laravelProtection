<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Receivable\CreateSetMeal;
use App\Admin\Extensions\Receivable\ReceivableForm;
use App\Admin\Extensions\Receivable\CreateOtherFee;
use App\Admin\Models\Receivable;
use App\Admin\Models\Place;
use App\Admin\Models\SetMeal;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Admin;
use Encore\Admin\Facades\Admin as Admin1;
use Hamcrest\Core\Set;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use Illuminate\Support\MessageBag;
use App\Admin\Extensions\Receivable\ReceivableEdit;

use Field\Interaction\FieldTriggerTrait;
use Field\Interaction\FieldSubscriberTrait;

use Encore\Admin\Grid\Displayers\Actions;
use Encore\Admin\Grid\Displayers\DropdownActions;
use Encore\Admin\Grid\Displayers\ContextMenuActions;  //原始图标


class ReceivableController extends Controller
{
    use HasResourceActions, FieldTriggerTrait, FieldSubscriberTrait;

    public function update($id)
    {
        return $this->createSetMeal()->update($id);
    }

    public function store(Request $request)
    {
//        $action =  $request->get('action');
//        if($action=='createSetMeal'){
            return $this->createSetMeal()->store();
//        }else{
//            return $this->createOtherFee()->store();
//        }
    }
    public function destroy($id)
    {
        $value = DB::table('receivable')->where('id',$id)->value('completion_money');
        if($value>0){
            return response()->json([
                'status'  => false,
                'message' => '已收款',
            ]);
        }

        return $this->createSetMeal($id=$id)->destroy($id);
    }


    public function index(Content $content)
    {
        return $content
            ->body($this->grid())
            ->body($this->grid1());
    }


    public function show($id, Content $content)
    {
        return $content
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
        $receivable = DB::table('receivable')->where('id',$id)->first();
//        if($receivable->completion_money>0){
//            $error = new MessageBag([
//                'title'   => '提示',
//                'message' => '已收款,无法更改',
//            ]);
//            return back()->with(compact('error'));
//        }
        if($receivable->item_source === 0){   //费项来源是套餐
            return $content->body($this->createSetMeal($id)->edit($id));
        }elseif($receivable->item_source===1){ //费项来源是其它费项
            return $content->body($this->createOtherFee($id)->edit($id));
        }
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content,Request $request)
    {
        $action =  $request->get('action');
        if($action=='createSetMeal'){
            return $content->body($this->createSetMeal($id=0));
        }else{
            return $content->body($this->createOtherFee($id=0));
        }
    }

    public function grid()
    {

//        Admin1::disablePjax();
//        Admin::js('js/hide.js');
        Admin::script('receivable();');
        $grid = new Grid(new Place);
        $grid->setName('place');
        $grid->disableFilter(false);
        $grid->disableBatchActions();
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableEdit();
            $actions->disableDelete();
        });
        $grid->paginate(5);


        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->column(1/3, function ($filter) {
//                $filter->like('key', 'key');
                $filter->like('placename', '场所名称');
            });

            $filter->column(1/3, function ($filter) {
                $filter->like('contacts', '联系人');
            });

            $filter->column(1/3, function ($filter) {
                $filter->like('phone', '手机号');
            });
        });

        $grid->userno('场所编号');
        $grid->key('key');
        $grid->placehd('场所服务器ID');
        $grid->placename('场所名称');
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
        $grid = new Grid(new Receivable);
        $grid->setName('receivable');
        $grid->setView('receivable.receivable');
        $grid->disableCreateButton();
        $grid->disableColumnSelector();
        $grid->disableExport();
        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });
//        $grid->paginate(10);

        $query = http_build_query(['receivable_svrkey' => app('request')->get('receivable_svrkey'),'action'=>'createSetMeal']);
        $query1 = http_build_query(['receivable_svrkey' => app('request')->get('receivable_svrkey'),'action'=>'createOtherFee']);

        //新增其它费项
        $grid->tools(function ($tools)use($grid, $query1){
            $tools->append(new createOtherFee($grid, $query1));
        });
        //新增套餐
        $grid->tools(function ($tools)use($grid, $query){
            $tools->append(new CreateSetMeal($grid, $query));
        });
        $grid->tools(function (Grid\Tools $tools) {
            $tools->append(new ReceivableForm());
        });

//        $grid->disableFilter(false);
        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->like('svrkey','svrkey');
        });
        if(!app('request')->get('receivable_svrkey')){  //默认不显示应收纪录
            $grid->model()->where('svrkey', '');
        }

        $grid->model()->orderby('item_no','desc');
//        $grid->id('Id');
//        $grid->svrkey('场所key');
//        $grid->placehd('场所服务器id');
        $grid->placename('场所名称')->display(function () {
            return DB::table('place')->where('key',$this->svrkey)->value('placename');
        });
        $grid->item_no('单号');
        $grid->item_source('费项来源')->display(function ($item_source) {
            if(!is_null($item_source)){
                $arra = [0=>'套餐',1=>'其它收费'];
                return $arra[$item_source];
            }
        });
        $grid->item_Id('费项名称')->display(function ($item_Id) {
            if(!is_null($item_Id)){
                if($this->item_source===0){
                    return DB::table('setMeal')->where('setMeal_id',$item_Id)->value('setMeal_name');
                }else{
                  return DB::table('itemfeename')->where('itemId',$item_Id)->value('itemName');
                }
            }
        });
        $grid->item_num('套餐份数')->display(function ($item_num) {
            return sprintf('%d',$item_num);
        });
        $grid->item_price('单价(元)');
        $grid->item_discount('折扣');
        $grid->item_totalprice('总价(元)');
        $grid->completion_money('已收(元)');
        $grid->owedPrice('欠收(元)')->display(function () {
            $owedPrice = sprintf('%.2f',$this->item_totalprice-$this->completion_money);
            if($owedPrice>0){
                return "<span style='color:white;background-color: red;padding:5px'>$owedPrice</span>";
            }
            return "<span style=''>0</span>";
        });
        $grid->item_date('应收日期');
        $grid->createDate('产生时间');
        $grid->operation('操作人');
        $grid->sourceType('来源方式')->display(function ($sourceType) {
            if(!is_null($sourceType)){
                return [0=>'后台增加产生',1=>'场所支付产生'][$sourceType];
            }
        });
//        $grid->sourceId('来源id');
//        $grid->setMeal_startdate('套餐产生的应收时的期限开始日期');
//        $grid->setMeal_enddate('套餐产生的应收时的期限结束日期');
        $grid->Remarks('备注');

        $grid->actions(function ($actions) {
            $actions->disableView();
            if (!Admin1::user()->can('应收管理删除')) {
                $actions->disableDelete();
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
    protected function createSetMeal($id=0)
    {
        $form = new Form(new Receivable);
        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
            $tools->disableDelete();
        });

        $item_no = $this->receivableNo();
        $receivable_svrkey = !empty($_GET['receivable_svrkey'])?$_GET['receivable_svrkey']:'';
        $setMeal = json_encode(collect(DB::table('setMeal')->get())->keyBy('setMeal_id')->toArray());
        $svrkey = '';
        $placehd = '';
        $placename = '';
        $place = '';
        if($receivable_svrkey){
            $svrkey = $receivable_svrkey;
            $placehd = DB::table('place')->where('key',$receivable_svrkey)->value('placehd');
            $placename = DB::table('place')->where('key',$receivable_svrkey)->value('placename');
            $place = DB::table('place')->where('key',$receivable_svrkey)->first();  //场所信息
            $place = json_encode($place);
        }
        if($id){
            $receivable = DB::table('receivable')->where('id',$id)->first();
            $key = $receivable->svrkey;
//            $key = DB::table('receivable')->where('id',$id)->value('svrkey');
            $place = DB::table('place')->where('key',$key)->first();  //场所信息
//            $placename = DB::table('place')->where('key',$key)->value('placename');
            $placename = $place->placename;
            $place = json_encode($place);
            if($receivable->completion_money>0){
                $form->disableSubmit();
                $form->disableReset();
            }
        }

            $form->hidden('svrkey', '场所key')->default($svrkey)->readonly()->required();
            $form->hidden('placehd', '场所服务器id')->default($placehd)->readonly()->required();
            $form->text('placename', '场所名称')->default($placename)->required()->readonly();
            $form->text('item_no', '单号')->default($item_no)->readonly();
        $form->select('item_source', '费项来源')->options([0=>'套餐',1=>'其它费项'])->readonly()->required();
            $form->select('item_Id', '费项名称')->options(DB::table('setMeal')->pluck('setMeal_name','setMeal_id'))->required();
            $form->number('item_num', '套餐份数')->default(1)->required();
            $form->number('item_price', '单价(元)')->required();
            $form->number('item_discount', '折扣')->required();
            $form->decimal('item_totalprice', '总价(元)')->readonly()->required();
            $form->date('item_date', '应收日期')->required()->default(date('Y-m-d'));
            $form->hidden('createDate', '产生时间')->default(date('Y-m-d H:i:s'))->required();
            $form->text('operation', '操作人')->default(Admin1::user()->name)->readonly()->required();
            $form->select('sourceType', '来源方式')->options([0=>'后台增加产生',1=>'场所支付产生'])->default(0)->readOnly();
            $form->text('Remarks', '备注');

        $form->ignore(['placename']);

        $triggerScript = $this->createTriggerScript($form);
        $subscribeScript = $this->createSubscriberScript($form, function($builder) use ($setMeal,$place){
            //费项名称
            $builder->subscribe('item_Id', 'select', function($event) use($setMeal,$place){
                //setMeal_mode,1：按有效机顶盒数量，2按固定费用
                return <<< EOT
                function (data) {
                    var setMeal = {$setMeal};
                    var place = {$place};
                    var setMeal_id = data.id;
                    var setMeal_mode = setMeal[setMeal_id].setMeal_mode;
                    $('.item_price').val(setMeal[setMeal_id].setMeal_price);
                    var par = $('.item_price').parent();
                    $('#roomtotal').remove();
                    if(setMeal_mode ==1){ 
                        $('.item_num').val(1);
                    par.append("<span id='roomtotal' style='line-height:-10px;padding-left:30px;vertical-align:top'>"+place.roomtotal+"个有效机顶盒</span>");
                        $('.item_discount').val(setMeal[setMeal_id].setMeal_discount);
                        var totalprice = place.roomtotal*$('.item_price').val()*$('.item_num').val()*$('.item_discount').val();
                        $('.item_totalprice').val(totalprice.toFixed(2));
                    }else{
                        $('.item_num').val(1);
                        $('.item_price').val(setMeal[setMeal_id].setMeal_price);
                        $('.item_discount').val(setMeal[setMeal_id].setMeal_discount);
                        var totalprice = $('.item_price').val()*$('.item_num').val()*$('.item_discount').val();
                        $('.item_totalprice').val(totalprice.toFixed(2));
                    }
                }
EOT;
            });
            //套餐份数
            $builder->subscribe('item_num', 'number_change', function ($event) use ($setMeal,$place) {
                return <<< EOT
                    function (data) {
                        var setMeal = {$setMeal};
                        var place = {$place};
                        var setMeal_id = $('.item_Id').val();
                        var setMeal_mode = setMeal[setMeal_id].setMeal_mode;
                        if(setMeal_mode ==1){
                           var totalprice = place.roomtotal*$('.item_price').val()*$('.item_num').val()*$('.item_discount').val();
                            $('.item_totalprice').val(totalprice.toFixed(2));
                        }else{
                            var totalprice = $('.item_price').val()*$('.item_num').val()*$('.item_discount').val();
                            $('.item_totalprice').val(totalprice.toFixed(2));
                        }
                    }
EOT;
            });
            //单价
            $builder->subscribe('item_price', 'number_change', function ($event) use ($setMeal,$place) {
                return <<< EOT
                    function (data) {
                        var setMeal = {$setMeal};
                        var place = {$place};
                        var setMeal_id = $('.item_Id').val();
                        var setMeal_mode = setMeal[setMeal_id].setMeal_mode;
                        if(setMeal_mode ==1){
                           var totalprice = place.roomtotal*$('.item_price').val()*$('.item_num').val()*$('.item_discount').val();
                            $('.item_totalprice').val(totalprice.toFixed(2));
                        }else{
                            var totalprice = $('.item_price').val()*$('.item_num').val()*$('.item_discount').val();
                            $('.item_totalprice').val(totalprice.toFixed(2));
                        }
                    }
EOT;
            });
            //折扣
            $builder->subscribe('item_discount', 'number_change', function ($event) use ($setMeal,$place) {
                return <<< EOT
                    function (data) {
                        var setMeal = {$setMeal};
                        var place = {$place};
                        var setMeal_id = $('.item_Id').val();
                        var setMeal_mode = setMeal[setMeal_id].setMeal_mode;
                        if(setMeal_mode ==1){
                           var totalprice = place.roomtotal*$('.item_price').val()*$('.item_num').val()*$('.item_discount').val();
                            $('.item_totalprice').val(totalprice.toFixed(2));
                        }else{
                            var totalprice = $('.item_price').val()*$('.item_num').val()*$('.item_discount').val();
                            $('.item_totalprice').val(totalprice.toFixed(2));
                        }
                    }
EOT;
            });


        });
        // 最后把 $triggerScript 和 $subscribeScript 注入到Form中去。
        // scriptinjecter 第一个参数可以为任何字符，但不能为空！！！！
        $form->scriptinjecter('any_name_but_no_empty', $triggerScript, $subscribeScript);

        $form->saving(function (Form $form) {

            if (!$id = $form->model()->id) {
                $form->item_no = $this->receivableNo();
            }

        });
        return $form;

    }


    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function createOtherFee($id=0)
    {
        $form = new Form(new Receivable);
        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
            $tools->disableDelete();
        });

        $item_no = $this->receivableNo();
        $receivable_svrkey = !empty($_GET['receivable_svrkey'])?$_GET['receivable_svrkey']:'';
        $svrkey = '';
        $placehd = '';
        $placename = '';
        if($receivable_svrkey){
            $svrkey = $receivable_svrkey;
            $placehd = DB::table('place')->where('key',$receivable_svrkey)->value('placehd');
            $placename = DB::table('place')->where('key',$receivable_svrkey)->value('placename');
        }

        if($id){
            $receivable = DB::table('receivable')->where('id',$id)->first();
            $key = $receivable->svrkey;
//            $key = DB::table('receivable')->where('id',$id)->value('svrkey');
            $placename = DB::table('place')->where('key',$key)->value('placename');
            if($receivable->completion_money>0){
                $form->disableSubmit();
                $form->disableReset();
            }
        }

        $form->hidden('svrkey', '场所key')->default($svrkey)->readonly()->required();
        $form->hidden('placehd', '场所服务器id')->default($placehd)->readonly()->required();
        $form->text('placename', '场所名称')->default($placename)->required()->readonly();
        $form->text('item_no', '单号')->default($item_no)->readonly();
        $form->select('item_source', '费项来源')->options([0=>'套餐',1=>'其它收费'])->default(1)->readonly()->required();
        $form->select('item_Id', '费项名称')->options(DB::table('itemfeename')->pluck('itemName','itemId'))->required();
        $form->decimal('item_totalprice', '总价(元)')->required();
        $form->date('item_date', '应收日期')->required()->default(date('Y-m-d'));
        $form->hidden('createDate', '产生时间')->default(date('Y-m-d H:i:s'))->required();
        $form->text('operation', '操作人')->default(Admin1::user()->name)->readonly()->required();
        $form->select('sourceType', '来源方式')->options([0=>'后台增加产生',1=>'场所支付产生'])->default(0)->readOnly();
        $form->text('Remarks', '备注');

        $form->ignore(['placename']);

        $form->saving(function (Form $form) {

            if (!$id = $form->model()->id) {
                $form->item_no = $this->receivableNo();
            }

        });

        return $form;
    }


    /**
     * @param Request $request 表单联动
     * @return array
     */
    public function itemId(Request $request)
    {
        $id = $request->get('q');

        if($id==0){
            return SetMeal::pluck('setMeal_name');
        }else{
            return [0=>'费项1',1=>'费项2'];
        }

//        return ChinaArea::city()->where('parent_id', $provinceId)->get(['id', DB::raw('name as text')]);
    }

    /*
     * 生成单号
     */
    public function receivableNo()
    {
        $data = DB::select("select sP_Djno('YS',0) as number limit 1");
        $receivableNo = $data[0]->number;
        $exists = DB::table('receivable')->where('item_no', $receivableNo)->exists();
        if (!$exists) {
//            DB::select("select sP_Djno('YS',1)");
            return $receivableNo;
        } else {
            while (true) {
                $data = DB::select("select sP_Djno('YS',1) as number limit 1");
                $receivableNo = $data[0]->number;
                $exists = DB::table('receivable')->where('item_no', $receivableNo)->exists();
                if (!$exists) {
                    //DB::select("select sP_Djno('YS',1)");
                    return $receivableNo;
                }
            }
        }
    }


}
