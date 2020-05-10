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

class SetTopBoxController extends Controller
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

        $grid->disableFilter(false);
        $grid->filter(function($filter){

            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            $filter->column(1/2,function($filter){
                $filter->like('key', 'key');
                $filter->where(function ($query) {
                    $query->whereHas('place', function ($query) {
                        $query->where('placename', 'like', "%{$this->input}%");
                    });
                }, '场所');
                $filter->equal('KtvBoxState','状态')->select([0=>'待审核',1=>'正常',2=>'返修',3=>'过期',4=>'作废']);
            });
            $filter->column(1/2,function($filter){
                $filter->where(function ($query) {
                    $query->whereHas('place', function ($query) {
                        $query->where('province', 'like', "%{$this->input}%")
                            ->orWhere('city', 'like', "%{$this->input}%");
                    });
                }, '省市');
                $filter->where(function ($query) {
                    $query->whereHas('place', function ($query) {
                        $query->where('contacts', 'like', "%{$this->input}%");
                    });
                }, '联系人');
            });


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
            $city = DB::table('place')->where('key',$this->key )->value('city');
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
        $form->datetime('created_date', '启用日期');

        $form->text('mark', '备注');

        $form->select('FeesMode', '收费模式')->options([0=>'按场所模式',1=>'版权收费模式']);
        $id = request()->route()->parameters('id');
        $time1 = '00:00';
        $time2 = '00:00';
        $time3 = '00:00';
        $time4 = '00:00';
        if(!empty($id)){
            $place = DB::table('settopbox')->where('id',$id)->select('Opening1_time','Opening2_time')->first();
            $time1 = explode('-',$place->Opening1_time)[0];
            $time2 = explode('-',$place->Opening1_time)[1];
            $time3 = explode('-',$place->Opening2_time)[0];
            $time4 = explode('-',$place->Opening2_time)[1];
        }

        $form->html('
        <div class="row" style="width: 370px">
            <div class="col-lg-6">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    <input type="text" name="time1" value="'.$time1.'" class="form-control time1" style="width: 150px">
                </div>
            </div>

            <div class="col-lg-6">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    <input type="text" name="time2" value="'.$time2.'" class="form-control time2" style="width: 150px">
                </div>
            </div>
        </div>
', '开房时段一');
        $form->decimal('Opening1_price', '时段一单价(元)');
        $form->decimal('Effective1_time', '时段一有效时长(分钟)');
        $form->html('
        <div class="row" style="width: 370px">
            <div class="col-lg-6">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    <input type="text" name="time3" value="'.$time3.'" class="form-control time3" style="width: 150px">
                </div>
            </div>

            <div class="col-lg-6">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    <input type="text" name="time4" value="'.$time4.'" class="form-control time4" style="width: 150px">
                </div>
            </div>
        </div>
', '开房时段二');
        $form->decimal('Opening2_price', '时段二单价(元)');
        $form->decimal('Effective2_time', '时段二有效时长(分钟)');
        $form->decimal('Place_Royalty', '场所分成比例')->default(0)->rules('numeric|between:0,1',['between'=>'必须0到1之间']);
        $form->select('Place_Settlement', '场所分成结算方式')->options([1=>'按月结算',2=>'季结算',3=>'按年结算']);
        $form->decimal('Agent_Royalty', '代理商分成比例')->default(0)->rules('numeric|between:0,1',['between'=>'必须0到1之间']);;
        $form->select('Agent_Settlement', '代理商分成结算方式')->options([1=>'按月结算',2=>'季结算',3=>'按年结算']);
        $form->decimal('Obligee_Royalty', '权利人分成比例')->default(0)->rules('numeric|between:0,1',['between'=>'必须0到1之间']);;
        $form->select('Obligee_Settlement', '权利人分成结算方式')->options([1=>'按月结算',2=>'季结算',3=>'按年结算']);

        $form->hidden('mark', '备注')->rules(function ($form) {

        // 如果不是编辑状态，则添加字段唯一验证
        if (!$id = $form->model()->id) {
            return 'unique:settopbox,KtvBoxid';
        }

    });;



        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
        });

        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = bcrypt($form->password);
            }



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


        return $form;
    }
}
