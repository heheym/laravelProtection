<?php

namespace App\Admin\Controllers;

use App\Admin\Models\AdminPermissions;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;

use App\Admin\Extensions\Adminpermission\Replicate;

class AdminPermissonsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Admin\Models\AdminPermissions';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new AdminPermissions);

        $grid->column('id', __('id'));
        $grid->column('parent_id', __('parent_id'))->display(function($parent_id){
            return DB::table('admin_permissions')->where('id',$parent_id)->value('name').'('.$parent_id.')';
        })->editable();
        $grid->column('menu_id', __('menu_id'))->display(function($menu_id){
            return DB::table('admin_menu')->where('id',$menu_id)->value('title').'('.$menu_id.')';
        })->editable();
        $grid->column('name', __('name'))->editable();
        $grid->column('slug', __('slug'))->editable();
        $grid->column('http_method', __('http_method'))->editable();
        $grid->column('http_path', __('http_path'))->editable();
//        $grid->column('created_at', __('created_at'));
//        $grid->column('updated_at', __('updated_at'));

        $grid->batchActions(function ($batch) {
            $batch->add(new Replicate());
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
        $show = new Show(AdminPermissions::findOrFail($id));

        $show->field('id', __('id'));
        $show->field('parent_id', __('parent_id'));
        $show->field('menu_id', __('menu_id'));
        $show->field('name', __('name'));
        $show->field('slug', __('slug'));
        $show->field('http_method', __('http_method'));
        $show->field('http_path', __('http_path'));
        $show->field('created_at', __('created_at'));
        $show->field('updated_at', __('updated_at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new AdminPermissions);

//        $form->number('parent_id', __('parent_id'));

        $table = DB::table('admin_permissions')->get();
        foreach($table as $k=>$v){
            $temp[$v->id] = $v->name;
        }
        $form->select('parent_id','parent_id')->options($temp);

        $menu = DB::table('admin_menu')->get();
        foreach($menu as $k=>$v){
            $menuTemp[$v->id] = $v->title;
        }
        $form->select('menu_id', __('menu_id'))->options($menuTemp);

        $form->text('name', __('name'));
        $form->text('slug', __('slug'));
        $form->text('http_method', __('http_method'));
        $form->textarea('http_path', __('http_path'));

        return $form;
    }
}
