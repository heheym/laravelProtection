<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Place;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;
use Encore\Admin\Facades\Admin;

use Field\Interaction\FieldTriggerTrait;
use Field\Interaction\FieldSubscriberTrait;

class PlaceController extends Controller
{
    use HasResourceActions,FieldTriggerTrait, FieldSubscriberTrait;

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
        $grid = new Grid(new Place);
        $grid->setView('place.index');

//        $grid->disableColumnSelector();
        $grid->disableExport();
//        $grid->disableCreateButton();

//        $grid->disableFilter(false);
        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->column(1/2, function ($filter) {
                $filter->like('key', 'key');
                $filter->like('placename', '场所名称');
                $wangMode = DB::table('warningmode')->pluck('warningName','id')->toArray();
                $filter->equal('wangMode','预警模式')->select($wangMode);
//                $setMeal = DB::table('setMeal')->pluck('setMeal_name','setMeal_id')->toArray();
//                $filter->equal('setMeal','套餐')->select($setMeal);
                $filter->equal('status','状态')->select([0=>'未启用',1=>'已启用']);
            });

            $filter->column(1/2, function ($filter) {
//                $filter->where(function ($query) {
//                    $query->where('province', 'like', "%{$this->input}%")
//                        ->orWhere('city', 'like', "%{$this->input}%");
//                }, '省市');

                $filter->equal('province');
                $filter->equal('city');
//                $filter->like('province', '省');
//                $filter->like('city', '市');
                $filter->like('contacts', '联系人');
                $filter->like('phone', '手机号');
                $filter->between('expiredata', '场所有效时间')->datetime();
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
        $grid->actions(function ($actions) {
            $actions->disableView();
            if (!Admin::user()->can('场所删除')) {
                $actions->disableDelete();
            }
        });
        if (!Admin::user()->can('场所添加')) {
            $grid->disableCreateButton();  //场所添加的权限
        }

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
//        $show = new Show(User::findOrFail($id));
//
//        $show->id('id');
//        $show->name('Name');
//        $show->email('Email');
//        $show->phone('Phone');
//        $show->password('Password');
//        $show->remember_token('Remember token');
//        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        Admin::script('openingTime();');
        $form = new Form(new Place);
        $form->hidden('Opening1_time');
        $form->hidden('Opening1_price');
        $form->hidden('Effective1_time');
        $form->hidden('Opening2_time');
        $form->hidden('Opening2_price');
        $form->hidden('Effective2_time');

        $form->hidden('Place_Royalty');
        $form->hidden('Place_Settlement');
        $form->hidden('Agent_Royalty');
        $form->hidden('Agent_Settlement');
        $form->hidden('Obligee_Royalty');
        $form->hidden('Obligee_Settlement');
//        $placeaddress = $mailbox  = $phone = $contacts = $tel = '';
        $form->hidden('placeaddress');
        $form->hidden('mailbox');
        $form->hidden('phone');
        $form->hidden('contacts');
        $form->hidden('tel');

        $form->text('userno', '场所编号')->placeholder('自动生成')->readOnly();
        $form->text('key', 'key')->placeholder('自动生成')->readOnly();
        $form->text('placehd', '场所服务器ID');

        $form->text('placename', '场所名称')->rules(function ($form) {
            // 如果不是编辑状态，则添加字段唯一验证
            if (!$id = $form->model()->id) {
                return 'unique:users,placename';
            }
        });
        $form->number('boxPass', '机顶盒设置密码')->default('888888')->rules('required|regex:/^\d+$/',['regex' => '必须全部为数字']);

        $wangMode = DB::table('warningmode')->pluck('warningName','id')->toArray();
//        $form->select('wangMode', '预警模式')->options($wangMode);
        $form->number('warningRoomcount', '房间预警数量')->default(0);
        $form->number('warningCutsongcount', '切歌预警数量')->default(0);
        $form->select('FeesMode', '收费模式')->options([0=>'其它收费模式',1=>'开房收费模式']);
//        $form->timeRange('Opening1_time', 'Opening1_time', '开房时段一');
        $id = request()->route()->parameters('id');
        $time1 = '00:00';
        $time2 = '00:00';
        $time3 = '00:00';
        $time4 = '00:00';
        $Opening1_price = $Effective1_time = $Opening2_price = $Effective2_time = 0;
        $Place_Royalty = $Agent_Royalty = $Obligee_Royalty = 0;
        $Place_Settlement = $Agent_Settlement = $Obligee_Settlement = 1;
        $placeaddress = $mailbox  = $phone = $contacts = $tel = '';
        if(!empty($id)){
            $place = DB::table('place')->where('id',$id)->first();
            $time1 = explode('-',$place->Opening1_time)[0];
            $time2 = explode('-',$place->Opening1_time)[1];
            $time3 = explode('-',$place->Opening2_time)[0];
            $time4 = explode('-',$place->Opening2_time)[1];
            $Opening1_price = $place->Opening1_price;
            $Effective1_time = $place->Effective1_time;
            $Opening2_price = $place->Opening2_price;
            $Effective2_time = $place->Effective2_time;

            $Place_Royalty = $place->Place_Royalty;
            $Place_Settlement = $place->Place_Settlement;
            $Agent_Royalty = $place->Agent_Royalty;
            $Agent_Settlement = $place->Agent_Settlement;
            $Obligee_Royalty = $place->Obligee_Royalty;
            $Obligee_Settlement = $place->Obligee_Settlement;
 //        $placeaddress = $mailbox  = $phone = $contacts = $tel = '';
            $placeaddress = $place->placeaddress;
            $mailbox = $place->mailbox;
            $phone = $place->phone;
            $contacts = $place->contacts;
            $tel = $place->tel;
        }

        $form->html('
        <div class="form-inline feesmode">
               <input type="text" name="time1" value="'.$time1.'" class="form-control time1" style="width: 60px" required>&nbsp;&nbsp;至&nbsp;&nbsp;
               <input type="text" name="time2" value="'.$time2.'" class="form-control time2" style="width: 60px" required>
                <label class="form-inline" style="margin-left:10px">*单价(元)：<input type="text" class="form-control" name="Opening1_price" required value="'.$Opening1_price.'" /></label>
                <label class="form-inline" style="margin-left:10px">*有效时长(分钟)：<input type="text" class="form-control" name="Effective1_time" required value="'.$Effective1_time.'" /></label>
            </div>
','*开房时段一');

        $form->html('
        <div class="form-inline feesmode">
               <input type="text" name="time3" value="'.$time3.'" class="form-control time3" style="width: 60px" required>&nbsp;&nbsp;至&nbsp;&nbsp;
               <input type="text" name="time4" value="'.$time4.'" class="form-control time4" style="width: 60px" required>
                <label class="form-inline" style="margin-left:10px">*单价(元)：<input type="text" class="form-control" name="Opening2_price" required value="'.$Opening2_price.'" /></label>
                <label class="form-inline" style="margin-left:10px">*有效时长(分钟)：<input type="text" class="form-control" name="Effective2_time" required value="'.$Effective2_time.'" /></label>
            </div>
','*开房时段二');

        $form->html('
        <div class="form-inline feesmode">
               <input type="text" name="Place_Royalty" value="'.$Place_Royalty.'" class="form-control Place_Royalty" style="width: 60px" required>
                <label class="form-inline" style="margin-left:10px">*场所分成结算方式：
                <select style="width:100px;height:30px" name="Place_Settlement">
                    <option value="1" '.($Place_Settlement==1?"selected":"").'>按月结算</option>
                    <option value="2" '.($Place_Settlement==2?"selected":"").'>按季结算</option>
                    <option value="3" '.($Place_Settlement==3?"selected":"").'>按年结算</option>
                    </select>
                </label>
            </div>
','*场所分成比例');
        $form->html('
        <div class="form-inline feesmode">
               <input type="text" name="Agent_Royalty" value="'.$Agent_Royalty.'" class="form-control Agent_Royalty" style="width: 60px" required>
                <label class="form-inline" style="margin-left:10px">*代理商分成结算方式：
                <select style="width:100px;height:30px" name="Agent_Settlement">
                    <option value="1" '.($Agent_Settlement==1?"selected":"").'>按月结算</option>
                    <option value="2" '.($Agent_Settlement==2?"selected":"").'>按季结算</option>
                    <option value="3" '.($Agent_Settlement==3?"selected":"").'>按年结算</option>
                    </select>
                </label>
            </div>
','*代理商分成比例');
        $form->html('
        <div class="form-inline feesmode">
               <input type="text" name="Obligee_Royalty" value="'.$Obligee_Royalty.'" class="form-control Obligee_Royalty" style="width: 60px" required>
                <label class="form-inline" style="margin-left:10px">*权利人分成结算方式：
                <select style="width:100px;height:30px" name="Obligee_Settlement">
                    <option value="1" '.($Obligee_Settlement==1?"selected":"").'>按月结算</option>
                    <option value="2" '.($Obligee_Settlement==2?"selected":"").'>按季结算</option>
                    <option value="3" '.($Obligee_Settlement==3?"selected":"").'>按年结算</option>
                    </select>
                </label>
            </div>
','*权利人分成比例');

        //地址邮箱。。
        $form->html('
        <div class="form-inline">
               <input type="text" name="placeaddress" value="'.$placeaddress.'" class="form-control placeaddress" style="width: 150px" >
                <label class="form-inline" style="margin-left:5px">邮箱：
                <input type="text" name="mailbox" value="'.$mailbox.'" class="form-control mailbox" style="width: 90px" >
                </label>
                <label class="form-inline" style="margin-left:5px">手机号：
                <input type="text" name="phone" value="'.$phone.'" class="form-control phone" style="width: 90px" >
                </label>
                <label class="form-inline" style="margin-left:5px">联系人：
                <input type="text" name="contacts" value="'.$contacts.'" class="form-control contacts" style="width: 90px" >
                </label>
                </label>
                <label class="form-inline" style="margin-left:5px">联系电话：
                <input type="text" name="tel" value="'.$tel.'" class="form-control tel" style="width: 90px" >
                </label>
            </div>
','地址');

//        $form->text('placeaddress', '地址');
//        $form->email('mailbox', '邮箱');
//        $form->text('phone', '手机号');
//        $form->text('contacts', '联系人');
//        $form->text('tel', '联系电话');
        $form->number('roomtotal', '机顶盒数量');
        $form->datetime('created_date', '注册时间');
        $form->datetime('expiredata', '场所有效时间');
        $form->select('status', '状态')->options([0=>'未启用',1=>'已启用']);
        $form->hidden('key', 'key');
        $form->text('country', '国')->default('中国');
        $form->distpicker(['province', 'city', 'placArea']);
//        $form->text('province', '省');
//        $form->text('city', '市');
        $form->select('downloadMode', '歌曲下载方式')->options([1=>'不下载',2=>'点播下载',3=>'智能下载']);
        $form->select('apkUpdateMode', '机顶盒apk版本更新方式')->options([1=>'不更新',2=>'必须更新'])->default(2);
        $form->select('isclosePingfen', '是否要关闭评分功能')->options([0=>'不关闭',1=>'关闭'])->default(0);
        $form->select('iscloseSound', '是否要关闭录音功能')->options([0=>'不关闭',1=>'关闭'])->default(0);

        $form->saving(function (Form $form) {
            $form->key = !empty($form->model()->key)?$form->model()->key:strtoupper(str_random(12));
            $form->userno = !empty($form->model()->userno)?$form->model()->userno:time();

            $form->Opening1_time = request('time1').'-'.request('time2');
//            $form->Opening1_price = request('Opening1_price');
//            $form->Effective1_time = request('Effective1_time');
            $form->Opening2_time = request('time3').'-'.request('time4');
//            $form->Opening2_price = request('Opening2_price');
//            $form->Effective2_time = request('Effective2_time');

//            $form->Place_Royalty = request('Place_Royalty');
//            $form->Place_Settlement = request('Place_Settlement');
//            $form->Agent_Royalty = request('Agent_Royalty');
//            $form->Agent_Settlement = request('Agent_Settlement');
//            $form->Obligee_Royalty = request('Obligee_Royalty');
//            $form->Obligee_Settlement = request('Obligee_Settlement');
        });

        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
        });

        $triggerScript = $this->createTriggerScript($form);
        $subscribeScript = $this->createSubscriberScript($form, function($builder){
            //费项名称
            $builder->subscribe('FeesMode', 'select', function($event){
                //setMeal_mode,1：按有效机顶盒数量，2按固定费用
                return <<< EOT
                function (data) {
                    var id = data.id;
                    if(id ==0){ 
                        $('.feesmode').parents('.form-group').hide();
                    }else if(id ==1){
                        $('.feesmode').parents('.form-group').show();
                    }
                }
EOT;
            });
        });
        // 最后把 $triggerScript 和 $subscribeScript 注入到Form中去。
        // scriptinjecter 第一个参数可以为任何字符，但不能为空！！！！
        $form->scriptinjecter('any_name_but_no_empty', $triggerScript, $subscribeScript);
        return $form;
    }

    public function destroy($id)
    {
        $key = DB::table('place')->where('id',$id)->value('key');
        $exists = DB::table('settopbox')->where('key',$key)->exists();
        if($exists){
            return response()->json([
                'status'  => false,
                'message' => '场所下有机顶盒,不能删除',
            ]);
        }
        return $this->form()->destroy($id);
    }
}
