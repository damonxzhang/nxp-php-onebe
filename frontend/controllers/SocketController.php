<?php
namespace frontend\controllers;

use backend\models\Shelf;
use backend\models\ShelfApply;
use backend\models\SocketInfos;
use backend\models\SocketLog;
use yii\console\Controller;
use yii\log\Logger;
use Yii;

header("content-type:text/html;charset=utf-8");

class SocketController extends Controller{

    private static $run_time = 0;
    private $ip = "10.192.144.117";
    private $port = 2000;
    private $socket;
    private $sleep_time = 2;//多长时间执行一次

    public function connect(){
        error_reporting(E_ALL);
        set_time_limit(0);

        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $result = socket_connect($this->socket, $this->ip, $this->port);
        if ($result < 0) {
            $query = new SocketLog();
            $query->msg = "socket链接失败.返回信息: ($result) " . socket_strerror($result);
            $query->create_time = date('Y-m-d H:i:s');
            $query->save();

        }else {
            $query = new SocketLog();
            $query->msg = "socket连接成功";
            $query->create_time = date('Y-m-d H:i:s');
            $query->save();
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

        // 要取出所有进行循环
        // $data = SocketInfos::find()->where(['status' => 1])->asArray()->all();
        // foreach ($data as $key => $value) {
        //     $msg = $value->value;
        //     if(!socket_write($this->socket, $msg, strlen($msg))) {
        //         $query = new SocketLog();
        //         $query->socket_id = $value->id;
        //         $query->msg = '信息发送失败！错误信息:'.$msg;
        //         $query->create_time = date('Y-m-d H:i:s');
        //         $query->save();
        //     } else {
        //         $value->status = SocketInfos::STATUS_SUCCESS;
        //         $value->save();

        //         $query = new SocketLog();
        //         $query->socket_id = $value->id;
        //         $query->msg = '发送操作数据到服务器信息成功！'.$msg;
        //         $query->create_time = date('Y-m-d H:i:s');
        //         $query->save();

        //     }
        // }
        
        // $data = SocketInfos::find()->where(['status' => 1])->one();
        // if (!empty($data)){
        //     $msg = $data->value;
        //     if(!socket_write($this->socket, $msg, strlen($msg))) {
        //         $query = new SocketLog();
        //         $query->socket_id = $data->id;
        //         $query->msg = '信息发送失败！错误信息:'.$msg;
        //         $query->create_time = date('Y-m-d H:i:s');
        //         $query->save();
        //     } else {
        //         $data->status = SocketInfos::STATUS_SUCCESS;
        //         $data->save();

        //         $query = new SocketLog();
        //         $query->socket_id = $data->id;
        //         $query->msg = '发送操作数据到服务器信息成功！'.$msg;
        //         $query->create_time = date('Y-m-d H:i:s');
        //         $query->save();

        //     }
        // }
        echo "初始化socket通信客户端\n";
        error_reporting(E_ALL);
        set_time_limit(0);

        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $result = socket_connect($this->socket, $this->ip, $this->port);
        if ($result < 0) {
            echo "socket链接失败.返回信息: ($result) " . socket_strerror($result) . "\n";

        }else {
            echo "socket连接成功111\n";
        }

        $i = 0;
        for($i;$i<30;$i++){
            $data = SocketInfos::find()->where(['status' => 1])->one();
            if (!empty($data)){
                $msg = $data->value;
                if(!socket_write($this->socket, $msg, strlen($msg))) {
                    \Yii::getLogger()->log('信息发送失败！错误信息'.socket_strerror($this->socket).' id'.$data->id,Logger::LEVEL_ERROR);
                } else {
                    echo socket_last_error();
                    $data->status = SocketInfos::STATUS_SUCCESS;
                    $data->save();
                    echo "\n发送操作数据到服务器信息成功！\n";
                    echo $msg."\n";
                    \Yii::getLogger()->log('物料状态信息发送成功！发送的消息'."$msg",Logger::LEVEL_ERROR);
                }
            }
            usleep(2000000);
        }
       
                
    }

    public function close(){
        socket_close($this->socket);
        echo '关闭'. "\n";
    }

    public function actionIndex(){
        // $this->connect();
        $this->send();

    }
    
    public function actionCamstar(){
        error_reporting(E_ALL);
        set_time_limit(0);

        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $result = socket_connect($this->socket, '10.192.130.252', '22081');
        if ($result >= 0) {
            $msg = '{"appName":"MFGBE1","data":{"MESLotInfoRequest":{"appName":"MFGBE1","attributes":["packageOutlineName"],"eqpName":"T21OVN","wipFlagSelection":"NA"}},"eqpName":"T21OVN","subject":"ATTJ.REQ.MES.LOT_INFO.[EqpName]"}';
            if(!socket_write($this->socket, $msg, strlen($msg))) {
                print_r('失败');
            } else {
                print_r('成功');
                while($out = socket_read($this->socket, 8192)) {
                    echo "接收服务器回传信息成功！\n";
        
                    echo "接受的内容为:",$out."\n";
        
                    Log::error( "接受的内容为:".json_encode($out));
                }

            }
        }
        $this->close();

    }
}