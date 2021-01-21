<?php

namespace App\Admin\Controllers;

use App\Admin\Models\UsersOpenclose;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

use Encore\Admin\Facades\Admin;

class UsersOpencloseController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '开关房记录';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new UsersOpenclose);
        $grid->setView('usersopenclose.index');

        $grid->filter(function ($filter){
            $filter->disableIdFilter();
            $filter->like('srvkey','srvkey');
            $filter->like('KtvBoxid','KtvBoxid');
            //$filter->between('UploadDate', '上传时间')->datetime();
        });
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableEdit();
            if (!Admin::user()->can('歌曲点播查询删除')) {
                $actions->disableDelete();
            }
        });
        $where = [];
        if(!empty(request('placename'))){
            $placename =request('placename');
            $where[] = ['placename','like','%'.$placename.'%'];
        }
        $grid->model()->whereHas('place', function ($query) use($where){
            $query->where($where);
        });

        $settopbox = [];
        if(!empty(request('roomno'))){
            $roomno =request('roomno');
            $settopbox[] = ['roomno','like','%'.$roomno.'%'];
        }
        if(!empty(request('roomno'))){
            $grid->model()->whereHas('settopbox', function ($query) use($settopbox){
                $query->where($settopbox);
            });
        }


       // $grid->column('id', __('Id'));
        $grid->column('place.placename', __('场所名称'));
        $grid->column('settopbox.roomno', __('房号'));
        $grid->column('srvkey', __('srvkey'));
        $grid->column('KtvBoxid', __('机器码'));
        $grid->column('opendate', __('开房时间'));
        $grid->column('closedate', __('关房时间'));
        $grid->column('feesmode', __('收费模式'))->display(function ($feesmode){
            return [0=>'非扫码收费',1=>'扫码收费'][$feesmode];
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
        $show = new Show(UsersOpenclose::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('srvkey', __('Srvkey'));
        $show->field('KtvBoxid', __('KtvBoxid'));
        $show->field('opendate', __('Opendate'));
        $show->field('closedate', __('Closedate'));
        $show->field('feesmode', __('Feesmode'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new UsersOpenclose);

        $form->text('srvkey', __('srvkey'));
        $form->text('KtvBoxid', __('机器码'));
        $form->datetime('opendate', __('开房时间'))->default(date('Y-m-d H:i:s'));
        $form->datetime('closedate', __('关房时间'))->default(date('Y-m-d H:i:s'));
        $form->select('feesmode', __('收费模式'))->options([0=>'非扫码收费',1=>'扫码收费']);

        return $form;
    }
}
