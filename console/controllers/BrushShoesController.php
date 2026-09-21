<?php
namespace console\controllers;

use backend\models\Blog;
use backend\models\BrushShoes;
use backend\models\UserInfo;
use common\models\Utility;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
header("content-type:text/html;charset=utf-8");

class BrushShoesController extends Controller
{
    /**
     * 刷卡机
     *
     */
    public function actionIndex()
    {
        ignore_user_abort(true); //后台运行
        set_time_limit(0);       //取消脚本运行时间的超时上限
        date_default_timezone_set('PRC'); //设置中国时区

        $ThisIp = $this->get_local_ip();   //获取电脑IP地址
        $server = 'udp://'.$ThisIp.':39169';
        $socket = stream_socket_server($server, $errno, $errstr, STREAM_SERVER_BIND);
        if (!$socket)
        {
            die("$errstr ($errno)");
        }
      
    
        $LastBuf= array(9);      //保存最后接收到的信息，用于比较是否重复接收的信息
        do{
            //监听UDP端口，并将接收到的数据以16进制方式显示出来，正式项目只解析数据不需要显示
            $inMsg = stream_socket_recvfrom($socket, 1024, 0, $peer);
            $len = strlen($inMsg);
            $bytes = array();
            $Getinf='';
            for($i=0;$i<$len;$i++)
            {
                $byte = ord($inMsg[$i]);
                $bytes[] =  $byte ;
                $b=substr('00'.dechex($byte ),-2);
                $Getinf=$Getinf.$b.' ';
            }
            //对接收到的数进行解析
            if($len >8){
               
                if($bytes[0]==$LastBuf[0] && $bytes[1]==$LastBuf[1] && $bytes[2]==$LastBuf[2] && $bytes[3]==$LastBuf[3] && $bytes[4]==$LastBuf[4] && $bytes[5]==$LastBuf[5] && $bytes[6]==$LastBuf[6] && $bytes[7]==$LastBuf[7] && $bytes[8]==$LastBuf[8]){
                    $this->DisableSendAge($bytes,$peer ) ;  //接收到相同的信息，确定接收到刷卡器数据,否则读卡器会重发三次，不用处理
                 
                }else{
                  
                    for($i=0;$i<9;$i++){        //将本次接收到且已解析处理的信息保存，用于下次接收到信息时比对是否重复接收到的信息
                        $LastBuf[$i]=$bytes[$i];
                    }
                    switch($bytes[0]){
                        case hexdec('0xD1'):     //接收到ID卡信息
                            $this->DisableSendAge($bytes,$peer ) ;  //确定接收到刷卡器数据,否则读卡器会重发三次
                            $CardNo=$bytes[9] & 0xff;
                            $CardNo=$CardNo+($bytes[10] & 0xff) *256;
                            $CardNo=$CardNo+($bytes[11] & 0xff) *65536;
                            $CardNo=$CardNo+($bytes[12] & 0xff) *16777216;
                            $CardNo=substr("0000000000".$CardNo,-10);
                            $sixtheen = base_convert( $CardNo,10,16);
                            $dec =base_convert(substr($sixtheen,-4),16,10);
                            $code = str_pad($dec,5,0,STR_PAD_LEFT);//补充字符串

                            $query = UserInfo::findOne(['meal_card_code' => $code]);
                            if ($query){
                                $model = new BrushShoes();
                                $model->user_id = $query->id;
                                $model->brush_date = date('Y-m-d H:i:s', time());
                                $model->create_time = date('Y-m-d H:i:s', time());
                                if($model->save(false)){
                                    $DispStr="SUCCESS :".substr(date('Y-m-d h:i:s', time()),0,16)."            "; //全屏显示信息长度>=34
                                }else{
                                    $DispStr="FAIL :".substr(date('Y-m-d h:i:s', time()),0,16)."            "; //全屏显示信息长度>=34
                                }
                            }else{
                                $DispStr="FAIL :".substr(date('Y-m-d h:i:s', time()),0,16)."            "; //全屏显示信息长度>=34
                            }
                            $this->SendDispBeep($DispStr,$peer); //向读卡器发送显示及蜂鸣响声
                            break;
                        default:
                    }
                }
            }
        } while ($inMsg !== false);

    }

    /**
     * 向读卡器发送显示及蜂鸣响声
     * @param $DispStr
     * @param $peer
     */
    function SendDispBeep($DispStr,$peer)
    {
        $bytes[0]=hexdec('0x5A');             //指令码
        $bytes[1]=0;                          //机号低,机号为0表示广播
        $bytes[2]=0;                          //机号高
        $bytes[3]=2;                          //蜂鸣声代码
        $bytes[4]=20;                         //显示时长
        $sendBuf='';
        for($i=0;$i<5;$i++)
        {
            $sendBuf=$sendBuf.chr($bytes[$i]);
        }
        $sendBuf=$sendBuf.$DispStr;
        $handle = stream_socket_client("udp://".$peer, $errno, $errstr);
        fwrite($handle, $sendBuf);
//        $Sendinf='';                    //将发送的信息用16进制数显示，方便调式
//        for($i=0;$i<strlen($sendBuf);$i++){
//            $byte = ord($sendBuf[$i]);
//            $b=substr('00'.dechex($byte ),-2);
//            $Sendinf=$Sendinf.$b.' ';
//        }
//        echo "SendTo IP:".$peer."\n";
//        echo "SendData :{$Sendinf}\n\n";
    }

    /**
     * 确定接收到刷卡器数据，以免读卡器重复发送
     * @param $bytes
     * @param $peer
     */
    function DisableSendAge($bytes,$peer)
    {
        $bytes[0]=105;
        $sendBuf='';
        for($i=0;$i<9;$i++)
        {
            $sendBuf=$sendBuf.chr($bytes[$i]);
        }
        $handle = stream_socket_client("udp://".$peer, $errno, $errstr);
        fwrite($handle, $sendBuf);

    //    $Sendinf='';                    //将发送的信息用16进制数显示，方便调式
    //    for($i=0;$i<strlen($sendBuf);$i++){
    //        $byte = ord($sendBuf[$i]);
    //        $b=substr('00'.dechex($byte ),-2);
    //        $Sendinf=$Sendinf.$b.' ';
    //    }
    //    echo "SendTo IP:".$peer."\n";
    //    echo "SendData :{$Sendinf}\n\n";
    }

    /**
     * 获取本机IP地址
     * @return string
     */
    function get_local_ip()
    {
        $preg = "/\A((([0-9]?[0-9])|(1[0-9]{2})|(2[0-4][0-9])|(25[0-5]))\.){3}(([0-9]?[0-9])|(1[0-9]{2})|(2[0-4][0-9])|(25[0-5]))\Z/";
        //获取操作系统为win2000/xp、win7的本机IP真实地址
        exec("ipconfig", $out, $stats);
        if (!empty($out)) {
            foreach ($out AS $row) {
                if (strstr($row, "IP") && strstr($row, ":") && !strstr($row, "IPv6")) {
                    $tmpIp = explode(":", $row);
                    if (preg_match($preg, trim($tmpIp[1]))) {
                        return trim($tmpIp[1]);
                    }
                }
            }
        }
        //获取操作系统为linux类型的本机IP真实地址
        exec("ifconfig", $out, $stats);
        if (!empty($out)) {
            if (isset($out[1]) && strstr($out[1], 'addr:')) {
                $tmpArray = explode(":", $out[1]);
                $tmpIp = explode("", $tmpArray[1]);
                if (preg_match($preg, trim($tmpIp[0]))) {
                    return trim($tmpIp[0]);
                }
            }
        }
        return '127.0.0.1';
    }

}
