<?php

/**
 * Laravel-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Encore\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Encore\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */

Encore\Admin\Form::forget(['map', 'editor']);


use Encore\Admin\Grid;
use Encore\Admin\Form;
use App\Admin\Extensions\MultipleColumn;
use Encore\Admin\Facades\Admin;



Admin::js('js/hide.js');



Grid::init(function (Grid $grid) {
    $grid->disableFilter();
    $grid->actions(function (Grid\Displayers\Actions $actions) {
        $actions->disableView();
    });
    $grid->tools(function ($tools) {
        $tools->batch(function ($batch) {
            $batch->disableDelete();
        });

    });
});

Form::init(function (Form $form) {
    $form->tools(function (Form\Tools $tools) {
        $tools->disableView();

        $tools->append('<a class="btn btn-sm btn-default form-history-bac" style="float: right;margin-right: 20px;" href="javascript:history.go(-1);" ><i class="fa fa-arrow-left"></i>&nbsp;返回</a>');
    });

    $form->footer(function ($footer) {

        // 去掉`重置`按钮
//        $footer->disableReset();

        // 去掉`提交`按钮
//        $footer->disableSubmit();

        // 去掉`查看`checkbox
        $footer->disableViewCheck();

        // 去掉`继续编辑`checkbox
        $footer->disableEditingCheck();

        // 去掉`继续创建`checkbox
        $footer->disableCreatingCheck();

    });
});

Encore\Admin\Form::extend('scriptinjecter', Field\Interaction\ScriptInjecter::class);