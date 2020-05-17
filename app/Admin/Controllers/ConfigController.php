<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Config;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Facades\Admin as Admin1;

class ConfigController extends Controller
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
        $grid = new Grid(new Config);
        $grid->disableCreateButton();

        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
        });
//        $grid->id('Id');
        $grid->SoftwareName('软件名称');
        $grid->SoftwareVerno('软件版本号');
        $grid->NewSongHttp('新歌信息页面');
        $grid->SpeedLimit('限速(单位K)');
//        $grid->DomainNameSpace('云空间名称');
//        $grid->SecretKey('云SecretKey');
//        $grid->AccessKey('云AccessKey');
        $grid->LoginName('登录信息');
        $grid->UpdateMode('更新方式')->display(function ($UpdateMode){
            $UpdateModeArray = [1=>'不更新',2=>'可更新',3=>'必须更新'];
            return $UpdateModeArray[$UpdateMode];
        });;
        $grid->SoftseverVer('升级版本号');
        $grid->SoftseverHttp('升级版地址');
        $grid->SoftseverMemo('升级版说明');
        $grid->SoftboxVer('机顶盒升级版本号');
        $grid->SoftboxHttp('机顶盒升级版地址');
        $grid->SoftboxMemo('机顶盒升级版说明');
        $grid->SoftsongDbVer('歌曲文件升级版本号');
        $grid->SoftsongDbHttp('歌曲文件升级版地址');
        $grid->SingerPicHttp('下载地址目录');
//        $grid->warningTime('时间');
//        $grid->warningCountRoom('数量');
//
//        $grid->warningTime1('时间1');
//        $grid->warningCountRoom1('数量1');
        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableDelete();
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
        $show = new Show(Config::findOrFail($id));
        $show->id('Id');
        $show->version('版本');
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Config);
//        Admin1::disablePjax();
//        Admin::js('js/hide.js');
        Admin::script('show();');

        $form->text('SoftwareName', '软件名称')->required();
        $form->text('SoftwareVerno', '软件版本号')->required();
        $form->text('NewSongHttp', '新歌信息页面');
        $form->text('SpeedLimit', '限速(单位K）');
        $form->text('DomainNameSpace', '歌曲云空间名称');
        $form->text('Domain', '歌曲云空间域名');
        $form->text('posterDomainSpace', '广告云空间名称');
        $form->text('posterDomain', '广告云空间域名');
        $form->password('SecretKey', '云SecretKey');
        $form->password('AccessKey', '云AccessKey');
        $form->text('LoginName', '登录信息');
        $form->select('UpdateMode', '软件更新方式')->options([1=>'不更新',2=>'可更新',3=>'必须更新']);
        $form->text('SoftseverVer', '场所服务端升级版本号');
        $form->text('SoftseverHttp', '场所服务端升级版地址');
        $form->text('SoftseverMemo', '场所服务端升级版说明');
        $form->text('SoftboxVer', '机顶盒升级版本号');
        $form->text('SoftboxHttp', '机顶盒升级版地址');
        $form->text('SoftboxMemo', '机顶盒升级版说明');
        $form->text('SoftsongDbVer', '歌曲文件song.db升级版本号（自动升级)');
        $form->text('SoftsongDbHttp', '歌曲文件song.db升级版地址（自动上传)');
        $form->text('SingerPicHttp', '歌星图片下载地址目录（自动上传）');
        $form->text('SongNmelHttp', '歌曲评分文件下载地址');
        $form->text('SongPicHttp', '歌曲图片下载地址');
        $form->text('AppPicHttp', '前端App分类图片下载地址');
        $form->text('WarningAtoBtime', 'A版转B版时间(分钟)')->default(40);
        $form->text('WechatPublicHttp', '公众号链接地址');

        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
            $tools->disableDelete();
        });

        return $form;
    }
}
