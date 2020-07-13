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
use function foo\func;

class MerchantSetController extends Controller
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

        $grid->disableColumnSelector();
        $grid->disableExport();
        $grid->disableCreateButton();

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
            $actions->disableDelete();
//            if (!Admin::user()->can('场所删除')) {
//                $actions->disableDelete();
//            }
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
//        Admin::script('openingTime();');
        $form = new Form(new Place);

        $form->column(5/6, function ($form) {

            $form->text('placename', '场所名称')->readonly();
            //商户
            $merchanttable = DB::table('Merchanttable')->select('merchantId', 'merchantName')->get();
            foreach ($merchanttable as $k => $v) {
                $merchantOption[$v->merchantId] = '('.$v->merchantId.')'.$v->merchantName;
            }
            $form->table('merchantfirst', '主体商户', function ($table) use ($merchantOption) {
                $table->select('merchantId', '商户名')->options($merchantOption)->required();
//                $table->number('shareproportion', '分成比例(%)')->default(10)->rules('numeric|between:1,100');
            })->disableCreate()->disableDelete();
            $form->table('merchant', '商户', function ($table) use ($merchantOption) {
                $table->select('merchantId', '商户名')->options($merchantOption);
                $table->number('shareproportion', '分成比例(%)')->default(10)->rules('numeric|between:1,100');
            });
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
