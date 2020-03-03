<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Singer;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;


class SingerController extends Controller
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
        $grid = new Grid(new Singer);

        $grid->filter(function($filter){
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            // 在这里添加字段过滤器
            $filter->like('Singername', '歌星');
        });

        $grid->id('Id');
        $grid->Singername('歌星名称');
        $grid->NickName('歌星别名');
        $grid->AreaType('歌星地区')->display(function ($AreaType) {
            if(!is_null($AreaType)){
                $arra = [1=>'大陆',2=>'香港',3=>'台湾',4=>'欧美',5=>'日本',6=>'韩国',7=>'其它'];
                return $arra[$AreaType];
            }
        });


        $grid->Isband('是否组合')->display(function ($Isband) {
            if(!is_null($Isband)){
                $arra = [0=>'否',1=>'是'];
                return $arra[$Isband];
            }
        });


        $grid->Sex('歌星性别')->display(function ($Sex) {
            if(!is_null($Sex)){
                $arra = [1=>'男',2=>'女',3=>'合唱'];
                return $arra[$Sex];
            }
        });
        $grid->Pinyin('歌星简拼');
        $grid->PinyinAll('歌星全拼');
        $grid->Strokes('笔画');
        $grid->Wordcount('字数');
        $grid->SingerZhuYin('歌星注音');
        $grid->PicFilename('歌星图片');
        $grid->ClickRanking('排行');
        $grid->FistWordCount('歌手的首字笔画数');
        $grid->Company('唱片公司');
        $grid->Country('国籍');
        $grid->NativePlace('籍贯');
        $grid->JoinDay('入行日期');
        $grid->BirthDay('出生日期');
        $grid->UpdateDate('最后更新时间');

        $grid->actions(function ($actions) {
            $actions->disableView();
        });
        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();

            });
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
        $show = new Show(Singer::findOrFail($id));

        $show->id('Id');
        $show->Singername('Singername');
        $show->NickName('NickName');
        $show->AreaType('AreaType');
        $show->Isband('Isband');
        $show->Sex('Sex');
        $show->Pinyin('Pinyin');
        $show->PinyinAll('PinyinAll');
        $show->Strokes('Strokes');
        $show->Wordcount('Wordcount');
        $show->SingerZhuYin('SingerZhuYin');
        $show->PicFilename('PicFilename');
        $show->ClickRanking('ClickRanking');
        $show->FistWordCount('FistWordCount');
        $show->Company('Company');
        $show->Country('Country');
        $show->NativePlace('NativePlace');
        $show->JoinDay('JoinDay');
        $show->BirthDay('BirthDay');
        $show->UpdateDate('UpdateDate');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Singer);

        $form->text('Singername', '歌星名称');
        $form->text('NickName', '歌星别名');
        $form->select('AreaType', '歌星地区')->options([1=>'大陆',2=>'香港',3=>'台湾',4=>'欧美',5=>'日本',6=>'韩国',7=>'其它']);
        $form->select('Isband', '是否组合')->options([0=>'否',1=>'是']);;
        $form->select('Sex', '歌星性别')->options([1=>'男',2=>'女',3=>'合唱']);
        $form->text('Pinyin', '歌星简拼');
        $form->text('PinyinAll', '歌星全拼');
        $form->text('Strokes', '笔画');
        $form->number('Wordcount', '字数');
        $form->text('SingerZhuYin', '歌星注音');
        $form->text('PicFilename', '歌星图片');
        $form->number('ClickRanking', '排行');
        $form->number('FistWordCount', '歌手的首字笔画数');
        $form->text('Company', '唱片公司');
        $form->text('Country', '国籍');
        $form->text('NativePlace', '籍贯');
        $form->text('JoinDay', '入行日期');
        $form->text('BirthDay', '出生日期');
        $form->datetime('UpdateDate', '最后更新时间')->default(date('Y-m-d H:i:s'));

        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
        });

        $form->saving(function (Form $form) {
            $form->UpdateDate = date('Y-m-d H:i:s');
        });


        return $form;
    }
}
