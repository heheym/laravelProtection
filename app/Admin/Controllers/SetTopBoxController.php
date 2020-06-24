<?php

namespace App\Admin\Controllers;

use App\Admin\Models\SetTopBox;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use App\Admin\Actions\SetTopBox\BatchChange;
use Encore\Admin\Facades\Admin;

use Field\Interaction\FieldTriggerTrait;
use Field\Interaction\FieldSubscriberTrait;

class SetTopBoxController extends Controller
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
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new SetTopBox);
        $grid->setView('settopbox.index');

        $where = [];
        if(!empty(request('placename'))){
            $placename =request('placename');
            $where[] = ['placename','like','%'.$placename.'%'];
        }
        if(!empty(request('contacts'))){
            $contacts =request('contacts');
            $where[] = ['contacts','like','%'.$contacts.'%'];
        }
        if(!empty(request('province'))){
            $province =request('province');
            $where[] = ['province','like','%'.$province.'%'];
        }
        if(!empty(request('city'))){
            $city =request('city');
            $where[] = ['city','like','%'.$city.'%'];
        }

        $grid->model()->whereHas('place', function ($query) use($where){
            $query->where($where);
        });

//        $grid->disableFilter(false);
        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->like('key', 'key');
            $filter->like('KtvBoxid', 'KtvBoxid');
            $filter->like('machineCode', 'machineCode');
            $filter->equal('KtvBoxState','状态')->select([0=>'待审核',1=>'正常',2=>'返修',3=>'过期',4=>'作废']);
        });

        $grid->id('Id');
        $grid->key('Key');
        $grid->KtvBoxid('机器码');
        $grid->machineCode('机顶盒MAC');
        $grid->roomno('房号');

        $grid->KtvBoxState('状态')->display(function ($KtvBoxState) {
            if(!is_null($KtvBoxState)){
                $arra = [0=>'待审核',1=>'正常',2=>'返修',3=>'过期',4=>'作废'];
                return $arra[$KtvBoxState];
            }
        });
        $grid->created_date('启用日期');

        // 添加不存在的字段
        $grid->place('场所')->display(function () {
            return DB::table('place')->where('key',$this->key )->value('placename');
        });
        $grid->contact('联系人')->display(function () {
            return DB::table('place')->where('key',$this->key )->value('contacts');
        });
        $grid->address('省市')->display(function () {
            $province = DB::table('place')->where('key',$this->key )->value('province');
            if(!is_null($province)){
                $province =  DB::table('china_area')->where('code',$province)->value('name');
            }
            $city = DB::table('place')->where('key',$this->key )->value('city');
            if(!is_null($province)){
                $city =  DB::table('china_area')->where('code',$city)->value('name');
            }
            return $province.$city;
        });


        $grid->mark('备注');

//        $grid->batchActions(function ($batch) {
//            $batch->add(new BatchChange(1));
//        });

        $grid->tools(function (Grid\Tools $tools) {
            $tools->append(new BatchChange());
        });
        $grid->actions(function ($actions) {
            $actions->disableView();
            if (!Admin::user()->can('机顶盒删除')) {
                $actions->disableDelete();
            }
        });

        if (!Admin::user()->can('机顶盒添加')) {
            $grid->disableCreateButton();  //场所添加的权限
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
        $show = new Show(SetTopBox::findOrFail($id));

        $show->id('Id');
        $show->KtvBoxid('KtvBoxid');
        $show->machineCode('MachineCode');
        $show->roomno('Roomno');
        $show->key('Key');
        $show->status('Status');
        $show->created_date('Created date');
        $show->mark('Mark');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        Admin::script('openingTime();');
        $form = new Form(new SetTopBox);

        $form->hidden('Opening1_time');
        $form->hidden('Opening1_price');
        $form->hidden('Effective1_time');
        $form->hidden('Opening2_time');
        $form->hidden('Opening2_price');
        $form->hidden('Effective2_time');

//        $form->hidden('Place_Royalty');
//        $form->hidden('Place_Settlement');
//        $form->hidden('Agent_Royalty');
//        $form->hidden('Agent_Settlement');
//        $form->hidden('Obligee_Royalty');
//        $form->hidden('Obligee_Settlement');

        $form->text('key', 'Key')->required()->rules(function ($form) {
            return 'exists:place,key';
            // 如果不是编辑状态，则添加字段唯一验证
            if (!$id = $form->model()->id) {

            }

        });

//        $form->text('KtvBoxid', '机顶盒MAC')->required();

        $form->text('KtvBoxid', '机器码')->required()->rules(function ($form) {

            // 如果不是编辑状态，则添加字段唯一验证
            if (!$id = $form->model()->id) {
                return 'unique:settopbox,KtvBoxid';
            }

        });

        $form->text('machineCode', '机顶盒MAC');
        $form->text('roomno', '房号');
        $form->select('KtvBoxState', '状态')->options([0=>'待审核',1=>'正常',2=>'返修',3=>'过期',4=>'作废']);
        $form->datetime('created_date', '启用日期')->default(date('Y-m-d H:i:s'));

        $form->text('mark', '备注');

        $form->select('FeesMode', '收费模式')->options([0=>'场所收费模式',1=>'开房收费模式']);
        $id = request()->route()->parameters('id');
        $time1 = '00:00';
        $time2 = '00:00';
        $time3 = '00:00';
        $time4 = '00:00';
        $Opening1_price = $Effective1_time = $Opening2_price = $Effective2_time = 0;
        $Place_Royalty = $Agent_Royalty = $Obligee_Royalty = 0;
        $Place_Settlement = $Agent_Settlement = $Obligee_Settlement = 1;
        if(!empty($id)){
            $place = DB::table('settopbox')->where('id',$id)->first();
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

//        $form->html('
//        <div class="form-inline feesmode">
//               <input type="text" name="Place_Royalty" value="'.$Place_Royalty.'" class="form-control Place_Royalty" style="width: 60px" required>
//                <label class="form-inline" style="margin-left:10px">*场所分成结算方式：
//                <select style="width:100px;height:30px" name="Place_Settlement">
//                    <option value="1" '.($Place_Settlement==1?"selected":"").'>按月结算</option>
//                    <option value="2" '.($Place_Settlement==2?"selected":"").'>按季结算</option>
//                    <option value="3" '.($Place_Settlement==3?"selected":"").'>按年结算</option>
//                    </select>
//                </label>
//            </div>
//','*场所分成比例');
//        $form->html('
//        <div class="form-inline feesmode">
//               <input type="text" name="Agent_Royalty" value="'.$Agent_Royalty.'" class="form-control Agent_Royalty" style="width: 60px" required>
//                <label class="form-inline" style="margin-left:10px">*代理商分成结算方式：
//                <select style="width:100px;height:30px" name="Agent_Settlement">
//                    <option value="1" '.($Agent_Settlement==1?"selected":"").'>按月结算</option>
//                    <option value="2" '.($Agent_Settlement==2?"selected":"").'>按季结算</option>
//                    <option value="3" '.($Agent_Settlement==3?"selected":"").'>按年结算</option>
//                    </select>
//                </label>
//            </div>
//','*代理商分成比例');
//        $form->html('
//        <div class="form-inline feesmode">
//               <input type="text" name="Obligee_Royalty" value="'.$Obligee_Royalty.'" class="form-control Obligee_Royalty" style="width: 60px" required>
//                <label class="form-inline" style="margin-left:10px">*权利人分成结算方式：
//                <select style="width:100px;height:30px" name="Obligee_Settlement">
//                    <option value="1" '.($Obligee_Settlement==1?"selected":"").'>按月结算</option>
//                    <option value="2" '.($Obligee_Settlement==2?"selected":"").'>按季结算</option>
//                    <option value="3" '.($Obligee_Settlement==3?"selected":"").'>按年结算</option>
//                    </select>
//                </label>
//            </div>
//','*权利人分成比例');

        $form->hidden('mark', '备注')->rules(function ($form) {
        // 如果不是编辑状态，则添加字段唯一验证
            if (!$id = $form->model()->id) {
                return 'unique:settopbox,KtvBoxid';
            }
        });



        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
        });

        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = bcrypt($form->password);
            }
            $form->Opening1_time = request('time1').'-'.request('time2');
            $form->Opening1_price = request('Opening1_price');
            $form->Effective1_time = request('Effective1_time');
            $form->Opening2_time = request('time3').'-'.request('time4');
            $form->Opening2_price = request('Opening2_price');
            $form->Effective2_time = request('Effective2_time');

            $form->Place_Royalty = request('Place_Royalty');
            $form->Place_Settlement = request('Place_Settlement');
            $form->Agent_Royalty = request('Agent_Royalty');
            $form->Agent_Settlement = request('Agent_Settlement');
            $form->Obligee_Royalty = request('Obligee_Royalty');
            $form->Obligee_Settlement = request('Obligee_Settlement');

            $count2 = DB::table('settopbox')->where(['key'=> $form->key])
                ->where(function ($query) {
                    $query->where('KtvBoxState',0)
                        ->orWhere('KtvBoxState', 1);
                })->count();

            $count3 = $count2+1;
            $roomtotal = DB::table('place')->where(['key'=> $form->key])->value('roomtotal');
            $error = new MessageBag([
                'title'   => '提示',
                'message' => '机顶盒超过有效数量',
            ]);
            if($count3>$roomtotal){
                return back()->with(compact('error'));
            }
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
        $form->scriptinjecter('any_name_but_no_empty', $triggerScript, $subscribeScript);

        return $form;
    }
}
