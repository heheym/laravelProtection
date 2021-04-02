<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\OrderCount\Export;
use App\Admin\Models\OrderCount;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;

use App\Admin\Extensions\OrderCount\PostsExporter;
use Qiniu\Http\Request;


class OrderCountController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '交易流水查询统计';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new OrderCount);
        $grid->disableCreateButton();
        $grid->disableColumnSelector();
        $grid->disableExport();
        $grid->disableActions();
        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableEdit();
            $actions->disableDelete();
        });

        $grid->setView('ordercount.index');
        $grid->model()->orderBy('pay_time', 'desc')->where('order_status',1);
        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->between('pay_time', '支付时间')->datetime();
        });

        $html = "";
        $where = [];
        if(!empty(request('placename'))){
            $placename =request('placename');
            $where[] = ['placename','like','%'.$placename.'%'];
            $html .="<span style='text-align:center;color:red;font-size:20px;margin-left:50px'>场所名称 ：".$placename."</span>" ;
        }
        $setopboxWhere = [];
        if(!empty(request('roomno'))){
            $roomno =request('roomno');
            $setopboxWhere[] = ['roomno','like','%'.$roomno.'%'];
            $grid->model()->whereHas('settopbox', function ($query) use($setopboxWhere){
                $query->where($setopboxWhere);
            });
            $html .="<span style='text-align:center;color:red;font-size:20px;margin-left:50px'>房号 ：".$roomno."</span>" ;
        }

        if(!empty(request('province'))){
            $province =request('province');
            $where[] = ['province','like','%'.$province.'%'];
            $province  = DB::table('china_area')->where('code',$province)->value('name');
            $html .="<span style='text-align:center;color:red;font-size:20px;margin-left:50px'>地址 ：".$province."</span>" ;
        }
        if(!empty(request('city'))){
            $city =request('city');
            $where[] = ['city','like','%'.$city.'%'];
            $city  = DB::table('china_area')->where('code',$city)->value('name');
            $html .= "<span style='text-align:center;color:red;font-size:20px;'>".$city."</span>";
        }
        if(!empty($where)){
            $grid->model()->whereHas('place', function ($query) use($where){
                $query->where($where);
            });
        }

        if(!empty(request('pay_time')['start'])){
            $payTimeStart = request('pay_time')['start'];
            $html .= "<span style='text-align:center;color:red;font-size:20px;margin-left:50px'>支付时间 ：".$payTimeStart."</span>";
        }
        if(!empty(request('pay_time')['end'])){
            $payTimeEnd = request('pay_time')['end'];
            $html .= "<span style='text-align:center;color:red;font-size:20px;'> 至 ".$payTimeEnd."</span>";
        }
        $grid->column('place.placename', __('场所名称'));
        $grid->column('settopbox.roomno', __('房号'));
        $grid->address('省市')->display(function () {
            if(isset($this->province[0]->name) && isset($this->city[0]->name)){
                return $this->province[0]->name.$this->city[0]->name;
            }
        });
//        $grid->column('KtvBoxid', __('机器码'));
//        $grid->column('order_sn', __('Order sn'));
        $grid->column('order_sn_submit', __('订单号'));
        $grid->column('order_status', __('订单状态'))->display(function($order_status){
            return [0=>'未支付',1=>'已支付'][$order_status];
        });
        $grid->column('amount', __('订单金额(元)'));
        $grid->column('submit_time', __('创建时间'));
        $grid->column('pay_time', __('支付时间'));
        $grid->column('leshua_order_id', __('乐刷订单号'));
        $grid->column('pay_way', __('支付方式'))->display(function($pay_way){
            if(!empty($pay_way)){
                return ['WXZF'=>'微信','ZFBZF'=>'支付宝'][$pay_way];
            }
        });
        $grid->column('openid', __('用户号'));
        $grid->column('note', __('备注'));
        $grid->column('send_message', __('发送'))->display(function($send_message){
            if(isset($send_message)){
                return [0=>'未发送',1=>'已发送'][$send_message];
            }
        });
        $grid->column('confirm_order', __('处理'))->display(function($confirm_order){
            if(isset($confirm_order)){
                return [0=>'未处理',1=>'已处理'][$confirm_order];
            }
        });

        $grid->footer(function ($query) use($html) {
            // 查询出已支付状态的订单总金额
            $amount = $query->sum('amount');
            $html .= "<span style='text-align:center;color:red;font-size:20px;margin-left:50px'>总金额 ：".$amount."</span>";
            return $html;
        });


        $postsExporter =  new PostsExporter();
        $postsExporter->fileName = date('Y-m-d H:i:s').'.xlsx';
        $grid->exporter($postsExporter);

//        dd(url()->current());
        $url = url()->current()."?";
        foreach(request()->all() as $k => $v){
            if(!isset($v)){
                continue;
            }
            if(is_array($v)){
                $url .= "&pay_time[start]=".$v['start']."&pay_time[end]=".$v['end'];
            }else{
                $url .= "&$k=$v";
            }
        }
        $url .= "&_export_=all";

//        if(!isset(request()->all()['pay_time']['start']) && !isset(request()->all()['pay_time']['end'])){
//            $grid->tools(function ($tools)use($grid, $url){
//                $tools->append('<div class="btn-group pull-right" style="margin-left: 30px">
//    <a href="javascirpt:void(0)" class="btn btn-sm btn-success" onclick=alert("请填写支付时间")>
//        <i class="fa fa-plus"></i>&nbsp;&nbsp;导出
//    </a>
//</div>');
//            });
//        }else{
//            $grid->tools(function ($tools)use($grid, $url){
//                $tools->append('<div class="btn-group pull-right" style="margin-left: 30px">
//    <a href="'.$url.'" class="btn btn-sm btn-success" target="_blank">
//        <i class="fa fa-plus"></i>&nbsp;&nbsp;导出
//    </a>
//</div>');
//            });
//        }

        $grid->tools(function ($tools)use($grid,$url){
            $tools->append(new Export($grid,$url));
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
        $show = new Show(Ordersn::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('key', __('Key'));
        $show->field('KtvBoxid', __('KtvBoxid'));
        $show->field('order_sn', __('Order sn'));
        $show->field('order_sn_submit', __('Order sn submit'));
        $show->field('order_status', __('Order status'));
        $show->field('amount', __('Amount'));
        $show->field('submit_time', __('Submit time'));
        $show->field('note', __('Note'));
        $show->field('o_status', __('O status'));
        $show->field('pay_time', __('Pay time'));
        $show->field('leshua_order_id', __('Leshua order id'));
        $show->field('pay_way', __('Pay way'));
        $show->field('send_message', __('Send message'));
        $show->field('confirm_order', __('Confirm order'));
        $show->field('openid', __('Openid'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new OrderCount);

        $form->text('key', __('Key'));
        $form->text('KtvBoxid', __('KtvBoxid'));
        $form->text('order_sn', __('Order sn'));
        $form->text('order_sn_submit', __('Order sn submit'));
        $form->switch('order_status', __('Order status'));
        $form->decimal('amount', __('Amount'))->default(0.00);
        $form->datetime('submit_time', __('Submit time'))->default(date('Y-m-d H:i:s'));
        $form->text('note', __('Note'));
        $form->switch('o_status', __('O status'))->default(1);
        $form->datetime('pay_time', __('Pay time'))->default(date('Y-m-d H:i:s'));
        $form->text('leshua_order_id', __('Leshua order id'));
        $form->text('pay_way', __('Pay way'));
        $form->switch('send_message', __('Send message'));
        $form->switch('confirm_order', __('Confirm order'));
        $form->text('openid', __('Openid'));

        return $form;
    }
}
