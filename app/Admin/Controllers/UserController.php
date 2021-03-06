<?php

namespace App\Admin\Controllers;

use App\Admin\Models\User;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Facades\Admin;

class UserController extends Controller
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
        $grid = new Grid(new User);

        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->like('userno','用户名');
        });


//        $grid->id('Id');
        $grid->userno('用户名');
        $grid->email('邮箱');
        $grid->phone('手机号');
//        $grid->mac('Mac');
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
        $form = new Form(new User);

        $form->text('userno', '用户名')->rules(function ($form) {
            // 如果不是编辑状态，则添加字段唯一验证
            if (!$id = $form->model()->id) {
                return 'unique:users,userno';
            }
        });
        $form->email('email', '邮箱');
        $form->text('phone', '手机号');
        $form->password('password', '密码');
//        $form->text('mac', 'Mac');

//        $form->select('vipState', '会员状态')->options([0=>'试用会员',1=>'付费会员',2=>'已过期会员']);
//        $form->text('vipXgStartDay', '可浏览天数');
//        $form->datetime('vipStartTime', '会员开始时间');
//        $form->datetime('vipTime', '会员到期时间');
//        $form->text('download', '可下载次数');
//        $form->switch('add', '是否可以补歌');

        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
        });

        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = bcrypt($form->password);
            }
        });

        return $form;
    }


}