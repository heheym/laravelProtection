<?php

namespace Encore\Admin\Controllers;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;

class RoleController extends AdminController
{
    /**
     * {@inheritdoc}
     */
    protected function title()
    {
        return trans('admin.roles');
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $roleModel = config('admin.database.roles_model');

        $grid = new Grid(new $roleModel());

//        $grid->column('id', 'ID')->sortable();
        $grid->column('slug', trans('admin.slug'));
        $grid->column('name', trans('admin.name'));

//        $grid->column('permissions', trans('admin.permission'))->pluck('name')->label();

//        $grid->column('created_at', trans('admin.created_at'));
//        $grid->column('updated_at', trans('admin.updated_at'));

        $grid->actions(function (Grid\Displayers\Actions $actions) {
            if ($actions->row->slug == 'administrator') {
                $actions->disableDelete();
            }
            $actions->disableView();
        });

        $grid->tools(function (Grid\Tools $tools) {
            $tools->batch(function (Grid\Tools\BatchActions $actions) {
                $actions->disableDelete();
            });
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        $roleModel = config('admin.database.roles_model');

        $show = new Show($roleModel::findOrFail($id));

        $show->field('id', 'ID');
        $show->field('slug', trans('admin.slug'));
        $show->field('name', trans('admin.name'));
        $show->field('permissions', trans('admin.permissions'))->as(function ($permission) {
            return $permission->pluck('name');
        })->label();
        $show->field('created_at', trans('admin.created_at'));
        $show->field('updated_at', trans('admin.updated_at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        $permissionModel = config('admin.database.permissions_model');
        $roleModel = config('admin.database.roles_model');

        $form = new Form(new $roleModel());

//        $form->display('id', 'ID');

        $form->text('slug', trans('admin.slug'))->rules('required');
        $form->text('name', trans('admin.name'))->rules('required');
//        $form->listbox('permissions', trans('admin.permissions'))->options($permissionModel::all()->pluck('name', 'id'));

        $che = '';
        $id = request()->route()->parameters('id');
        if($id){
            $permissionId = DB::table('admin_role_permissions')->where(['role_id'=>$id])->pluck('permission_id');
            $che = json_encode($permissionId);
        }


        $sql = "select id,parent_id,name from (
              select t1.id,t1.parent_id,t1.name,
              if(find_in_set(parent_id, @pids) > 0, @pids := concat(@pids, ',', id), 0) as ischild
              from (
                   select id,parent_id,name from admin_permissions t order by parent_id, id
              ) t1,
              (select @pids := 1) t2
        ) t3 where ischild != 0";
        $data = DB::select($sql);
        array_unshift($data,['id'=>1,'parent_id'=>0,'name'=>'所有权限']);
        $data = json_encode($data);

        $form->html(view('role.permission',compact(['data','che'])),'权限');
        $form->hidden('permission');
//        $form->display('created_at', trans('admin.created_at'));
//        $form->display('updated_at', trans('admin.updated_at'));

        return $form;
    }
}
