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

        $grid->filter(function($filter){

            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            // 在这里添加字段过滤器
//            $filter->like('place', '场所');
            $filter->like('Songname', '歌名');
            $filter->equal('OnlineStatus','上架状态')->select([0=>'下架',1=>'上架']);
            $filter->equal('VersionType','视频版本')->select([1=>'MTV',2=>'演唱会',3=>'影视剧情',
                4=>'人物',5=>'风景',6=>'动画',7=>'其他']);
            $wangMode = DB::table('warningmode')->pluck('warningName','id')->toArray();
            $filter->equal('wangMode','预警模式')->select($wangMode);
            $setMeal = DB::table('setMeal')->pluck('setMeal_name','setMeal_id')->toArray();
            $filter->equal('setMeal','套餐')->select($setMeal);
        });

        $grid->id('Id');
        $grid->userno('场所编号');
        $grid->key('key');
        $grid->placehd('场所服务器ID');
        $grid->placename('场所名称');
        $grid->mailbox('邮箱');
        $grid->phone('手机号');
        $grid->contacts('联系人');
        $grid->tel('联系电话');
        $grid->placeaddress('地址');
        $grid->roomtotal('机顶盒数量');
        $grid->expiredata('场所有效时间');
        $grid->country('国家');
        $grid->province('省');
        $grid->city('市');


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
        $grid->setMeal('套餐')->display(function ($setMeal) {
            if(!is_null($setMeal)){
                return DB::table('setMeal')->where('setMeal_id',$setMeal)->value('setMeal_name');
            }
        });

//
//        $grid->vipState('会员状态')->display(function ($vipState) {
//            $arra = [0=>'试用会员',1=>'付费会员',2=>'已过期会员'];
//            return $arra[$vipState];
//        });
//
//        $grid->vipXgStartDay('可浏览天数');
//        $grid->vipStartTime('会员开始时间');
//        $grid->vipTime('会员到期时间');
//
//        $grid->download('可下载次数');
//        $grid->add('是否可以补歌')->display(function ($add) {
//            return $add?'是':'否';
//        });

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
        $show = new Show(User::findOrFail($id));

        $show->id('id');
        $show->name('Name');
        $show->email('Email');
        $show->phone('Phone');
        $show->password('Password');
        $show->remember_token('Remember token');
//        $show->created_at('Created at');
//        $show->updated_at('Updated at');
//        $show->api_token('Api token');
//        $show->mac('Mac');
//        $show->vipTime('VipTime');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Place);

        $form->text('userno', '场所编号')->placeholder('自动生成')->readOnly();
        $form->text('key', 'key')->placeholder('自动生成')->readOnly();
        $form->text('placehd', '场所服务器ID')->required();

        $form->text('placename', '场所名称')->rules(function ($form) {
            // 如果不是编辑状态，则添加字段唯一验证
            if (!$id = $form->model()->id) {
                return 'unique:users,placename';
            }
        });

        $wangMode = DB::table('warningmode')->pluck('warningName','id')->toArray();
        $form->select('wangMode', '预警模式')->options($wangMode);

        $setMeal = DB::table('setMeal')->pluck('setMeal_name','setMeal_id')->toArray();
        $form->select('setMeal', '套餐')->options($setMeal);

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
        $form->text('country', '国');
        $form->text('province', '省');
        $form->text('city', '市');


        $form->saving(function (Form $form) {

            $form->key = !empty($form->model()->key)?$form->model()->key:strtoupper(str_random(12));
            $form->userno = !empty($form->model()->userno)?$form->model()->userno:time();

        });
//        $form->text('mac', 'Mac');
//        $form->select('vipState', '会员状态')->options([0=>'试用会员',1=>'付费会员',2=>'已过期会员']);
//        $form->text('vipXgStartDay', '可浏览天数');
//        $form->datetime('vipStartTime', '会员开始时间');
//        $form->datetime('vipTime', '会员到期时间');
//        $form->text('download', '可下载次数');
//        $form->switch('add', '是否可以补歌');

        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
            $tools->append();
        });

        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = bcrypt($form->password);
            }
        });

        return $form;
    }
}
