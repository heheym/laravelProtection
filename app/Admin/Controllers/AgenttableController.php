<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Agenttable;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

use Encore\Admin\Layout\Content;

use App\Admin\Models\Place;

use Encore\Admin\Facades\Admin;
use Illuminate\Support\Facades\DB;

class AgenttableController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '代理商';

    public function index(Content $content)
    {
        return $content
            ->header('代理商')
           // ->description('description')
            ->body($this->agenttable())
            ->body($this->place());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function agenttable()
    {
        Admin::style(
            '
    table{table-layout:fixed ;}
    table thead th,td{
    max-width: 10px;
    min-width: 10px;
    overflow:hidden;
    white-space:nowrap;
    text-overflow:ellipsis;}'
        );

        Admin::script(
<<<js
    $(function () {
        $(".column-agentid").parents('tbody').css('cursor','pointer');

        var url = new URL(location);
        var place_agentid = url.searchParams.get('place_agentid');
        if(place_agentid!==null && place_agentid.length>0){
            $(".column-agentid:contains('"+place_agentid+"')").parent("tr").css('background','rgb(255, 255, 213)');
        }
    })

    var tabSwitch = function (element, fn) {
        $(element).dblclick(function () {
            var index = $.trim($(this).find('.column-agentid').html());
            $(this).addClass("active").siblings().removeClass("active");
            if (typeof fn === "function") {
                fn(index);
            }
        });
    };

    var elem = $(".column-agentid").parent("tr");

    tabSwitch(elem,function (index) {
        var url = new URL(location);

        url.searchParams.set('place_agentid',index);

        $.pjax({container:'#pjax-container', url: url.toString()});
        //分页数据
    });
js
        );

        $grid = new Grid(new Agenttable());
        // $grid->disableActions();
        $grid->disableCreateButton();
        $grid->disableBatchActions();
        $grid->actions(function($actions){
            $actions->disableDelete();
            $actions->disableView();
        });
        $grid->setName('agenttable');
        $grid->setView('agenttable.index');
        $grid->filter(function($filter){
            $filter->like('agentName');
        });

        $grid->column('agentid','代理商id');
        $grid->column('agentNo','代理商编号');
        $grid->column('agentName','代理商名称');
        $grid->column('country','国家');
        $grid->column('province.name','省');
        $grid->column('city.name','市');
        $grid->column('agentLevel','代理商级别')->display(function($agentLevel){
            return [1=>'一级',2=>'二级'][$agentLevel];
        });
        $grid->column('parentAgentid','上一级代理商');
        $grid->column('isappChanne','是否启用专属app渠道号')->display(function($isappChanne){
            return [0=>'否',1=>'是'][$isappChanne];
        });
        $grid->column('ChannelNo','专属app渠道号');
        $grid->column('WechatPublicHttp','专属微信公众号');
        $grid->column('agentboxVer','专属app渠道号机顶盒升级版本号');
        $grid->column('agentboxFile','专属app渠道号机顶盒升级版本最新文件名');

        return $grid;
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function place()
    {
        $grid = new Grid(new Place);
        // $grid->setView('place.index');
        $grid->setName('place');

        $grid->disableColumnSelector();
        $grid->disableCreateButton();
        $grid->disableActions();
        $grid->disableBatchActions();

        $grid->filter(function ($filter) {
            $filter->equal('agentid');
        });

        // $grid->id('Id');
        $grid->userno('场所编号');
        $grid->key('key');
        $grid->placehd('场所服务器ID');
        $grid->placename('场所名称');
        // $grid->mailbox('邮箱');
        $grid->phone('手机号');
        $grid->contacts('联系人');
        $grid->tel('联系电话');
        $grid->placeaddress('地址');
        $grid->roomtotal('机顶盒数量');
        $grid->expiredata('场所有效时间');
        $grid->country('国家');
        // dd($grid->model()->province1);
        $grid->column('province1.name','省');
        $grid->column('city1.name','市');

        $grid->status('状态')->display(function ($status) {
            if (!is_null($status)) {
                $arra = [0 => '未启用', 1 => '已启用'];
                return $arra[$status];
            }
        });

        $grid->actions(function ($actions) {
            $actions->disableView();
            if (!Admin::user()->can('场所删除')) {
                $actions->disableDelete();
            }
            if (!Admin::user()->can('场所修改')) {
                $actions->disableEdit();
            }
        });
        if (!Admin::user()->can('场所添加')) {
            $grid->disableCreateButton();  //场所添加的权限
        }

        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });
        return $grid;
    }


    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Agenttable);

        $form->text('agentNo','代理商编号')->disable();
        $form->text('agentName','代理商名称')->disable();
        $form->text('country','国家')->disable();
        $form->text('province.name','省')->disable();
        $form->text('city.name','市')->disable();
        $form->select('agentLevel','代理商级别')->options([1=>'一级',2=>'二级'])->readOnly();
        $form->text('parentAgentid','上一级代理商')->disable();
        $form->text('isappChanne','是否启用专属app渠道号')->disable();
        $form->text('ChannelNo','专属app渠道号')->disable();
        $form->text('WechatPublicHttp','专属微信公众号');
        $form->text('agentboxVer','专属app渠道号机顶盒升级版本号');
        $form->text('agentboxFile','专属app渠道号机顶盒升级版本最新文件名');

        return $form;
    }

    /*
     *
     *
     *
     *
     *
     *
     *
     */
}
