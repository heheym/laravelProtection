<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Workerman\Worker;

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
        // 初始化一个worker容器, 监听19999端口, 用于接收浏览器websocket请求
        $worker = new Worker('websocket://0.0.0.0:8081');

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
                $res = sendMessageByUid($data['srvkey'], $send);
                $connection->send($res ? true : false);
            };
            $inner_text_worker->listen();
        };


// 当有客户端发来消息时执行的回调函数, 客户端需要表明自己是哪个uid
        $worker->onMessage = function ($connection, $data){
            $data = json_decode($data,true);
            global $worker;
            if(!empty($data['srvkey'])){
                $connection->uid = $data['srvkey'];
                $worker->uidConnections[$connection->uid] = $connection;
                $respond = json_encode(['code'=>200,'msg'=>'请求成功','data'=>null],JSON_UNESCAPED_UNICODE);
                $connection->send($respond);
                return;
            }
            if(!isset($connection->uid)){
                // 没验证的话把第一个包当做uid（这里为了方便演示，没做真正的验证）
                $respond = json_encode(['code'=>200,'msg'=>'没有授权','data'=>null],JSON_UNESCAPED_UNICODE);
                $connection->send($respond);
                return;
            }
        };

        $worker->onClose = function ($connection){
            global $worker;
            if(isset($connection->uid)){
                unset($worker->uidConnections[$connection->uid]);
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
            global $worker;
            if(isset($worker->uidConnections[$uid])){
                $connection = $worker->uidConnections[$uid];
                $connection->send($message);
                return true;
            }
            return false;
        }
        Worker::runAll();
    }


}
