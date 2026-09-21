<?php
namespace console\controllers;

use backend\models\Shelf;
use backend\models\ShelfApply;
use backend\models\SocketInfos;
use yii\console\Controller;
use yii\log\Logger;

header("content-type:text/html;charset=utf-8");

class SocketController extends Controller{

    private static $run_time = 0;
    private $ip = "10.192.144.117";
    private $port = 2000;
    private $socket;
    private $sleep_time = 1;//多长时间执行一次

    public function connect(){
        echo "初始化socket通信客户端\n";
        error_reporting(E_ALL);
        set_time_limit(0);

        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $result = socket_connect($this->socket, $this->ip, $this->port);
        if ($result < 0) {
            echo "socket链接失败.返回信息: ($result) " . socket_strerror($result) . "\n";

        }else {
            echo "socket连接成功\n";
        }
    }

    public function getMsg(){
        echo "接受信息\n";
        while($out = socket_read($this->socket, 8192)) {
            echo "接收服务器回传信息成功！\n";

            echo "接受的内容为:",$out."\n";

            Log::error( "接受的内容为:".json_encode($out));
        }
    }

    public function send(){

        self::$run_time = time();
        while(true){
            if (self::$run_time == time()){

                $data = SocketInfos::find()->where(['status' => 1])->one();
                if (!empty($data)){
                    $msg = $data->value;

                    if(!socket_write($this->socket, $msg, strlen($msg))) {
                        \Yii::getLogger()->log('信息发送失败！错误信息'.socket_strerror($this->socket).' id'.$data->id,Logger::LEVEL_ERROR);

                    } else {
                        $data->status = SocketInfos::STATUS_SUCCESS;
                        $data->save();
                        echo "发送操作数据到服务器信息成功！\n";
                        echo $msg."\n";
                        \Yii::getLogger()->log('物料状态信息发送成功！发送的消息'."$msg",Logger::LEVEL_ERROR);

                    }
                }

                $shelf = Shelf::find()->select('shelf_number, status, car_number')->asArray()->one();
                if (!empty($shelf)){
                    $msg = json_encode($shelf);

                    if(!socket_write($this->socket, $msg, strlen($msg))) {
                        \Yii::getLogger()->log('同步货架信息失败！错误信息'.socket_strerror($this->socket).$msg,Logger::LEVEL_ERROR);

                    } else {

                        echo "货架信息成功发送到服务器！\n";
                        \Yii::getLogger()->log('货架状态信息发送成功！发送的消息'."$msg",Logger::LEVEL_ERROR);
                    }
//                    echo '发送的消息：'. $msg. "\n";
                }

                self::$run_time += $this->sleep_time;
            }
        }
    }

    public function close(){
        socket_close($this->socket);
        echo '关闭'. "\n";
    }

    public function actionIndex(){
        $this->connect();
        $this->send();

    }
}