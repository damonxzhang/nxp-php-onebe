<?php

namespace common\models;

class SendMqtt
{
    //推送消息配置
    public $username       = ''; //用户名
    public $password       = ''; //密码
    public $serverHost     = '10.192.144.121'; //地址
    public $serverPort     = '1883';    //端口
    public $serClientName  = 'ser2dev'; //需要链接的服务器名称
    public $serClientName2  = 'ser2dev2'; //需要链接的服务器名称
    public $devClientName  = 'dev2ser'; //推送的服务器名称
    public $serverClientId = 'xiangshaoxia'; //服务器端 MQTT Client id

    const HEAD        = '55AA';//帧头 方向码
    const TAIL        = 'AA';  //帧尾
    const INSTRUCTION = '03';  //指令码
    const ALLLAMP     = '04';  //同步所有灯指令码

    //20灯对应关系
    public static $baseLampArr = [
        '01' => '00', '02' => '01',
        '03' => '02', '04' => '03',
        '05' => '04', '06' => '05',
        '07' => '06', '08' => '07',
        '09' => '08', '10' => '09',
        '11' => '0A', '12' => '0B',
        '13' => '0C', '14' => '0D',
        '15' => '0E', '16' => '0F',
        '17' => '10', '18' => '11',
        '19' => '12', '20' => '13',
    ];

    //20个灯的标识
    public static $LampArr = [
        '00' => '01', '01' => '02',
        '02' => '03', '03' => '04',
        '04' => '05', '05' => '06',
        '06' => '07', '07' => '08',
        '08' => '09', '09' => '10',
        '0A' => '11', '0B' => '12',
        '0C' => '13', '0D' => '14',
        '0E' => '15', '0F' => '16',
        '10' => '17', '11' => '18',
        '12' => '19', '13' => '20',
    ];

    //货架所有灯的状态
    public static $allLampArr = [
        1 => '020001',   //空闲
        2 => '020002',   //醒料中
        3 => '020003',   //可领取
        4 => '020004',   //领取中
        5 => '020005',   //预警中
        6 => '020006',   //过期
        7 => '020007',   //异常
    ];

    //货架状态
    const SHELF_RECEIVE   = 1;  //可领取
    const SHELF_PROGRESS  = 2;  //醒料中
    const SHELF_FREE      = 3;  //空闲
    const SHELF_EXCEPTION = 4;  //异常
    const SHELF_WARNING   = 5;  //预警中
    const SHELF_EXPIRE    = 6;  //过期
    const SHELF_RECEIVED  = 7;  //领取中

    //货架灯的状态
    public static $statusLampArr = [
        self::SHELF_FREE      => '01',   //空闲
        self::SHELF_PROGRESS  => '02',   //醒料中
        self::SHELF_RECEIVE   => '03',   //可领取
        self::SHELF_RECEIVED  => '04',   //领取中
        self::SHELF_WARNING   => '05',   //预警中
        self::SHELF_EXPIRE    => '06',   //过期
        self::SHELF_EXCEPTION => '07',   //异常
    ];

    //重启设备指令
    public static $reloadLampArr = [
        0 => '55AA01080000801AAA', //重启01货架控制系统
    ];

    //注册设备指令
    public static $registerLampArr = [
        0 => '55AA010100005018AA', //响应01设备的注册
    ];

    /**
     * 推送消息到服务器端
     * @param string $mark 货架ID
     * @param string $number 灯ID
     * @param string $status 状态
     * @return bool
     */
    public function publishMessage($mark = '', $number = '', $status = '')
    {
        //参数不能为空
        if (empty($mark) || empty($number) || empty($status)) {
            return false;
        }
        $mark       = '0' . $mark; //货架ID
        $lamp       = self::$baseLampArr[$number];//灯ID
        $str        = '01' . self::INSTRUCTION . $lamp . self::$statusLampArr[$status];//货架,指令码,灯ID,状态码
        // $str        = $mark . self::INSTRUCTION . $lamp . self::$statusLampArr[$status];//货架,指令码,灯ID,状态码
        $crcStr     = (new CRC())->getCRC16($str);//CRC校验值
        $message    = self::HEAD . $str . $crcStr . self::TAIL;
        $mqttServer = new phpMQTT($this->serverHost, $this->serverPort, $this->serverClientId);
        $connect    = $mqttServer->connect(true, NULL, $this->username, $this->password);
        
        if ($connect) {
            if ($mark == '01' || $mark == 1){
                $mqttServer->publish($this->serClientName, $message, 0, false);//最后参数设为false是为了去除服务器保留消息,true就是保留消息
            }
            elseif ($mark == '02' || $mark == 2){
                $mqttServer->publish($this->serClientName2, $message, 0, false);//最后参数设为false是为了去除服务器保留消息,true就是保留消息
                // print_r($message);
            }
            else{
                $mqttServer->publish($this->serClientName, $message, 0, false);//最后参数设为false是为了去除服务器保留消息,true就是保留消息

            }
        // if ($connect) {
            // $mqttServer->publish($this->serClientName, $message, 0, false);//最后参数设为false是为了去除服务器保留消息,true就是保留消息
            $mqttServer->close();
            return true;
        }
        return false;
    }

    /**
     * 发送消息到服务器端
     * @param string $message
     */
    public function sendMqttMessage($message = '')
    {
        $mqttServer = new phpMQTT($this->serverHost, $this->serverPort, $this->serverClientId);
        $connect    = $mqttServer->connect(true, NULL, $this->username, $this->password);
        if ($connect) {
            $mqttServer->publish($this->serClientName, $message, 0, false);//最后参数设为false是为了去除服务器保留消息,true就是保留消息
            $mqttServer->close();
        }
    }

    /**
     * 接收服务器端的消息
     * @return bool
     */
    public function acceptMessage()
    {
        $mqttServer = new phpMQTT($this->serverHost, $this->serverPort, $this->serverClientId);
        $connect    = $mqttServer->connect(true, NULL, $this->username, $this->password);
        if (!$connect) {
            return false;
        }
        //订阅消息
        $key          = $this->devClientName;
        $topics[$key] = array("qos" => 0, "function" => array($this, "message"));
        $mqttServer->subscribe($topics, 0);
        $mqttServer->proc();
        $mqttServer->close();
    }

    /**
     * 回调函数接收消息
     * @param $topic
     * @param $message
     * @return bool
     */
    public function message($topic, $message)
    {
        $message = trim($message);
        if (empty($message)) {
            return false;
        }
        exit;
    }

}