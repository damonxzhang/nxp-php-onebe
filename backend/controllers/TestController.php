<?php

namespace backend\controllers;

use backend\models\Appeal;
use backend\models\Facility;
use backend\models\Ir;
use backend\models\MachineModel;
use backend\models\MachineVendor;
use backend\models\MaintainType;
use backend\models\Shelf;
use backend\models\Token;
use backend\models\User;
use backend\models\UserInfo;
use backend\models\UserStation;
use common\models\phpMQTT;
use common\models\SendMqtt;
use common\models\Upload;
use common\models\Utility;
use xj\qrcode\actions\QRcodeAction;
use xj\qrcode\QRcode;
use xj\qrcode\widgets\Text;
use Yii;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use backend\models\BrushShoes;
use backend\models\Esdresult;
use yii\helpers\Json;

class TestController extends BaseController
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
         return [
             [
                 'class' => 'common\models\Upload',
                 'type' => Upload::VEDIO_TYPE
             ]
         ];
    }

    /**
     * @return string
     */
    public function actionPicture()
    {
        return $this->render('picture');
    }

    public function actionImport()
    {
        // //洗鞋机数据
        // $query = BrushShoes::find();
        // $query->alias('b');
        // $query->leftJoin(['u' => UserInfo::tableName()], 'b.user_id = u.id');
        // $query->distinct();
        // $query->select('u.meal_card_code');
        // $query->where(['u.shoe_type' => 1]);
        // $beforeDateHour = date('Y-m-d H:i:s', strtotime('-26hour'));//当前时间戳-26小时
        // $query->andWhere(['>=', 'brush_date', $beforeDateHour]);
        // $brushCard = $query->column();

        // //三辊闸数据
        // $esdQuery = Esdresult::find();
        // $esdQuery->select('cardno');
        // $esdQuery->distinct();
        // $startDate = date("Y-m-d 08:00:00", strtotime("-1 day"));//昨天八点
        // $endDate   = date("Y-m-d 08:00:00", time());             //今天八点
        // $esdQuery->where(['>', 'timedate', $startDate]);
        // $esdQuery->andWhere(['<=', 'timedate', $endDate]);  
        // $gateCard = $esdQuery->column();

        // $cardResult = array_diff($gateCard, $brushCard);//比较数据中差值
        // $dataProvider = UserInfo::find()->select(['chinese_name', 'pid'])->where(['in', 'meal_card_code', $cardResult])->andWhere(['shoe_type' => 1])->asArray()->all();
        // $tdData       = '';
        // foreach($dataProvider as $value) {
        //     $tdData .= '<tr class="content"><td>' . $value['chinese_name'] . '</td><td>' . $value['pid'] . '</td></tr>';
        // }
        // $body       = '<div><table border = 1><tr class="th"><th>姓名 </th><th>PID</th></tr>' . $tdData . '</table></div>';
        // $idList     = [];
        // $mail = Yii::$app->mailer->compose();
        // $mail->setTo('894698693@qq.com');                            //要发送给那个人的邮箱
        // $mail->setSubject('Washing machine unpassing card list'); //邮件主题
        // $mail->setHtmlBody($body);                                //发送的消息内容
        // $mail->send();
        // echo "<pre>";var_dump($dataProvider);die;

    	echo phpinfo();die;
        if (Yii::$app->request->isPost) {
            $file = $_FILES['file'];
            if (empty($file['tmp_name'])) {
                return $this->json(100, '上传文件不能为空!');
            }
            
            $tmp    = explode('.', $file['name']);
            $suffix = array_pop($tmp);
            if ('xlsx' !== $suffix) {
                return $this->json(100, '上传文件无法识别，请使用模版!');
            }
            
            if ($file['size'] > 2097152) {
                return $this->json(100, '上传文件不能超过2MB!');
            }
            $excelData = Utility::getExcelData($file['tmp_name']);
            if (empty($excelData)) {
                return $this->json(100, '上传文件无内容!');
            }
            $return = [
                'successCount' => 0,
                'errorCount'   => 0,
                'errorList'    => '',
            ];
            $errorList = [];
            foreach ($excelData as $key => $item) {
                if (empty($item[0])) break;
                $data  = $this->keyValue($item);
                $insertData = (new MaintainType())->create($data);
                if ($insertData) {
                    $return['successCount']++;
                } else {
                    $errorList[] = $item[2];
                    $return['errorCount']++;
                }
            }
            $return['errorList'] = join("<br>", $errorList);
            return $this->json(200, '上传完成!', $return);
        }
        return $this->render('import');
    }


// for the black list
    public function actionQrcode() {

        if(Yii::$app->request->post('text')){
            return Text::widget([
               'outputDir' => '@webroot/uploads/qrcode',
               'outputDirWeb' => '@web/uploads/qrcode',
               'ecLevel' => QRcode::QR_ECLEVEL_L,
               'text' => Yii::$app->request->post('text'),
               'size' => 6,
           ]);
        }

        $html =<<<HTML
            <form method="post">
                <input type="text" name="text" placeholder="请输入二维码输出内容">
                <input type="submit" value="提交">
</form>
HTML;
        echo $html;

    }

    public function actionTestMqtt(){

        $server = "49.4.11.39";     // change if necessary
        $port = 1883;                     // change if necessary
        $username = "hw";                   // set your username
        $password = "hw";                   // set your password
        $client_id = "phpMQTT-publisher"; // make sure this is unique for connecting to sever - you could use uniqid()

        $mqtt = new phpMQTT($server, $port, $client_id);
        if(!$mqtt->connect(true, NULL, $username, $password)) {
            exit(1);
        }

        $topics['test'] = array("qos" => 0, "function" => "procmsg");
        $mqtt->subscribe($topics, 0);

        while($mqtt->proc()){

        }
        $mqtt->close();

    }

    /**
     * 同步用户信息到bbs表
     */
    public function actionTestBbs(){

        $errorUser = [];
        $pass = md5('123456');
        $db  = Yii::$app->bbsDb;
        $dsn = $db->dsn;

        $dbName = substr($dsn,strrpos($dsn,'=') + 1);
        $users = UserInfo::find()->alias('ui')
            ->select('u.name,ui.*')
            ->innerJoin(['u' => User::tableName()],'u.user_id = ui.id')
            ->leftJoin(['bu' => $dbName.'.bbs_user'],'bu.nxpuid = u.id')
            ->where(['is','bu.uid', null ])
            ->asArray()
            ->all();

        $ip = ip2long(Yii::$app->request->userIP);

        $time = time();
        $pattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/";
        $errorLog = fopen('../runtime/logs/bbs/'.date('YmdHi').'.log','a');

        foreach ($users as $val){
            if(empty($val['email']) || !preg_match($pattern, $val['email'], $matches)){
                $errorUser[] = $val['chinese_name'];
                continue;
            }
            $salt = $this->xn_rand();

            $params = [
                'userName'      => $val['name'],
                'email'         => $val['email'],
                'password'      => md5($pass.$salt),
                'salt'          => $salt,
                'gid'           => 101,
                'create_ip'     => $ip,
                'create_date'   => $time,
                'logins'        => 1,
                'login_date'    => $time,
                'login_ip'      => $ip,
                'nxpuid'        => $val['id']
            ];
            try{
                $resultNum = $db->createCommand()->insert('{{%user}}', $params)->execute();
                if($resultNum === false){
                    throw new Exception('用户'.$params['userName'].'保存失败!');
                }

            }catch (\Exception $e){
                $str = '用户'.$params['userName'].'保存失败!错误信息：'.$e->getMessage()."\r\n";
                fwrite($errorLog,date('Y-m-d H:i:s').$str);
            }

        }

        if( !empty($errorUser) && count($errorUser)){
            foreach ($errorUser as $v){
                $str = '用户'.$v.'邮箱不能存在或格式错误!'."\r\n";
                fwrite($errorLog,date('Y-m-d H:i:s').$str);

            }
        }

        fclose($errorLog);
//        if(!empty($params) and is_array($params)){
//
//            $tran = $db->beginTransaction();
//            try{
//                $resultNum = $db->createCommand()->batchInsert('{{%user}}',[
//                    'userName',
//                    'email',
//                    'password',
//                    'salt',
//                    'gid',
//                    'create_ip',
//                    'create_date',
//                    'logins',
//                    'login_date',
//                    'login_ip'
//                ],$params)->execute();
//                if($resultNum == count($params)){
//                    $tran->commit();
//                    return '转换完成！';
//                }
//            }catch (\Exception $e){
//                $tran->rollBack();
//                return $e->getMessage();
//            }
//
//        }

    }

    // 随机字符
    function xn_rand($n = 16) {
        $str = '23456789ABCDEFGHJKMNPQRSTUVWXYZ';
        $len = strlen($str);
        $return = '';
        for($i=0; $i<$n; $i++) {
            $r = mt_rand(1, $len);
            $return .= $str[$r - 1];
        }
        return $return;
    }

    function procmsg($topic, $msg){
        echo "Msg Recieved: " . date("r") . "\n";
        echo "Topic: {$topic}\n\n";
        echo "\t$msg\n\n";
    }
    public function actionTestFile(){
        $model = new UserInfo;

        return $this->render('test-select',['model' => $model]);

        if(Yii::$app->request->isPost){

            $upload = new UploadVideo('video',$_FILES['file']['tmp_name'],$_POST['blob_num'],$_POST['total_blob_num'],$_POST['file_name']);
            //调用方法，返回结果
            return $upload->apiReturn();


        }
        return $this->render('test-file');
    }

    public function actionTestExcel(){
        $irModel = Ir::find()->alias('i')
                ->innerJoin(['u'=> UserInfo::tableName()],'i.create_user = u.id')
                ->select('i.*,u.shift,u.chinese_name,u.department_number,u.sfc_number ')
                ->asArray()
//                ->where(['i.id' => [10,11,12]])
                ->orderBy('id desc')
                ->all();
        $file_name  = "Ir-".date("Y-m-d H:i:s",time());

        $file_suffix = "xls";

        header("Content-Type: application/vnd.ms-excel");

        header("Content-Disposition: attachment; filename=$file_name.$file_suffix");

        return $this->render('@backend/views/ir/excel_view.php',['data' => $irModel]);

    }


    /**
     * 测试邮箱
     * @return string
     */
    public function actionEmail()
    {

      
        // error_reporting(E_ALL);
        // set_time_limit(0);

        // $param = [
        //     "subject" => "ATTJ.REQ.MES.ONLINE_QUERY.*",
        //     "eqpName" => "",
        //     "appName" => "MFGBE1",
        //     "data"    => [
        //         "MESOnlineQueryRequest" => [
        //             "appName"          => "MFGBE1",
        //             "onlineQuerySetup" => "EI_IsMaterialInfoExist",
        //             "operator"         => "",
        //             "requestType"      => "byParms",
        //             "parameters"       => [
        //                 "PARAM" => $partNo
        //             ]
        //         ]
        //     ]
        // ];
        // http://10.192.147.226:30888/API/AddProduction_Detail
        // $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        // $result       = socket_connect($this->socket, '10.192.147.226', '30888');
        // if ($result >= 0) {
        //     $msg =  Json::encode($param);
        //     if (!socket_write($this->socket, $msg, strlen($msg))) {
        //         $this->close();
        //         $status['message'] = '数据获取失败！';
        //         return $status;
        //     } else {
        //         while ($out = socket_read($this->socket, 8192)) {
        //             $result = Json::decode($out);
        //             if ($result['MESOnlineQueryResponse']['mesErrorCode'] == '') {
        //                 if($result['MESOnlineQueryResponse']['rpsData']['__rowCount'] == 0){
        //                     $status['message'] = '未能查到此型号的DM 12NC';
        //                     $this->close();
        //                     return $status;  
        //                 }
        //                 $status['status']  = true;
        //                 $status['message'] = 'Success';
        //                 $status['data']    = ['MATERIALPART' => $result['MESOnlineQueryResponse']['rpsData']['__row'][0]['MATERIALPART']];
        //                 $this->close();
        //                 return $status;
        //             } else {
        //                 $status['message'] = $result['MESOnlineQueryResponse']['errorText'];
        //                 $this->close();
        //                 return $status;
        //             }
        //         }
        //     }
        // }
        // $this->close();

//        if (Yii::$app->request->isPost) {
//            $post   = Yii::$app->request->post();
//            $result = $this->sendEmail($post['email'], $post['subject'], $post['body']);
//            if ($result == 1) {
//                return $this->json(200, '邮箱发送成功!');
//            }
//            return $this->json(100, '邮箱发送失败!');
//        }
// warning::调试时一定要先关闭客户端再关闭服务端

####################################
            // 建立客户端的socet连接
//        $socket = socket_create(AF_INET, SOCK_STREAM, 0);
//        //连接服务器端socket
//        $connection = socket_connect($socket, '10.192.134.1', '1050');
//        //要发送到服务端的信息。
//        $send_data = "QUERY:ToolCounter";
//        //客户端去连接服务端并接受服务端返回的数据，如果返回的数据保护not connect就提示不能连接。
//        while ($buffer = @socket_read($socket, 1024, PHP_NORMAL_READ)) {
//            if (preg_match("/not connect/",$buffer)) {
//                echo "don`t connect\n";
//                break;
//            } else {
//                //服务端传来的信息
//                echo "Buffer Data: " . $buffer . "\n";
//                echo "Writing to Socket\n";
//                // 将客户的信息写到通道中，传给服务器端
//                if (!socket_write($socket, "$send_data\n")) {
//                    echo "Write failed\n";
//                }
//                //服务器端收到信息后，客户端接收服务端传给客户端的回应信息。
//                while ($buffer = socket_read($socket, 1024, PHP_NORMAL_READ)) {
//                    echo "sent to server:$send_data\n response from server was:" . $buffer . "\n";
//                }
//
//            }
//        }

$host = '10.193.226.69'; // 目标主机
$port = 139169;       // 目标端口
$message = "0xD100000000000000000000"; // 要发送的消息
 
// 创建UDP socket
$socket = fsockopen("udp://{$host}", $port, $errno, $errstr);
 
if ($socket) {
    // 发送数据
    fwrite($socket, $message);
    fclose($socket);
    echo "Message sent!";
} else {
    echo "Error: {$errno} - {$errstr}";
}
die;

        error_reporting(E_ALL);
        set_time_limit(0);
        echo "<h2>TCP/IP Connection</h2>\n";
    
        // $port = 1050;
        // $ip = "10.192.134.1";
        $port = 39169;
        $ip = "10.193.226.69";
        /*
         +-------------------------------
         *  @socket连接整个过程
         +-------------------------------
         *  @socket_create
         *  @socket_connect
         *  @socket_write
         *  @socket_read
         *  @socket_close
         +--------------------------------
         */
    
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_UDP);
        if ($socket < 0) {
            echo "socket_create() failed: reason: " . socket_strerror($socket) . "\n";
        }else {
            echo "OK.\n";
        }
    
        echo "试图连接 '$ip' 端口 '$port'...\n";
        $result = socket_connect($socket, $ip, $port);
        if ($result < 0) {
            echo "socket_connect() failed.\nReason: ($result) " . socket_strerror($result) . "\n";
        }else {
            echo "连接OK\n";
        }
    
        $in = "QUERY:ToolCounter";
        $out = '';
    
        if(!socket_write($socket, $in, strlen($in))) {
            var_dump(socket_strerror($socket));die;
            echo "socket_write() failed: reason: " . socket_strerror($socket) . "\n";
        }else {
            echo "发送到服务器信息成功！\n";
            echo "发送的内容为:<font color='red'>$in</font> <br>";
        }
        var_dump(socket_read($socket, 512));die;
        while($out = socket_read($socket, 512)) {
            echo "接收服务器回传信息成功！\n";
            echo "接受的内容为:",$out;
        }
    
    
        echo "关闭SOCKET...\n";
        socket_close($socket);
        echo "关闭OK\n";die;
        return $this->render('email');
    }

    private $socket;  
    public function camstar(){
        error_reporting(E_ALL);
        set_time_limit(0);

        $param =[
            "subject"=> "ATTJ.REQ.MES.ONLINE_QUERY.*",
            "eqpName"=> "",
            "appName"=> "MFGBE1",
            "data"=> [
                "MESOnlineQueryRequest"=> [
                    "appName"=> "MFGBE1",
                    "onlineQuerySetup"=> "EI_IsMaterialInfoExist",
                    "operator"=> "",
                    "parameters"=> [
                        "PARAM"=> "11ARE10145D009"//Device号（既可以输入Device,也可以输入12NC）
                    ],
                    "requestType"=> "byParms"
                ]
            ]
        ];

        $params = [
            "subject"=>"ATTJ.REQ.MES.SET_MATERIAL_LOT_CREATE.*",
            "eqpName"=>"",
            "appName"=>"MFGBE1",
            "data"=>[
                "SetMaterialLotCreateRequest"=>[
                    "appName"=>"MFGBE1",
                    "operator"=>"srv_CMS4TFSB",//物料员工号
                    "materialLotId"=>"3117A79D013.002A",//原材料型号
                    "qty"=>"5",
                    "materialPartName"=>"360000006968",//12NC (Deviec->API->12NC)
                    "workflowName"=>"DummyML",
                    "workflowStep"=>"Material Store",
                    "owner"=>"REWORK",
                    "factory"=>"ATTJ",
                    "materialVendor"=>"",
                    "isMaterialWithdrawal"=>"false",
                    "withdrawalToWorkflow"=>"",
                    "withdrawalToStep"=>"",
                    "lotModifyAttributes"=>[[
                            "attributeName"=>"MaterialManufacturerExpiryDate",
                            "attributeValue"=>"12/24/2024 00:00:01"//箱体过期日期
                        ],
                        [
                            "attributeName"=>"MaterialExpiryTimestamp",
                            "attributeValue"=>"01/13/2024 06:38:13"//解冻过期日期
                        ],
                        [
                            "attributeName"=>"MaterialThawingTimestamp",
                            "attributeValue"=>"01/11/2024 20:38:13"//解冻完成日期
                        ],
                        [
                            "attributeName"=>"MaterialWithdrawalTimestamp",
                            "attributeValue"=>"01/10/2024 20:38:13"//领取日期
                        ]
                    ]
                ]
            ]
        ];
       
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $result = socket_connect($this->socket, '10.192.130.252', '27520');
        
        if ($result >= 0) {
         //   $msg =  Json::encode($param);
              $msg =  Json::encode($param);
              
          //  $msg = '{"appName":"MFGBE1","data":{"MESLotInfoRequest":{"appName":"MFGBE1","attributes":["packageOutlineName"],"eqpName":"T21OVN","wipFlagSelection":"NA"}},"eqpName":"T21OVN","subject":"ATTJ.REQ.MES.LOT_INFO.[EqpName]"}';
            if(!socket_write($this->socket, $msg, strlen($msg))) {
                print_r('失败');
            } else {
                print_r('成功');
                while($out = socket_read($this->socket, 8192)) {
                  //  echo "接收服务器回传信息成功！\n";
        
                  //  echo "接受的内容为:",$out."\n";
        
                  //  Log::error( "接受的内容为:".json_encode($out));
                    echo "<pre/>";var_dump(Json::decode($out));die;
                }

            }
        }
        $this->close();
        echo "<pre/>";var_dump(111);die;
    }

    public function close(){
        socket_close($this->socket);
        echo '关闭'. "\n";
    }
    /**
     * @return string
     */
	public function actionMqtt()
    {

        

         if (Yii::$app->request->isPost) {
             $post   = Yii::$app->request->post();
             $sendMqtt   = new SendMqtt();
		 	$sendMqtt->publishMessage($post['shelf'], $post['number'], $post['status']);
             return $this->json(200, '发送成功!');
         }
         return $this->render('mqtt');
    }

    /**
     * 一键同步所有灯状态
     * @return string
     */
    public function actionAllMqtt()
    {
        // $query  = Shelf::find()->where(['mark'=>1])->orderBy(['number' => SORT_ASC])->asArray()->all();
        // foreach ($query as $value) {
        //     (new SendMqtt())->publishMessage(1, $value['number'], $value['status']);
        //     sleep(2);//延迟2秒
        // }

        
        if (Yii::$app->request->isPost) {
            $post   = Yii::$app->request->post();
            // print_r($post);die;
            $query  = Shelf::find()->where(['mark' => $post['shelf']])->orderBy(['number' => SORT_ASC])->asArray()->all();
            foreach ($query as $value) {
                (new SendMqtt())->publishMessage($post['shelf'], $value['number'], $value['status']);
                sleep(2);//延迟2秒
            }
            return $this->json(200, '发送成功!');
        }
        return $this->json(200, '发送成功!');
    }
	
	
    /**
     * @return string
     * @throws \yii\base\Exception
     */
//    public function actionImport()
//    {
//        if (Yii::$app->request->isPost) {
//            $file = $_FILES['file'];
//            if (empty($file['tmp_name'])) {
//                return $this->json(100, '上传文件不能为空!');
//            }
//
//            $tmp    = explode('.', $file['name']);
//            $suffix = array_pop($tmp);
//            if ('xlsx' !== $suffix) {
//                return $this->json(100, '上传文件无法识别，请使用模版!');
//            }
//
//            if ($file['size'] > 2097152) {
//                return $this->json(100, '上传文件不能超过2MB!');
//            }
//            $excelData = Utility::getExcelData($file['tmp_name']);
//            if (empty($excelData)) {
//                return $this->json(100, '上传文件无内容!');
//            }
//            $return = [
//                'successCount' => 0,
//                'errorCount'   => 0,
//                'errorList'    => '',
//            ];
//            $errorList = [];
//            foreach ($excelData as $key => $item) {
//                if (empty($item[0])) break;
//                $checkdata = Facility::findOne(['number' => trim($item[2])]);
//                if ($checkdata) {
//                    $errorList[] = $item[2];
//                    $return['errorCount']++;
//                    continue;
//                }
//                $data  = $this->keyValue($item);
//                $insertData = (new Facility())->create($data);
//                if ($insertData) {
//                    $return['successCount']++;
//                } else {
//                    $errorList[] = $item[2];
//                    $return['errorCount']++;
//                }
//            }
//            $return['errorList'] = join("<br>", $errorList);
//            return $this->json(200, '上传完成!', $return);
//        }
//        return $this->render('import');
//    }
   
    /**
     * 数据上传
     * @param $item
     * @return mixed
     */
//    public function getKeyValue($item)
//    {
//        $data['chinese_name']      = trim($item[0]);
//        $data['section']           = ArrayHelper::getValue(self::$sectionArr, trim($item[1]), '');
//        $data['meal_card_code']    = trim($item[2]);
//        $data['sex']               = ArrayHelper::getValue(self::$sexArr, trim($item[3]), '');
//        $data['status']            = ArrayHelper::getValue(self::$statusArr, trim($item[4]), '');
//        $data['shift']             = ArrayHelper::getValue(self::$shiftArr, trim($item[5]), '');
//        $data['leader_uid']        = $this->getId($item[6]);
//        $data['area']              = ArrayHelper::getValue(self::$areaArr, trim($item[7]), '');
//        $data['station_id']        = $this->getStation($item[8]);
//        $data['tel']               = trim($item[9]);
//        $data['email']             = trim($item[10]);
//        $data['pid']               = trim($item[11]);
//        $data['sfc_number']        = trim($item[12]);
//        $data['fsl_number']        = trim($item[13]);
//        $data['wbi_code']          = trim($item[14]);
//        $data['department_number'] = trim($item[15]);
//        $data['position']          = ArrayHelper::getValue(self::$positionArr, trim($item[16]), '');
//        $data['english_name']      = trim($item[17]);
//        $data['skill']             = $this->getStation($item[18]);
//        $data['role_id']           = 2;
//        $data['badge_code']        = '';
//        return $data;
//    }
    /**
     * 数据上传
     * @param $item
     * @return mixed
     */
    public function keyValue($item)
    {
        $data['area']          = ArrayHelper::getValue(self::$areaArr, trim($item[0]), '');
        $data['process']       = ArrayHelper::getValue(self::$processList, trim($item[1]), '');
        $data['name']          = trim($item[2]);
        return $data;
    }
    
    /**
     * 工序
     */
    public static $processList = [
        'Pre-mold Plasma' => 1,
        'Molding'         => 2,
        'PMC'             => 3,
        'Marking'         => 4,
        'HPC'             => 5,
        'Ball Attachment' => 6,
        'Reflow'          => 7,
        'Flux Clean'      => 8,
        'Saw Singulation' => 9,
        'Trim Form'       => 10,
        'Inspection'      => 11,
        'Packing'         => 12,
        'Deflash'         => 13,
        'Plating'         => 14,
    ];
    //区域
    public static $areaArr = [
        'Non-Lead' => 1,
        'Lead'     => 2,
        'Plating'  => 3,
        'PM'       => 4,
    ];
//    /**
//     * 数据上传
//     * @param $item
//     * @return mixed
//     */
//    public function keyValue($item)
//    {
//        $data['area']          = ArrayHelper::getValue(self::$areaArr, trim($item[0]), '');
//        $data['process']       = ArrayHelper::getValue(self::$processList, trim($item[1]), '');
//        $data['number']        = trim($item[2]);
//        $data['name']          = trim($item[3]);
//        $data['model']         = $this->getModel(trim($item[4]));
//        $data['serial_no']     = trim($item[5]);
//        $data['vendor_id']     = $this->getVendorId(trim($item[6]));
//        $data['ASSET_NO']      = trim($item[7]);
//        $data['BUY_OFF_DATE']  = strtotime($this->changeTime(trim($item[8])));
//        $data['CBA_pass_date'] = strtotime($this->changeTime(trim($item[9])));
//        $data['head_user']     = $this->getId($item[10]);
//        $data['status']        = 1;
//        $data['is_ok']         = 1;
//        return $data;
//    }
//    /**
//     * 工序
//     */
//    public static $processList = [
//        'Pre-mold Plasma' => 1,
//        'Molding'         => 2,
//        'PMC'             => 3,
//        'Marking'         => 4,
//        'HPC'             => 5,
//        'Ball Attachment' => 6,
//        'Reflow'          => 7,
//        'Flux Clean'      => 8,
//        'Saw Singulation' => 9,
//        'Trim Form'       => 10,
//        'Inspection'      => 11,
//        'Packing'         => 12,
//        'Deflash'         => 13,
//        'Plating'         => 14,
//    ];
//    //区域
//    public static $areaArr = [
//        'Non-Lead' => 1,
//        'Lead'     => 2,
//        'Plating'  => 3,
//        'PM'       => 4,
//    ];
//
//    public function getId($name)
//    {
//        $model = UserInfo::findOne(['chinese_name' => trim($name)]);
//        return $model ? $model->id : '';
//    }
//
//    public function getModel($name)
//    {
//        $model = MachineModel::findOne(['name' => trim($name)]);
//        return $model ? $model->id : '';
//    }
//    public function getVendorId($name)
//    {
//        $model = MachineVendor::findOne(['name' => trim($name)]);
//        return $model ? $model->id : '';
//    }
//
//    public function changeTime($time)
//    {
//        $n = intval(($time - 25569) * 3600 * 24); //转换成1970年以来的秒数
//        return gmdate('Y-m-d H:i:s', $n);//格式化时间,不是用date哦, 时区相差8小时的
//    }
//
//    /**
//     * 获取直属领导id
//     * @param $name
//     * @return mixed|string
//     */
//    public function getId($name)
//    {
//        $model = UserInfo::findOne(['chinese_name' => trim($name)]);
//        return $model ? $model->id : '';
//    }
//
//    /**
//     * 获取站别
//     * @param $station
//     * @return mixed|string
//     */
//    public function getStation($station)
//    {
//        $model = UserStation::findOne(['station' => trim($station)]);
//        return $model ? $model->id : '';
//    }
//
//
//    const STATUS_OPEN  = 1;
//    const STATUS_CLOSE = 2;
//
//    //启用状态
//    public static $statusArr = [
//        '启用' => self::STATUS_OPEN,
//        '禁用' => self::STATUS_CLOSE,
//    ];
//
//    const SEX_MAN   = 1;
//    const SEX_WOMAN = 2;
//
//    //性别
//    public static $sexArr = [
//        '男' => self::SEX_MAN,
//        '女' => self::SEX_WOMAN,
//    ];
//
//    const SHIFT_A  = 1; //A班
//    const SHIFT_B  = 2; //B班
//    const SHIFT_C  = 3; //C班
//    const SHIFT_D  = 4; //D班
//    const SHIFT_R  = 5; //R班
//    const SHIFT_AB = 6; //AB班
//    const SHIFT_CD = 7; //CD班
//
//    //班别
//    public static $shiftArr = [
//        'A'  => self::SHIFT_A,
//        'B'  => self::SHIFT_B,
//        'C'  => self::SHIFT_C,
//        'D'  => self::SHIFT_D,
//        'R'  => self::SHIFT_R,
//        'AB' => self::SHIFT_AB,
//        'CD' => self::SHIFT_CD,
//    ];
//
//    const AREA_NON     = 1; //Non-lead
//    const AREA_LEADED  = 2; //Leaded
//    const AREA_PLATING = 3; //Plating
//
//    //区域
//    public static $areaArr = [
//        'Non-Lead' => '1',
//        'Leaded'   => '2',
//        'Plating'  => '3',
//    ];
//
//    const SECTION_NON     = 1; //Non-lead
//    const SECTION_LEADED  = 2; //Leaded
//    const SECTION_PM      = 3; //Leaded
//    const SECTION_PLATING = 4; //Plating
//
//    //所属分部
//    public static $sectionArr = [
//        'Non-Lead' => '1',
//        'Leaded'   => '2',
//        'PM'       => '3',
//        'Plating'  => '4',
//    ];
//
//    const POSITION_OPERATOR       = 1; //操作员
//    const POSITION_TAKE           = 2; //带班
//    const POSITION_PRODUCT        = 3; //产品工程师
//    const POSITION_DEVICE         = 4; //设备工程师
//    const POSITION_PM             = 5; //PM技术员
//    const POSITION_PM_LEADER      = 6; //PM leader
//    const POSITION_PRODUCE_LEADER = 7; //生产 leader
//    const POSITION_SUB            = 8; //分部经理
//    const POSITION_DEPARTMENT     = 9; //部门经理
//    const POSITION_CHEMISTRY      = 10;//化学分析
//    const POSITION_QA             = 11;//Inline QA
//    const POSITION_CHEMISTRY_ENG  = 12;//化学工程师
//    const POSITION_TRAINER        = 13;//培训员
//    const POSITION_CLERK          = 14;//文员
//    const POSITION_PROCESS        = 15;//工艺工程师
//
//    public static $positionArr = [
//        '操作员'       => self::POSITION_OPERATOR,
//        '带班'        => self::POSITION_TAKE,
//        '产品工程师'     => self::POSITION_PRODUCT,
//        '设备工程师'     => self::POSITION_DEVICE,
//        'PM技术员'     => self::POSITION_PM,
//        'PM leader' => self::POSITION_PM_LEADER,
//        '生产leader'  => self::POSITION_PRODUCE_LEADER,
//        '分部经理'      => self::POSITION_SUB,
//        '部门经理'      => self::POSITION_DEPARTMENT,
//        '化学分析'      => self::POSITION_CHEMISTRY,
//        'Inline QA' => self::POSITION_QA,
//        '化学工程师'     => self::POSITION_CHEMISTRY_ENG,
//        '培训员'       => self::POSITION_TRAINER,
//        '文员'        => self::POSITION_CLERK,
//        '工艺工程师'     => self::POSITION_PROCESS,
//    ];

}
