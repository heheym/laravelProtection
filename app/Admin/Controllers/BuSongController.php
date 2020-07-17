<?php

namespace App\Admin\Controllers;

use App\Admin\Models\BuSong;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

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

//        $grid->column('serialid', __('Serialid'));
        $grid->column('svrkey', __('svrkey'));
        $grid->column('songname', __('歌名'));
        $grid->column('singer', __('歌星'));
        $grid->column('langtype', __('语种'))->display(function ($langtype){

        });
        $grid->column('remarks', __('Remarks'));
        $grid->column('createdate', __('Createdate'));
        $grid->column('ischeck', __('Ischeck'));
        $grid->column('buState', __('BuState'));
        $grid->column('musicdbpk', __('Musicdbpk'));
        $grid->column('optionRemarks', __('OptionRemarks'));

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

        $form->number('serialid', __('Serialid'));
        $form->text('svrkey', __('Svrkey'));
        $form->text('songname', __('Songname'));
        $form->text('singer', __('Singer'));
        $form->switch('langtype', __('Langtype'));
        $form->text('remarks', __('Remarks'));
        $form->datetime('createdate', __('Createdate'))->default(date('Y-m-d H:i:s'));
        $form->switch('ischeck', __('Ischeck'));
        $form->switch('buState', __('BuState'));
        $form->number('musicdbpk', __('Musicdbpk'));
        $form->text('optionRemarks', __('OptionRemarks'));

        return $form;
    }
}
