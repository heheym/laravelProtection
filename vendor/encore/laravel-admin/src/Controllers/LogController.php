<?php

namespace Encore\Admin\Controllers;

use Encore\Admin\Auth\Database\OperationLog;
use Encore\Admin\Grid;
use Illuminate\Support\Arr;

class LogController extends AdminController
{
    /**
     * {@inheritdoc}
     */
    protected function title()
    {
        return trans('admin.operation_log');
    }

    /**
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new OperationLog());

        $grid->model()->orderBy('id', 'DESC');

//        $grid->column('id', 'ID')->sortable();
        $grid->column('user.name', '用户名称');
        $grid->column('method',' 操作')->display(function ($method) {
            $color = Arr::get(OperationLog::$methodColors, $method, 'grey');
            $arr = ['GET'=>'查看','PUT'=>'修改','POST'=>'添加','DELETE'=>'删除'];
            return "<span class=\"badge bg-$color\">$arr[$method]</span>";
        });
        $grid->column('path','路径')->label('info');
        $grid->column('ip')->label('primary');
//        $grid->column('input')->display(function ($input) {
//            $input = json_decode($input, true);
//            $input = Arr::except($input, ['_pjax', '_token', '_method', '_previous_']);
//            if (empty($input)) {
//                return '<code>{}</code>';
//            }
//
//            return '<pre>'.json_encode($input, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE).'</pre>';
//        });

        $grid->column('created_at', trans('admin.created_at'));

        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->disableEdit();
            $actions->disableView();
        });

        $grid->disableCreateButton();

        $grid->filter(function (Grid\Filter $filter) {
            $userModel = config('admin.database.users_model');

            $filter->equal('user_id', 'User')->select($userModel::all()->pluck('name', 'id'));
            $filter->equal('method')->select(array_combine(OperationLog::$methods, OperationLog::$methods));
            $filter->like('path');
            $filter->equal('ip');
        });

        return $grid;
    }

    /**
     * @param mixed $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $ids = explode(',', $id);

        if (OperationLog::destroy(array_filter($ids))) {
            $data = [
                'status'  => true,
                'message' => trans('admin.delete_succeeded'),
            ];
        } else {
            $data = [
                'status'  => false,
                'message' => trans('admin.delete_failed'),
            ];
        }

        return response()->json($data);
    }
}
