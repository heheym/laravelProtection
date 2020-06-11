<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Workerman\Worker;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class WorkermanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'workerman {action} {--d}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start a Workerman server.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        global $argv;
        $action = $this->argument('action');
        $argv[1] = $action;
        $argv[2] = $this->option('d') ? '-d' : '';//该参数是以daemon（守护进程）方式启动
        $this->start();
    }

    private function start()
    {

//        Log::info('123'.PHP_EOL);
        // 初始化一个worker容器, 监听19999端口, 用于接收浏览器websocket请求
        $worker = new Worker('websocket://0.0.0.0:8081');

        $worker->onConnect = function($connection)
        {
            $connection->onWebSocketConnect = function($connection , $http_header)
            {
Log::getMonolog()->popHandler();
Log::useDailyFiles(storage_path('logs/WkOnConnect.log'));
                // 可以在这里判断连接来源是否合法，不合法就关掉连接
                 if(empty($_GET['srvkey'])){
                     $respond = json_encode(['code'=>500,'msg'=>'srvkey错误','data'=>null],JSON_UNESCAPED_UNICODE);
                     $connection->send($respond);
Log::info('连接失败,srvkey错误'.PHP_EOL);
                     return;
                 }
                 $exists = DB::table('place')->where('key',$_GET['srvkey'])->exists();
                 if(!$exists){
                     $respond = json_encode(['code'=>500,'msg'=>'srvkey不存在','data'=>null],JSON_UNESCAPED_UNICODE);
                     $connection->send($respond);
Log::info('连接失败,srvkey不存在'.PHP_EOL);
                     return;
                 }
                $connection->uid = $_GET['srvkey'];
                global $worker;
                $worker->uidConnections[$connection->uid] = $connection;
                $respond = json_encode(['code'=>200,'func'=>'connect','msg'=>'连接成功','data'=>null],JSON_UNESCAPED_UNICODE);
                $connection->send($respond);
Log::info('连接成功,srvkey:'.$_GET['srvkey'].PHP_EOL);
                return;
            };
        };

        $worker->count = 1;
        $worker->uidConnections = [];

// worker进程启动后创建一个text Worker以便打开一个内部通讯端口
        $worker->onWorkerStart = function ($worker){

            // 开启一个内部端口，方便内部系统推送数据，Text协议格式 文本+换行符
            $inner_text_worker = new Worker('Text://0.0.0.0:82');
            $inner_text_worker->onMessage = function ($connection, $buffer){
                // 使用uid判断需要向哪个页面推送数据
                // $data数组格式，里面有uid，表示向那个uid的页面推送数据
                $data = json_decode($buffer, true);
                $send = json_encode($data,JSON_UNESCAPED_UNICODE);
//                Log::info('456'.PHP_EOL);
                $res = sendMessageByUid($data['srvkey'], $send);
                $connection->send($res ? 'success' : 'false');
            };
            $inner_text_worker->listen();
        };


// 当有客户端发来消息时执行的回调函数, 客户端需要表明自己是哪个uid
        $worker->onMessage = function ($connection, $data){
Log::getMonolog()->popHandler();
Log::useDailyFiles(storage_path('logs/WkOnMessage.log'));

            $data = json_decode($data,true);

            global $worker;

            if(!isset($connection->uid)){
                // 没验证的话把第一个包当做uid（这里为了方便演示，没做真正的验证）
                $respond = json_encode(['code'=>500,'msg'=>'没有授权,断开连接','data'=>null],JSON_UNESCAPED_UNICODE);
                $connection->send($respond);
Log::info('授权失败,没有授权,断开连接'.PHP_EOL);
                $connection->close();
                return;
            }
            //"func":"confirm_order",
            if(!empty($data['func']) && $data['func'] == 'confirm_order'){
                if(empty($data['order_id'])){
                    $respond = json_encode(['code'=>500,'msg'=>'订单号不能为空','data'=>null],JSON_UNESCAPED_UNICODE);
                    $connection->send($respond);
Log::info('处理失败,订单号不能为空,srvkey:'.$connection->uid.',data:'.json_encode($data).PHP_EOL);
                    return;
                }
                $exists = DB::table('ordersn')->where('leshua_order_id',$data['order_id'])->exists();
                if(!$exists){
                    $respond = json_encode(['code'=>500,'msg'=>'订单不存在','data'=>null],JSON_UNESCAPED_UNICODE);
                    $connection->send($respond);
Log::info('处理失败,订单不存在,srvkey:'.$connection->uid.',data:'.json_encode($data).PHP_EOL);
                    return;
                }
                $result = DB::table('ordersn')->where('leshua_order_id',$data['order_id'])->update(['confirm_order'=>1]);
                if($result){
                    $respond = json_encode(['code'=>200,'msg'=>'请求成功','data'=>null],JSON_UNESCAPED_UNICODE);
                    $connection->send($respond);
Log::info('处理成功,srvkey:'.$connection->uid.',data:'.json_encode($data).PHP_EOL);
                    return;
                }else{
                    $respond = json_encode(['code'=>200,'msg'=>'请求成功,订单已处理','data'=>null],JSON_UNESCAPED_UNICODE);
                    $connection->send($respond);
Log::info('处理成功,订单已处理,srvkey:'.$connection->uid.',data:'.json_encode($data).PHP_EOL);
                    return;
                }

            }

            //"func":"query_order",
            if(!empty($data['func']) && $data['func'] == 'query_order'){
                if(empty($data['srvkey_id'])){
                    $respond = json_encode(['code'=>500,'msg'=>'srvkey不能为空','data'=>null],JSON_UNESCAPED_UNICODE);
                    $connection->send($respond);
Log::info('查询失败,srvkey不能为空,srvkey:'.$connection->uid.',data:'.json_encode($data).PHP_EOL);
                    return;
                }
                $exists = DB::table('place')->where('key',$data['srvkey_id'])->exists();
                if(!$exists){
                    $respond = json_encode(['code'=>500,'msg'=>'srvkey不存在','data'=>null],JSON_UNESCAPED_UNICODE);
                    $connection->send($respond);
Log::info('查询失败,srvkey不存在,srvkey:'.$connection->uid.',data:'.json_encode($data).PHP_EOL);
                    return;
                }
                $data = DB::table('ordersn')->where(['key'=>$data['srvkey_id'],'order_status'=>1,'confirm_order'=>0])->select('KtvBoxid','pay_time','leshua_order_id','amount')->get();
                $respond = json_encode(['func'=>'query_order_result','data'=>$data],JSON_UNESCAPED_UNICODE);
Log::info('查询成功,srvkey:'.$connection->uid.',data:'.json_encode($data).PHP_EOL);
                $connection->send($respond);
                return;
            }

        $respond = json_encode(['code'=>500,'msg'=>'请求失败,格式错误','data'=>$data],JSON_UNESCAPED_UNICODE);
        $connection->send($respond);
Log::info('请求失败,srvkey:'.$connection->uid.',data:'.json_encode($data).PHP_EOL);
        return;
        };

        $worker->onClose = function ($connection){
            global $worker;
            Log::getMonolog()->popHandler();
            Log::useDailyFiles(storage_path('logs/WkOnClose.log'));
            if(isset($connection->uid)){
                unset($worker->uidConnections[$connection->uid]);
                Log::info('断开连接,srvkey:'.$connection->uid);
            }
        };

        function broadCast($message){
            global $worker;
            if(!empty($worker->uidConnections)){
                foreach ($worker->uidConnections as $connection){
                    $connection->send($message);
                }
                return true;
            }
            return false;
        }

// 向客户端某一个uid推送数据
        function sendMessageByUid($uid, $message){
            Log::getMonolog()->popHandler();
            Log::useDailyFiles(storage_path('logs/sendMessageByUid.log'));
            global $worker;
            if(isset($worker->uidConnections[$uid])){
                $connection = $worker->uidConnections[$uid];
                $connection->send($message);
                Log::info('推送成功,srvkey:'.$uid);
                return true;
            }else{
                Log::info('推送失败,场所没有建立连接,srvkey:'.$uid);
                return false;
            }

        }
        Worker::runAll();
    }


}
