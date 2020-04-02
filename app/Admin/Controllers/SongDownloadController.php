<?php

namespace App\Admin\Controllers;

use App\Admin\Models\SongDownload;
use App\Http\Controllers\Controller;
use Encore\Admin\Layout\Content;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;

class SongDownloadController extends Controller
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '';


    public function index(Content $content)
    {
        return $content
            ->body($this->grid());
    }


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new SongDownload);

        $grid->disableCreateButton();
        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->disableView();
            $actions->disableEdit();
        });
        $grid->disableFilter(false);
        $grid->filter(function($filter){

            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            $filter->column(1/2,function($filter){
                $filter->like('srvkey', 'srvkey');
                $filter->where(function ($query) {
                    $query->whereHas('place', function ($query) {
                        $query->where('placename', 'like', "%{$this->input}%");
                    });
                }, '场所');
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

        $grid->column('musicdbpk', __('歌曲主键'));
        $grid->column('srvkey', __('Srvkey'));
        $grid->column('placehd', __('场所服务器ID'));
        $grid->column('KtvBoxid', __('机器码'));
        // 添加不存在的字段
        $grid->placename('场所名')->display(function () {
            return DB::table('place')->where('key',$this->srvkey )->value('placename');
        });
        $grid->contact('联系人')->display(function () {
            return DB::table('place')->where('key',$this->srvkey )->value('contacts');
        });
        $grid->address('省市')->display(function () {
            $province = DB::table('place')->where('key',$this->srvkey )->value('province');
            $city = DB::table('place')->where('key',$this->srvkey )->value('city');
            return $province.$city;
        });
        $grid->column('created_date', __('时间'));

        $grid->footer(function ($query) {

            // 查询出已支付状态的订单总金额
            $data = $query->count('id');

//            return '<a class="page-link" href="http://laravelprotection.test/admin/songdownload?_pjax=%23pjax-container&amp;per_page=10&amp;page=3">3</a>';
            return "<h4 style='text-align:center'>总下载 ： $data</h4>";
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
        $show = new Show(SongDownload::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('musicdbpk', __('Musicdbpk'));
        $show->field('srvkey', __('Srvkey'));
        $show->field('placehd', __('Placehd'));
        $show->field('KtvBoxid', __('KtvBoxid'));
        $show->field('created_date', __('Created date'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new SongDownload);

        $form->number('musicdbpk', __('Musicdbpk'));
        $form->text('srvkey', __('Srvkey'));
        $form->text('placehd', __('Placehd'));
        $form->text('KtvBoxid', __('KtvBoxid'));
        $form->datetime('created_date', __('Created date'))->default(date('Y-m-d H:i:s'));

        return $form;
    }
}
