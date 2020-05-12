<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Song\BatchSongOnline;
use App\Admin\Models\Song;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use App\Admin\Extensions\Tools\SongOnline;
use Illuminate\Http\Request;
use Encore\Admin\Facades\Admin;


class SongController extends Controller
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
        $grid = new Grid(new Song);
        $grid->disableFilter(false);

        $grid->filter(function($filter){

            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            $filter->column(1/2,function($filter){
                $filter->like('Singer', '歌星名称');
                $filter->like('Songname', '歌名');
                $filter->equal('LangType', '语种')->select([0=>'国语',1=>'粤语',2=>'英语',3=>'台语', 4=>'日语',5=>'韩语',6=>'其他']);
                $filter->like('Album', '专辑');
            });

            $filter->column(1/2,function($filter){
                $filter->equal('VersionType','视频版本')->select([1=>'MTV',2=>'演唱会',3=>'影视剧情',
                    4=>'人物',5=>'风景',6=>'动画',7=>'其他']);
                $filter->like('RecordCompany', '唱片公司');
                $filter->like('Obligee', '权利人');
                $filter->equal('OnlineStatus','上架状态')->select([0=>'下架',1=>'上架']);
            });
        });

        $grid->Singer('歌星名称');
        $grid->Songname('歌曲名称');
//        $grid->SongAlias('歌曲别名');
        $grid->AreaType('发行地区')->display(function ($AreaType) {
            if(!is_null($AreaType)){
                $arra = [1=>'大陆',2=>'香港',3=>'台湾',4=>'欧美',5=>'日本',6=>'韩国',7=>'其它'];
                return $arra[$AreaType];
            }
        });

//        $grid->SoundType('声音类型')->editable('select', [1=>'男',2=>'女',3=>'合唱',4=>'组合',5=>'群星']);
        $grid->LangType('语种')->display(function ($LangType) {
            if(!is_null($LangType)){
                $arra = [0=>'国语',1=>'粤语',2=>'英语',3=>'台语', 4=>'日语',5=>'韩语',6=>'其他'];
                return $arra[$LangType];
            }
        });
//        $grid->SongType('歌曲类别')->editable('select', [1=>'流行歌曲',2=>'男女对唱',3=>'军旅红歌',
//        4=>'戏曲',5=>'儿童歌曲',6=>'舞曲',7=>'节日祝福',8=>'迪士高',9=>'民歌']);
        $grid->VersionType('视频版本')->display(function ($VersionType) {
            if(!is_null($VersionType)){
                $arra = [1=>'MTV',2=>'演唱会',3=>'影视剧情', 4=>'人物',5=>'风景',6=>'动画',7=>'其他'];
                return $arra[$VersionType];
            }
        });
//        $grid->VideoType('视频类型')->editable('select', [1=>'DVD 4：3 单',2=>'DVD 4：3',5=>'DVD 16：9',7=>'HD高清']);
        $grid->ClickRanking('点击率或排行');
//        $grid->Pinyin('歌曲简拼');
//        $grid->PinyinAll('歌曲全拼');
//        $grid->Strokes('笔画');
//        $grid->WordCount('字数');
        $grid->InputDate('入库日期');
        $grid->IssueDate('发行日期');
        $grid->Album('所属专辑名');
        $grid->VarietyArea('综艺专区');
//        $grid->VarietyArea1('综艺专区一');
//        $grid->VarietyArea2('综艺专区二');
//        $grid->MusicType('音乐类型')->editable('select', [1=>'普通歌曲',2=>'新歌推荐',5=>'网络歌曲']);
//        $grid->Autotype('授权类型')->editable('select', [0=>'授权歌曲',1=>'音集协授权',2=>'非授权歌曲']);
//        $grid->SongZhuYin('歌曲注音');
//        $grid->FirstWordStrokeNumber('歌曲的首字笔画数');
        $grid->RecordCompany('唱片公司');
//        $grid->TrackType('音轨类型')->editable('select', [1=>'左伴右唱',2=>'左唱右伴',3=>'一唱二伴',4=>'一伴二唱']);
//        $grid->ProductionType('制作类型')->editable('select', [1=>'大陆',2=>'台湾',3=>'香港',4=>'外语',5=>'定制',6=>'网络',
//            7=>'综艺',8=>'精品']);
//        $grid->Size('歌曲大小');

//        $Isbver= [
//            'on'  => ['value' => 1, 'text' => '是', 'color' => 'primary'],
//            'off' => ['value' => 0, 'text' => '否', 'color' => 'primary'],
//        ];
//        $grid->Isbver('是否是B版歌曲')->switch($Isbver);

//        $grid->SongWriter('作词');
//        $grid->Composer('作曲');
//        $grid->OriginalSinger('原唱');


        $grid->IsAverSong('是否是A版歌曲')->display(function ($IsAverSong) {
            if(!is_null($IsAverSong)){
                $arra = [0=>'否',1=>'是'];
                return $arra[$IsAverSong];
            }
        });

//        $IsExistsB= [
//            'on'  => ['value' => 1, 'text' => '存在', 'color' => 'primary'],
//            'off' => ['value' => 0, 'text' => '不存在', 'color' => 'primary'],
//        ];
//        $grid->IsExistsB('是否存在B版歌曲')->switch($IsExistsB);

//        $grid->BverFilename('A版歌曲对应的B版歌曲文件名');
//        $grid->BverVersionType('视频版本')->editable('select', [1=>'MTV',2=>'演唱会',3=>'影视剧情',
//            4=>'人物',5=>'风景',6=>'动画',7=>'其他']);
        $grid->Filename('歌曲文件名');
//        $grid->Nmelfile('评份文件名');
//        $grid->FrontCover('封面图片');

//        $IsDouYin= [
//            'on'  => ['value' => 1, 'text' => '是', 'color' => 'primary'],
//            'off' => ['value' => 0, 'text' => '否', 'color' => 'primary'],
//        ];
//        $grid->IsDouYin('是否抖音热播歌曲')->switch($IsDouYin);

//        $IsHipHop= [
//            'on'  => ['value' => 1, 'text' => '是', 'color' => 'primary'],
//            'off' => ['value' => 0, 'text' => '否', 'color' => 'primary'],
//        ];
//        $grid->IsHipHop('是否嘻哈歌曲')->switch($IsHipHop);

        $grid->Obligee('权利人');

        $grid->OnlineStatus('上架状态')->display(function ($OnlineStatus) {
            if(!is_null($OnlineStatus)){
                $arra = [0=>'否',1=>'是'];
                return $arra[$OnlineStatus];
            }
        });

//        $grid->UpdateDate('最后更新时间')->editable('datetime');

        $grid->actions(function ($actions) {
            $actions->disableView();
            if (!Admin::user()->can('歌曲删除')) {
                $actions->disableDelete();
            }
        });

        if (!Admin::user()->can('歌曲添加')) {
            $grid->disableCreateButton();  //场所添加的权限
        }

        $grid->tools(function (Grid\Tools $tools) {
            $tools->append(new BatchSongOnline());
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
        $show = new Show(Song::findOrFail($id));

        $show->musicdbpk('musicdbpk');
        $show->Singer('歌星名称');
        $show->Songname('歌曲名称');
        $show->SongAlias('歌曲别名');
        $show->AreaType('发行地区')->as(function ($AreaType) {
            return [1=>'大陆',2=>'香港',3=>'台湾',4=>'欧美',5=>'日本',6=>'韩国',7=>'其它'][$AreaType];
        });
        $show->SoundType('声音类型')->as(function ($SoundType) {
//            return "<{$title}>";
        });
        $show->LangType('语种');
        $show->SongType('SongType');
        $show->VersionType('VersionType');
        $show->VideoType('VideoType');
        $show->ClickRanking('ClickRanking');
        $show->Pinyin('Pinyin');
        $show->PinyinAll('PinyinAll');
        $show->Strokes('Strokes');
        $show->WordCount('WordCount');
        $show->InputDate('InputDate');
        $show->IssueDate('IssueDate');
        $show->Album('Album');
        $show->VarietyArea('VarietyArea');
        $show->VarietyArea1('VarietyArea1');
        $show->VarietyArea2('VarietyArea2');
        $show->MusicType('MusicType');
        $show->Autotype('Autotype');
        $show->SongZhuYin('SongZhuYin');
        $show->FirstWordStrokeNumber('FirstWordStrokeNumber');
        $show->RecordCompany('RecordCompany');
        $show->TrackType('TrackType');
        $show->ProductionType('ProductionType');
        $show->Size('Size');
        $show->Isbver('Isbver');
        $show->SongWriter('SongWriter');
        $show->Composer('Composer');
        $show->OriginalSinger('OriginalSinger');
        $show->IsAverSong('IsAverSong');
        $show->IsExistsB('IsExistsB');
        $show->BverFilename('BverFilename');
        $show->BverVersionType('BverVersionType');
        $show->Filename('Filename');
        $show->Nmelfile('Nmelfile');
        $show->FrontCover('FrontCover');
        $show->IsDouYin('IsDouYin');
        $show->IsHipHop('IsHipHop');
        $show->Obligee('Obligee');
        $show->OnlineStatus('OnlineStatus');
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
        $form = new Form(new Song);

        $form->text('musicdbpk', 'Musicdbpk')->readonly();
        $form->text('Singer', '歌星名称')->required();
        $form->text('Songname', '歌曲名称')->required();
        $form->text('SongAlias', '歌曲别名');
        $form->select('AreaType', '发行地区')->options([1=>'大陆',2=>'香港',3=>'台湾',4=>'欧美',5=>'日本',6=>'韩国',7=>'其它']);
        $form->select('SoundType', '声音类型')->options([1=>'男',2=>'女',3=>'合唱',4=>'组合',5=>'群星']);
        $form->select('LangType', '语种')->options([0=>'国语',1=>'粤语',2=>'英语',3=>'台语',
            4=>'日语',5=>'韩语',6=>'其他']);
        $form->select('SongType', '歌曲类别')->options([1=>'流行歌曲',2=>'男女对唱',3=>'军旅红歌',
            4=>'戏曲',5=>'儿童歌曲',6=>'舞曲',7=>'节日祝福',8=>'迪士高',9=>'民歌']);
        $form->select('VersionType', '视频版本')->options([1=>'MTV',2=>'演唱会',3=>'影视剧情',
            4=>'人物',5=>'风景',6=>'动画',7=>'其他']);
        $form->select('VideoType', '视频类型')->options([1=>'DVD 4：3 单',2=>'DVD 4：3',5=>'DVD 16：9',7=>'HD高清']);
        $form->number('ClickRanking', '点击率或排行');
        $form->text('Pinyin', '歌曲简拼');
        $form->text('PinyinAll', '歌曲全拼');
        $form->text('Strokes', '笔画');
        $form->number('WordCount', '字数');
        $form->date('InputDate', '入库日期')->default(date('Y-m-d H:i:s'));
        $form->date('IssueDate', '发行日期')->default(date('Y-m-d H:i:s'));
        $form->text('Album', '所属专辑名');
        $form->text('VarietyArea', '综艺专区');
        $form->text('VarietyArea1', '综艺专区一');
        $form->text('VarietyArea2', '综艺专区二');
        $form->select('MusicType', '音乐类型')->options([1=>'普通歌曲',2=>'新歌推荐',5=>'网络歌曲']);
        $form->select('Autotype', '授权类型')->options([0=>'授权歌曲',1=>'音集协授权',2=>'非授权歌曲']);
        $form->text('SongZhuYin', '歌曲注音');
        $form->text('FirstWordStrokeNumber', '歌曲的首字笔画数');
        $form->text('RecordCompany', '唱片公司');
        $form->select('TrackType', '音轨类型')->options([1=>'左伴右唱',2=>'左唱右伴',3=>'一唱二伴',4=>'一伴二唱']);
        $form->select('ProductionType', '制作类型')->options([1=>'大陆',2=>'台湾',3=>'香港',4=>'外语',5=>'定制',6=>'网络',
            7=>'综艺',8=>'精品']);
        $form->decimal('Size', '歌曲大小');
        $form->select('Isbver', '是否是B版歌曲')->options([0=>'否',1=>'是']);
        $form->text('SongWriter', '作词');
        $form->text('Composer', '作曲');
        $form->text('OriginalSinger', '原唱');
        $form->select('IsAverSong', '是否是A版')->options([0=>'否',1=>'是']);
        $form->select('IsExistsB', '是否存在B版歌曲')->options([0=>'不存在',1=>'存在']);
        $form->text('BverFilename', 'A版歌曲对应的B版歌曲文件名');
        $form->select('BverVersionType', '视频版本')->options([1=>'MTV',2=>'演唱会',3=>'影视剧情',
            4=>'人物',5=>'风景',6=>'动画',7=>'其他']);
        $form->text('Filename', '歌曲文件名');
        $form->text('Nmelfile', '评份文件名');
        $form->text('FrontCover', '封面图片');
        $form->select('IsDouYin', '是否抖音热播歌曲')->options([0=>'否',1=>'是']);
        $form->select('IsHipHop', '是否嘻哈歌曲')->options([0=>'否',1=>'是']);
        $form->text('Obligee', '权利人');
        $form->select('OnlineStatus', '上架状态')->options([0=>'下架',1=>'上架']);
        $form->select('IsClassic', '是否经典歌曲')->options([0=>'否',1=>'是']);
        $form->select('IsMustPoint', '是否KTV必点歌曲')->options([0=>'否',1=>'是']);
        $form->text('HalfYearClick', '半年内的点击率');
        $form->datetime('UpdateDate', '最后更新时间')->default(date('Y-m-d H:i:s'));


        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
        });

        $form->saving(function (Form $form) {
            $form->UpdateDate = date('Y-m-d H:i:s');
        });

        return $form;
    }



    public function songonline(Request $request)
    {
        foreach (Song::find($request->get('ids')) as $post) {
            $post->OnlineStatus = $request->get('action');
            $post->save();
        }
    }
}
