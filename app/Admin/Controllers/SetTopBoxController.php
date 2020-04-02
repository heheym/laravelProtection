<?php

namespace App\Admin\Controllers;

use App\Admin\Models\SetTopBox;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use App\Admin\Actions\SetTopBox\BatchChange;

class SetTopBoxController extends Controller
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
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new SetTopBox);

        $grid->disableFilter(false);
        $grid->filter(function($filter){

            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            $filter->column(1/2,function($filter){
                $filter->like('key', 'key');
                $filter->where(function ($query) {
                    $query->whereHas('place', function ($query) {
                        $query->where('placename', 'like', "%{$this->input}%");
                    });
                }, '场所');
                $filter->equal('KtvBoxState','状态')->select([0=>'待审核',1=>'正常',2=>'返修',3=>'过期',4=>'作废']);
            });
            $filter->column(1/2,function($filter){
                $filter->where(function ($query) {
                    $query->whereHas('place', function ($query) {
                        $query->where('province', 'like', "%{$this->input}%")
                            ->orWhere('city', 'like', "%{$this->input}%");
                    });
                }, '省市');
                $filter->where(function ($query) {
                    $query->whereHas('place', function ($query) {
                        $query->where('contacts', 'like', "%{$this->input}%");
                    });
                }, '联系人');
            });


        });

        $grid->id('Id');
        $grid->key('Key');
        $grid->KtvBoxid('机器码');
        $grid->machineCode('机顶盒MAC');
        $grid->roomno('房号');

        $grid->KtvBoxState('状态')->display(function ($KtvBoxState) {
            if(!is_null($KtvBoxState)){
                $arra = [0=>'待审核',1=>'正常',2=>'返修',3=>'过期',4=>'作废'];
                return $arra[$KtvBoxState];
            }
        });
        $grid->created_date('启用日期');

        // 添加不存在的字段
        $grid->place('场所')->display(function () {
            return DB::table('place')->where('key',$this->key )->value('placename');
        });
        $grid->contact('联系人')->display(function () {
            return DB::table('place')->where('key',$this->key )->value('contacts');
        });
        $grid->address('省市')->display(function () {
            $province = DB::table('place')->where('key',$this->key )->value('province');
            $city = DB::table('place')->where('key',$this->key )->value('city');
            return $province.$city;
        });


        $grid->mark('备注');

//        $grid->batchActions(function ($batch) {
//            $batch->add(new BatchChange(1));
//        });

        $grid->tools(function (Grid\Tools $tools) {
            $tools->append(new BatchChange());
        });
        $grid->actions(function ($actions) {
            $actions->disableView();
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
        $show = new Show(SetTopBox::findOrFail($id));

        $show->id('Id');
        $show->KtvBoxid('KtvBoxid');
        $show->machineCode('MachineCode');
        $show->roomno('Roomno');
        $show->key('Key');
        $show->status('Status');
        $show->created_date('Created date');
        $show->mark('Mark');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new SetTopBox);

        $form->text('key', 'Key')->required()->rules(function ($form) {
            return 'exists:place,key';
            // 如果不是编辑状态，则添加字段唯一验证
            if (!$id = $form->model()->id) {

            }

        });

//        $form->text('KtvBoxid', '机顶盒MAC')->required();

        $form->text('KtvBoxid', '机器码')->required()->rules(function ($form) {

            // 如果不是编辑状态，则添加字段唯一验证
            if (!$id = $form->model()->id) {
                return 'unique:settopbox,KtvBoxid';
            }

        });

        $form->text('machineCode', '机顶盒MAC');
        $form->text('roomno', '房号');
        $form->select('KtvBoxState', '状态')->options([0=>'待审核',1=>'正常',2=>'返修',3=>'过期',4=>'作废']);
        $form->datetime('created_date', '启用日期');

        $form->text('mark', '备注');
        $form->hidden('mark', '备注')->rules(function ($form) {

        // 如果不是编辑状态，则添加字段唯一验证
        if (!$id = $form->model()->id) {
            return 'unique:settopbox,KtvBoxid';
        }

    });;



        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
        });

        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = bcrypt($form->password);
            }



            $count2 = DB::table('settopbox')->where(['key'=> $form->key])
                ->where(function ($query) {
                    $query->where('KtvBoxState',0)
                        ->orWhere('KtvBoxState', 1);
                })->count();

            $count3 = $count2+1;
            $roomtotal = DB::table('place')->where(['key'=> $form->key])->value('roomtotal');
            $error = new MessageBag([
                'title'   => '提示',
                'message' => '机顶盒超过有效数量',
            ]);
            if($count3>$roomtotal){
                return back()->with(compact('error'));
            }
        });


        return $form;
    }
}
