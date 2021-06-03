<?php

namespace App\Admin\Controllers;

use App\Admin\Models\SoftVersion;
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

class SoftVersionController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '版本管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new SoftVersion);

        $grid->column('name', __('名称'));
        $grid->column('description', __('描述'));
        // $grid->column('pic','图片');
        $grid->column('version', __('版本号'));

        $grid->column('pic', __('图片'))->display(function ($pic) {
            $parameterset = DB::table('parameterset')->first();
            $accessKey = $parameterset->AccessKey;
            $secretKey = $parameterset->SecretKey;

            $bucket = $parameterset->posterDomainSpace;
            $domain = $parameterset->posterDomain;

            $auth = new Auth($accessKey, $secretKey);
            // 私有空间中的外链 http://<domain>/<file_key>
            $baseUrl = $domain."/".$pic;
            // 对链接进行签名
            $signedUrl = $auth->privateDownloadUrl($baseUrl,86400);
            return "<a href=".$signedUrl." target='_blank'>$pic</a>";
        });
        $grid->column('versionaddress', __('软件地址'))->display(function ($versionaddress) {
            $parameterset = DB::table('parameterset')->first();
            $accessKey = $parameterset->AccessKey;
            $secretKey = $parameterset->SecretKey;

            $bucket = $parameterset->posterDomainSpace;
            $domain = $parameterset->posterDomain;

            $auth = new Auth($accessKey, $secretKey);
            // 私有空间中的外链 http://<domain>/<file_key>
            $baseUrl = $domain."/".$versionaddress;
            // 对链接进行签名
            $signedUrl = $auth->privateDownloadUrl($baseUrl,86400);
            return "<a href=".$signedUrl." target='_blank'>$versionaddress</a>";
        });


        $grid->actions(function ($actions) {
            $actions->disableView();
            if (!Admin::user()->can('版本管理删除')) {
                $actions->disableDelete();
            }
            if (!Admin::user()->can('版本管理修改')) {
                $actions->disableEdit();
            }
        });
        if (!Admin::user()->can('版本管理添加')) {
            $grid->disableCreateButton();  //版本管理的权限
        }

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
    //  */
    protected function detail($id)
    {
        // $show = new Show(PosterTab::findOrFail($id));
        //
        // $show->field('Poster_id', __('Poster id'));
        // $show->field('Poster_No', __('Poster No'));
        // $show->field('Poster_name', __('Poster name'));
        // $show->field('File_type', __('File type'));
        // $show->field('Startdate', __('Startdate'));
        // $show->field('Enddate', __('Enddate'));
        // $show->field('Postertime', __('Postertime'));
        // $show->field('Poster_mode', __('Poster mode'));
        // $show->field('Poster_filename', __('Poster filename'));
        // $show->field('Poster_content', __('Poster content'));
        // $show->field('CreateDate', __('CreateDate'));
        // $show->field('Operator', __('Operator'));
        //
        // return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new SoftVersion);


        $pic = '';
        $signedUrl = '';
        $versionaddress = '';
        $versionAdressUrl = '';
        $id = request()->route()->parameters('Poster_id');
        $parameterset = DB::table('parameterset')->first();
        if($id){

            $softversion = DB::table('softversion')->where('id',$id)->first();
            $pic = $softversion->pic;
            $versionaddress = $softversion->versionaddress;

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
            $exist= $bucketManager->stat($bucket, $pic);
            if(!isset($exist[0]['fsize'])){

            }else{
                // 私有空间中的外链 http://<domain>/<file_key>
                $baseUrl = $domain."/".$pic;
                // 对链接进行签名
                $signedUrl = $auth->privateDownloadUrl($baseUrl,86400);

                $versionaddressUrl = $domain."/".$versionaddress;
                $versionAdressUrl = $auth->privateDownloadUrl($versionaddressUrl,86400);
            }

        }

        $form->column(5/6, function ($form) use ($pic,$signedUrl,$versionaddress,$versionAdressUrl) {
            $form->text('name','名称')->required();
            $form->text('description','描述')->required();
            $form->text('version','版本号')->required();
            $form->hidden('pic','图片')->required();
            $form->hidden('versionaddress','版本地址')->required();

            $form->html(view('softversion.file',compact(['pic','signedUrl'])),'图片')->required();
            $form->html(view('softversion.versionaddress',compact(['versionaddress','versionAdressUrl'])),'软件地址')->required();

        });


        $form->saving(function (Form $form) {

           if($form->pic!==''&& is_object($form->pic)){
               // $entension = $form->pic -> getClientOriginalExtension();

               $clientName = $form->pic->getClientOriginalName();

               // 需要填写你的 Access Key 和 Secret Key
               $parameterset = DB::table('parameterset')->first();

               $accessKey = $parameterset->AccessKey;
               $secretKey = $parameterset->SecretKey;
               $bucket = $parameterset->posterDomainSpace;
               $domain = $parameterset->posterDomain;

               // 上传到七牛后保存的文件名
               // $clientName = $form->Poster_No.'.'.$entension;
               // 要上传文件的本地路径
               $realPath = $form->pic->getRealPath();

               // 构建鉴权对象
               $auth = new Auth($accessKey, $secretKey);
               // 生成上传 Token

               $token = $auth->uploadToken($bucket, $clientName, 86400);
              // $token = $auth->uploadToken($bucket);


               // 初始化 UploadManager 对象并进行文件的上传。
               $uploadMgr = new UploadManager();
               // 调用 UploadManager 的 putFile 方法进行文件的上传。
               list($ret,$err) = $uploadMgr->putFile($token, $clientName, $realPath);
              // $data = $uploadMgr->putFile($token, $clientName, $realPath);

               if ($err !== null) {
                   $error = new MessageBag([
                       'title'   => '提示',
                       'message' => $ret,
                   ]);
                   return back()->with(compact('error'));
               }
               $form->pic = $clientName;
           }
            if($form->versionaddress!==''&& is_object($form->versionaddress)){
                // $entension = $form->versionaddress -> getClientOriginalExtension();

                $clientName = $form->versionaddress->getClientOriginalName();

                // 需要填写你的 Access Key 和 Secret Key
                $parameterset = DB::table('parameterset')->first();

                $accessKey = $parameterset->AccessKey;
                $secretKey = $parameterset->SecretKey;
                $bucket = $parameterset->posterDomainSpace;
                $domain = $parameterset->posterDomain;

                // 上传到七牛后保存的文件名
                // $clientName = $form->Poster_No.'.'.$entension;
                // 要上传文件的本地路径
                $realPath = $form->versionaddress->getRealPath();

                // 构建鉴权对象
                $auth = new Auth($accessKey, $secretKey);
                // 生成上传 Token

                $token = $auth->uploadToken($bucket, $clientName, 86400);
                // $token = $auth->uploadToken($bucket);


                // 初始化 UploadManager 对象并进行文件的上传。
                $uploadMgr = new UploadManager();
                // 调用 UploadManager 的 putFile 方法进行文件的上传。
                list($ret,$err) = $uploadMgr->putFile($token, $clientName, $realPath);
                // $data = $uploadMgr->putFile($token, $clientName, $realPath);

                if ($err !== null) {
                    $error = new MessageBag([
                        'title'   => '提示',
                        'message' => $ret,
                    ]);
                    return back()->with(compact('error'));
                }
                $form->versionaddress = $clientName;
            }

        });
        return $form;
    }




}
