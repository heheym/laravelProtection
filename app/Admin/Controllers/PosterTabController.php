<?php

namespace App\Admin\Controllers;

use App\Admin\Models\PosterTab;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Encore\Admin\Facades\Admin;  //获取当前用户

// 引入鉴权类
use Qiniu\Auth;
// 引入上传类
use Qiniu\Storage\UploadManager;

class PosterTabController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new PosterTab);

//        $grid->column('Poster_id', __('Poster id'));
        $grid->column('Poster_No', __('广告编号'));
        $grid->column('Poster_name', __('广告名称'));

        $grid->File_type('文件类型')->display(function ($File_type) {
            return [0=>'图片文件',1=>'视频文件'][$File_type];
        });
        $grid->column('Startdate', __('开始时间'));
        $grid->column('Enddate', __('结束时间'));
        $grid->column('Postertime', __('播放时间段'));
//        $grid->column('Poster_mode', __('推送方式'));
        $grid->column('Poster_filename', __('文件名称'))->display(function ($Poster_filename) {
            $parameterset = DB::table('parameterset')->first();
            $accessKey = $parameterset->AccessKey;
            $secretKey = $parameterset->SecretKey;
//            $bucket = $parameterset->DomainNameSpace;
//            $domain = $parameterset->Domain;
            $bucket = $parameterset->posterDomainSpace;
            $domain = $parameterset->posterDomain;
//// 构建Auth对象
            $auth = new Auth($accessKey, $secretKey);
            // 私有空间中的外链 http://<domain>/<file_key>
            $baseUrl = $domain."/".$Poster_filename;
            // 对链接进行签名
            $signedUrl = $auth->privateDownloadUrl($baseUrl,86400);
            return "<a href=".$signedUrl." target='_blank'>$Poster_filename</a>";
        });
        $grid->column('Poster_content', __('广告内容'));
        $grid->column('CreateDate', __('创建立时间'));
        $grid->column('Operator', __('操作人'));

        $grid->actions(function ($actions) {
            $actions->disableView();
            if (!Admin::user()->can('广告推广删除')) {
                $actions->disableDelete();
            }
            if (!Admin::user()->can('广告推广修改')) {
                $actions->disableEdit();
            }
        });
        if (!Admin::user()->can('广告推广添加')) {
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
        $show = new Show(PosterTab::findOrFail($id));

        $show->field('Poster_id', __('Poster id'));
        $show->field('Poster_No', __('Poster No'));
        $show->field('Poster_name', __('Poster name'));
        $show->field('File_type', __('File type'));
        $show->field('Startdate', __('Startdate'));
        $show->field('Enddate', __('Enddate'));
        $show->field('Postertime', __('Postertime'));
        $show->field('Poster_mode', __('Poster mode'));
        $show->field('Poster_filename', __('Poster filename'));
        $show->field('Poster_content', __('Poster content'));
        $show->field('CreateDate', __('CreateDate'));
        $show->field('Operator', __('Operator'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new PosterTab);

        $che = '';
        $fileName = '';
        $signedUrl = '';
        $id = request()->route()->parameters('Poster_id');
        if($id){
            //地区回显
            $exist = DB::table('PosterSet')->where(['Poster_id'=>$id,'Ly_type'=>5])->exists();
            if($exist){
                $che = 1;
            }else{
                $areaid = DB::table('PosterSet')->where(['Poster_id'=>$id,'Ly_type'=>3])->pluck('Areaid');
                $che = json_encode($areaid);
            }
            $fileName = DB::table('PosterTab')->where('Poster_id',$id)->value('Poster_filename');

            $parameterset = DB::table('parameterset')->first();
            $accessKey = $parameterset->AccessKey;
            $secretKey = $parameterset->SecretKey;
            $bucket = $parameterset->posterDomainSpace;
            $domain = $parameterset->posterDomain;
// 构建Auth对象
            $auth = new Auth($accessKey, $secretKey);
// var_dump($auth);
//判断文件是否存在
            $config = new \Qiniu\Config();
            $bucketManager = new \Qiniu\Storage\BucketManager($auth, $config);
// var_dump($bucketManager);
            $exist= $bucketManager->stat($bucket, $fileName);
            if(!isset($exist[0]['fsize'])){

            }else{
                // 私有空间中的外链 http://<domain>/<file_key>
                $baseUrl = $domain."/".$fileName;
                // 对链接进行签名
                $signedUrl = $auth->privateDownloadUrl($baseUrl,86400);
            }

        }

        $form->column(5/6, function ($form) use ($fileName,$signedUrl) {
            $form->text('Poster_No', __('广告编号'))->default($this->returnNo())->readOnly();
            $form->text('Poster_name', __('广告名称'))->required();
            $form->select('File_type', __('文件类型'))->options([0=>'图片文件',1=>'视频文件']);
            $form->datetime('Startdate', __('有效开始时间'))->default(date('Y-m-d H:i:s'));
            $form->datetime('Enddate', __('有效结束时间'))->default(date('Y-m-d H:i:s'));
            $form->text('Postertime', __('播放时间段'))->default('00:00-23:59');
//            $form->file('Poster_filename',__('文件名称'))->attribute(['type'=>'file','width'=>'500'])->required();
//            $form->text('Poster_filename',__('文件名称'))->attribute(['type'=>'file','width'=>'500'])->required();
//            $form->display('Poster_filename',__('文件名称'))->with(function ($Poster_filename) {
//                return "<input type='file' value=123 > ";
//            })->required()->setScript('this.after(123)');

            $form->html(view('postertab.file',compact(['fileName','signedUrl'])),'文件名称')->required();
            $form->hidden('Poster_filename')->required();

            $form->text('Poster_content', __('广告内容'));
            $form->datetime('CreateDate', __('创立时间'))->default(date('Y-m-d H:i:s'));
            $form->text('Operator', '操作人')->default(Admin::user()->name)->required();

        });
        $sql = "select id,parent_id,name,code from (
              select t1.id,t1.parent_id,t1.name,t1.code,
              if(find_in_set(parent_id, @pids) > 0, @pids := concat(@pids, ',', id), 0) as ischild
              from (
                   select id,parent_id,name,code from china_area t order by parent_id, id
              ) t1,
              (select @pids := 1) t2
        ) t3 where ischild != 0";
        $data = DB::select($sql);

        array_unshift($data,['id'=>1,'parent_id'=>0,'code'=>100000,'name'=>'中国']);
        $data = json_encode($data,320);

        $form->html(view('postertab.address',compact(['data','che'])),'地区');
        $form->listbox('place','场所')->height(200)->options(DB::table('place')->pluck('placename','key'));

        $form->hidden('address');

        $form->saving(function (Form $form) {
            if (!$id = $form->model()->Poster_id) {
                $form->Poster_No = $this->returnNo();
            }

           if($form->Poster_filename!==''&& is_object($form->Poster_filename)){
               $entension = $form->Poster_filename -> getClientOriginalExtension();

               // 需要填写你的 Access Key 和 Secret Key
               $parameterset = DB::table('parameterset')->first();
               $accessKey = $parameterset->AccessKey;
               $secretKey = $parameterset->SecretKey;
               $bucket = $parameterset->posterDomainSpace;
               $domain = $parameterset->posterDomain;

               // 上传到七牛后保存的文件名
               $clientName = $form->Poster_No.'.'.$entension;
               // 要上传文件的本地路径
               $realPath = $form->Poster_filename->getRealPath();

               // 构建鉴权对象
               $auth = new Auth($accessKey, $secretKey);
               // 生成上传 Token

               $token = $auth->uploadToken($bucket, $clientName, 86400);
//               $token = $auth->uploadToken($bucket);


               // 初始化 UploadManager 对象并进行文件的上传。
               $uploadMgr = new UploadManager();
               // 调用 UploadManager 的 putFile 方法进行文件的上传。
               list($ret,$err) = $uploadMgr->putFile($token, $clientName, $realPath);
//               $data = $uploadMgr->putFile($token, $clientName, $realPath);

               if ($err !== null) {
                   $error = new MessageBag([
                       'title'   => '提示',
                       'message' => $ret,
                   ]);
                   return back()->with(compact('error'));
               }
               $form->Poster_filename = $clientName;

           }
        });
        return $form;
    }


    /*
        * 生成单号
        */
    public function returnNo()
    {
        $data = DB::select("select sP_Djno('GG',0) as number limit 1");
        $No = $data[0]->number;
        $exists = DB::table('PosterTab')->where('Poster_No', $No)->exists();
        if (!$exists) {
            return $No;
        } else {
            while (true) {
                $data = DB::select("select sP_Djno('GG',1) as number limit 1");
                $No = $data[0]->number;
                $exists = DB::table('PosterTab')->where('Poster_No', $No)->exists();
                if (!$exists) {
                    return $No;
                }
            }
        }
    }


}
