<?php

namespace App\Admin\Controllers;

use App\Admin\Models\BuSong;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Facades\Admin;

class BuSongController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Admin\Models\BuSong';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new BuSong);

        $grid->setView('busong.index');

        $where = [];
        if(!empty(request('placename'))){
            $placename =request('placename');
            $where[] = ['placename','like','%'.$placename.'%'];
        }
        $grid->model()->whereHas('place', function ($query) use($where){
            $query->where($where);
        });

        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->like('svrkey','svrkey');
            $filter->like('songname','songname');
            $filter->like('singer','singer');
            $filter->between('createdate', '创建日期')->datetime();
        });

//        $grid->column('serialid', __('Serialid'));
        $grid->column('svrkey', __('svrkey'));
        $grid->column('place.placename', __('场所名称'));
        $grid->column('songname', __('歌名'));
        $grid->column('singer', __('歌星'));
        $grid->column('langtype', __('语种'))->display(function ($langtype){
            return [0=>'国语',1=>'粤语',2=>'英语',3=>'台语',4=>'日语',5=>'韩语',6=>'不详'][$langtype];
        });
        $grid->column('remarks', __('备注'));
        $grid->column('createdate', __('创建日期'));
        $grid->column('ischeck', __('是否检查'))->display(function ($ischeck){
            return [0=>'否',1=>'是'][$ischeck];
        });
        $grid->column('buState', __('状态'))->display(function($buState){
    return [0=>'新增',1=>'处理中',2=>'完成',3=>'歌曲信息出错',4=>'取消无法处理',5=>'已上传',6=>'彻底删除'][$buState];
        });
        $grid->column('musicdbpk', __('musicdbpk'));
        $grid->column('optionRemarks', __('操作日志'));

        $grid->actions(function ($actions) {
            $actions->disableView();
            if (!Admin::user()->can('补歌管理删除')) {
                $actions->disableDelete();
            }
            if (!Admin::user()->can('补歌管理修改')) {
                $actions->disableEdit();
            }
        });
        if (!Admin::user()->can('补歌管理添加')) {
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
        $show = new Show(BuSong::findOrFail($id));

        $show->field('serialid', __('Serialid'));
        $show->field('svrkey', __('Svrkey'));
        $show->field('songname', __('Songname'));
        $show->field('singer', __('Singer'));
        $show->field('langtype', __('Langtype'));
        $show->field('remarks', __('Remarks'));
        $show->field('createdate', __('Createdate'));
        $show->field('ischeck', __('Ischeck'));
        $show->field('buState', __('BuState'));
        $show->field('musicdbpk', __('Musicdbpk'));
        $show->field('optionRemarks', __('OptionRemarks'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new BuSong);

//        $form->number('serialid', __('Serialid'));
        $form->text('svrkey', __('svrkey'));
        $form->text('songname', __('歌名'));
        $form->text('singer', __('歌星'));
        $form->select('langtype', __('语种'))->options([0=>'国语',1=>'粤语',2=>'英语',3=>'台语',4=>'日语',5=>'韩语',6=>'不详']);
        $form->text('remarks', __('备注'));
        $form->datetime('createdate', __('创建时间'))->default(date('Y-m-d H:i:s'));
        $form->select('ischeck', __('是否检查'))->options([0=>'否',1=>'是']);
        $form->select('buState', __('状态'))->options([0=>'新增',1=>'处理中',2=>'完成',3=>'歌曲信息出错',4=>'取消无法处理',5=>'已上传',6=>'彻底删除']);
        $form->text('musicdbpk', __('musicdbpk'));
        $form->text('optionRemarks', __('操作日志'));

        return $form;
    }
}
