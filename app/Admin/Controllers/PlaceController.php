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

class PlaceController extends Controller
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
        $grid = new Grid(new Place);
        $grid->disableFilter(false);

        $grid->filter(function($filter){

            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            // 在这里添加字段过滤器


            $filter->column(1/2, function ($filter) {
                $filter->like('key', 'key');
                $filter->like('placename', '场所名称');
                $wangMode = DB::table('warningmode')->pluck('warningName','id')->toArray();
                $filter->equal('wangMode','预警模式')->select($wangMode);
                $setMeal = DB::table('setMeal')->pluck('setMeal_name','setMeal_id')->toArray();
                $filter->equal('setMeal','套餐')->select($setMeal);
                $filter->equal('status','状态')->select([0=>'未启用',1=>'已启用']);
            });

            $filter->column(1/2, function ($filter) {
                $filter->where(function ($query) {
                    $query->where('province', 'like', "%{$this->input}%")
                        ->orWhere('city', 'like', "%{$this->input}%");
                }, '省市');
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
//        $grid->setMeal('套餐')->display(function ($setMeal) {
//            if(!is_null($setMeal)){
//                return DB::table('setMeal')->where('setMeal_id',$setMeal)->value('setMeal_name');
//            }
//        });

        $grid->actions(function ($actions) {
            $actions->disableView();
            if (!Admin::user()->can('场所删除')) {
                $actions->disableDelete();
            }
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
        $form->select('wangMode', '预警模式')->options($wangMode);
        $form->select('FeesMode', '收费模式')->options([0=>'其它模式',1=>'版权收费']);
//        $form->timeRange('Opening1_time', 'Opening1_time', '开房时段一');
        $id = request()->route()->parameters('id');
        $time1 = '00:00';
        $time2 = '00:00';
        $time3 = '00:00';
        $time4 = '00:00';
        if(!empty($id)){
            $place = DB::table('place')->where('id',$id)->select('Opening1_time','Opening2_time')->first();
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
        $form->decimal('Agent_Royalty', '代理商分成比例')->default(0);
        $form->select('Agent_Settlement', '代理商分成结算方式')->options([1=>'按月结算',2=>'季结算',3=>'按年结算']);
        $form->decimal('Obligee_Royalty', '权利人分成比例')->default(0);
        $form->select('Obligee_Settlement', '权利人分成结算方式')->options([1=>'按月结算',2=>'季结算',3=>'按年结算']);

        $form->text('placeaddress', '地址');
        $form->email('mailbox', '邮箱');
        $form->text('phone', '手机号');
        $form->text('contacts', '联系人');
        $form->text('tel', '联系电话');
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
        $form->hidden('Opening1_time');
        $form->hidden('Opening2_time');
        $form->saving(function (Form $form) {
            $form->key = !empty($form->model()->key)?$form->model()->key:strtoupper(str_random(12));
            $form->userno = !empty($form->model()->userno)?$form->model()->userno:time();
            $form->Opening1_time = request('time1').'-'.request('time2');
            $form->Opening2_time = request('time3').'-'.request('time4');
        });

        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
        });


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
