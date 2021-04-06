<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\RechargeMoney\CreateRechargeMoney;
use App\Admin\Models\RechargeList;
use App\Admin\Models\UrgentPaymentno;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Admin\Models\Place;
use Illuminate\Support\Facades\DB;


use App\Admin\Models\RechargeMoney;

class RechargeListController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '预付款扣费';

    public function index(Content $content)
    {
        return $content
            ->header('预付款扣费')
           // ->description('description')
            ->body($this->place())
            ->body($this->rechargelist());

    }

    protected function place()
    {

        Admin::script('rechargelist();');
        $grid = new Grid(new Place);
        $grid->setView('rechargelist.place');
        $grid->paginate(5);
        $grid->setName('place');
        $grid->disableBatchActions();
        $grid->disableCreateButton();
        $grid->disableActions();
        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableEdit();
            $actions->disableDelete();
        });


        $grid->filter(function($filter){

            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->column(1/3, function ($filter) {
                $filter->like('placename', '场所名称');
            });

            $filter->column(1/3, function ($filter) {
                $filter->like('contacts', '联系人');
            });

            $filter->column(1/3, function ($filter) {
                $filter->like('phone', '手机号');
            });
        });

        $grid->userno('场所编号');
        $grid->key('key');
        $grid->placehd('场所服务器ID');
        $grid->placename('场所名称');
        $grid->phone('手机号');
        $grid->contacts('联系人');
        $grid->tel('联系电话');
        $grid->placeaddress('地址');
        $grid->roomtotal('机顶盒数量');
        $grid->balanceSum('预付款余额');
        $grid->expiredata('场所有效时间');
        $grid->country('国家');
        $grid->province('省')->display(function ($province) {
            if(!is_null($province)){
                return DB::table('china_area')->where('code',$province)->value('name');
            }
        });
        $grid->city('市')->display(function ($city) {
            if(!is_null($city)){
                return DB::table('china_area')->where('code',$city)->value('name');
            }
        });

        $grid->status('状态')->display(function ($status) {
            if(!is_null($status)){
                $arra = [0=>'未启用',1=>'已启用'];
                return $arra[$status];
            }
        });
        // $grid->wangMode('预警模式')->display(function ($wangMode) {
        //     if(!is_null($wangMode)){
        //         return DB::table('warningmode')->where('id',$wangMode)->value('warningName');
        //     }
        // });

        return $grid;
    }


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function rechargelist()
    {
        $grid = new Grid(new RechargeList());
        // $grid->setView('urgentpaymentno.index');
        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->like('srvkey','srvkey');
            $filter->between('createDate', '充值时间')->date();

        });
        $grid->model()->orderBy('createDate','desc');
        $createDatestart = app('request')->get('createDate_start');
        $createDateend= app('request')->get('createDate_end');
        if($createDatestart){
            $grid->model()->where([['createDate','>',$createDatestart]]);
        }
        if($createDateend){
            $grid->model()->where([['createDate','<',$createDateend.' 23:59:59']]);
        }
        $grid->disableActions();
        $grid->setView('rechargelist.rechargelist');
        $grid->setName('rechargelist');
        $grid->disableCreateButton();

        // $query = http_build_query(['rechargemoney_srvkey' => app('request')->get('rechargemoney_srvkey')]);
        // $grid->tools(function ($tools)use($grid, $query){
        //     $tools->append(new CreateRechargeMoney($grid, $query));
        // });

        $grid->column('srvkey', __('场所key'));
        $grid->column('KtvBoxid', __('机器码'));
        $grid->column('paymentmoney', __('金额'));
        $grid->column('createDate', __('时间'));
        $grid->column('Remarks', __('备注'));

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
        $show = new Show(UrgentPaymentno::findOrFail($id));

//        $show->field('id', __('Id'));
        $show->field('notype', __('账号类型'));
        $show->field('paymentno', __('账号'));
        $show->field('createdate', __('时间'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new RechargeMoney);

        $srvkey  = !empty($_GET['rechargemoney_srvkey'])?$_GET['rechargemoney_srvkey']:'';
        $placename = '';
        if($srvkey){
            $placename= DB::table('place')->where('key',$srvkey)->value('placename');
            // $place = DB::table('place')->where('key',$srvkey)->first();
        }
        // $place = DB::table('place')->where('key',$srvkey)->first();
        $form->hidden('srvkey', '场所key')->default($srvkey)->readonly()->required();
        $form->text('placename', '场所名称')->default($placename)->readonly();
        $form->text('billno', __('预充款单号'))->default('Y'.time())->readonly();
        $form->hidden('sourceType', __('来源'))->default(0);
        $form->text('amount', __('金额'))->required();
        // $form->text('balance', __('剩余金额'))->readonly();
        $form->hidden('rechargeDate', __('充值时间'))->default(date('Y-m-d H:i:s'));
        $form->text('voucherNo', __('收款凭证号'));
        $form->text('voucherFile1', __('收款凭证文件'));
        $form->text('voucherFile2', __('收款凭证文件'));
        $form->hidden('operator', __('操作人'))->default(Admin::user()->username);
        $form->text('remarks', __('备注说明'));

        $form->hidden('balance');
        $form->ignore(['placename']);

        $form->saving(function(Form $form){
            $place = DB::table('place')->where('key',$form->srvkey)->first();
            $form->balance = $form->amount+$place->balanceSum;
        });
        $form->saved(function(Form $form){
            DB::table('place')->where('key',$form->model()->srvkey)->increment('balanceSum',$form->model()->amount);
        });
        return $form;
    }


}
