<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Promote;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Encore\Admin\Facades\Admin;

class PromoteController extends Controller
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
           ->header('新歌推广')
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
        $grid = new Grid(new Promote);

//        $grid->id('Id');
        $grid->songnum('新歌总数');
        $grid->varietynum('综艺歌曲数量');
        $grid->netnum('网络歌曲数量');
        $grid->replacenum('替换歌曲数量');
        $grid->hotnum('热门新歌数量');
//        $grid->created_date('更新日期');

        $grid->column('created_date','更新日期')->display(function ($created_date) {
            return date('Y-m-d',strtotime($created_date));
        });

        $grid->actions(function ($actions) {
            $actions->disableView();
            if (!Admin::user()->can('新歌推广删除')) {
                $actions->disableDelete();
            }
            if (!Admin::user()->can('新歌推广修改')) {
                $actions->disableEdit();
            }
        });
        if (!Admin::user()->can('新歌推广添加')) {
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
        $show = new Show(Promote::findOrFail($id));

        $show->id('Id');
        $show->songnum('Songnum');
        $show->varietynum('Varietynum');
        $show->netnum('Netnum');
        $show->replacenum('Replacenum');
        $show->hotnum('Hotnum');
        $show->created_date('Created date');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Promote);

        $form->column(5/6, function ($form) {
            $form->number('songnum', '新歌总数');
            $form->number('varietynum', '综艺歌曲数量');
            $form->number('netnum', '网络歌曲数量');
            $form->number('replacenum', '替换歌曲数量');
            $form->number('hotnum', '热门新歌数量');
            $form->date('created_date', '更新日期')->default(date('Y-m-d'));
        });

        $form->table('song','歌曲', function ($table) {
            $table->text('singername','歌星');
            $table->text('songname','歌名');
            $table->text('lan','语种');
            $table->text('album','专辑');
        });

        $form->saving(function (Form $form) {
            // 如果不是编辑状态，则添加字段唯一验证
            if (!$id = $form->model()->id) {
                $exists = DB::table('promote')->where(['created_date'=> $form->created_date])->exists();
                $error = new MessageBag([
                    'title'   => '提示',
                    'message' => '已存在期数',
                ]);
                if($exists){
                    return back()->with(compact('error'));
                }
            }
        });
        return $form;
    }


}
