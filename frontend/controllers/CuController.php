<?php
namespace frontend\controllers;

use backend\models\ChargingAbnormal;
use backend\models\ChargingLog;
use backend\models\ChargingTank;
use backend\models\ChargingUserInfo;
use backend\models\ChargingWarehouse;
use backend\models\UserInfo;
use common\models\GlobalFun;
use yii\helpers\ArrayHelper;
use yii\db\Exception;
use Yii;

class CuController extends FrontendBaseController{


    public $email_title = 'Charging_Box Regularly reminded';
    public $emailView = '@backend/views/new-gemba/email.php';

    /**
     * 充电仓登录
     */
    public function login($params){


//        $params = $this->validateRequest();

        $model = new ChargingUserInfo();
        //设置验证规则
        $model->setScenario($model::SCEN_LOGIN);
        //写入参数
        $model->setAttributes($model->replaceKey($params));

        //验证提交信息
        if(!$model->validate()){
            return $this->outputJson('false',GlobalFun::errorToString($model->getErrors()));
        }

        //拿到用户信息
        $userInfo = ChargingUserInfo::findOne(['badge_code' => $params['wbiId']]);

         //用户不存在
         if(empty($userInfo)){
            $userInfoTemp = UserTempCard::findOne(['number' => $params['wbiId']]);
            if (empty($userInfoTemp)){
                return $this->outputJson('false','用户不存在') ;
            }
            else{
                $userInfo = ChargingUserInfo::findOne(['id' => $userInfoTemp->user_id]);
                if (empty($userInfo))
                    return $this->outputJson('false','用户不存在') ;
                else
                    return $this->byUserReturnResult($userInfo,$params['tankId'],2);

            }
        }else{
            return $this->byUserReturnResult($userInfo,$params['tankId'],1);

        }
        
        //用户不存在
        // if(empty($userInfo)){
        //     return $this->outputJson('false','用户不存在') ;
        // }

        // return $this->byUserReturnResult($userInfo,$params['tankId']);

    }

    /**
     * 根据身份返回数据
     * @param $userInfo
     */
    public function byUserReturnResult(UserInfo $userInfo,$tank_id,$type){
        $tank = ChargingTank::findOne($tank_id);

        if(!$tank){
            return $this->outputJson('false','充电柜不存在');
        }

        //        type : 1、本人卡；2、临时卡
        if (1){

            if($userInfo->id == 152 
            || $userInfo->id == 808 
            ||$userInfo->id == 57 
            ||$userInfo->id == 1027  
            ||$userInfo->id == 280 
            ||$userInfo->id == 40 
            ||$userInfo->id == 208
            ||$userInfo->id == 42){
                $roleName = 'admin';
                $role = 4;
            }else{
                if($userInfo->role_id <> 1){
                    // if($userInfo->role_id <> 1 && $userInfo->role_id <> 4){
                        $roleName = 'op';
                        $role = 2;
                    }
                    else{
                        $role = ChargingUserInfo::$role_equal[$userInfo->role_id];
                        $roleName = ChargingUserInfo::$role_name[$role];

                    }
            }
            

            $result = [
                'tankId' => $tank->id,
                'userId' => $userInfo->id,
                'wbiId'  => $userInfo->badge_code,
                "name"   => $userInfo->chinese_name,
                'role'   => $roleName,
                'shift'  => $userInfo->shift
            ];

            //保存登录日志
            ChargingLog::saveNewBlog($tank->number,'','',$userInfo->badge_code,ChargingLog::TYPE_LOGIN,'登录成功');

            //用户是操作员
            if($role === ChargingUserInfo::OPERATOR_ROLE){
                //判断操作员状态
        //                $model = new ChargingWarehouse();
        //                $data = $model->byUserStatusResult($userInfo->id,$tank->id);
        //                $result = array_merge($data,$result);

                return $this->outputJson('true','请求成功',$result);

            }else{
                //用户不是操作员，直接返回信息
                return $this->outputJson('true','请求成功',$result);
            }
        }

        if ($userInfo->shift == 5 || $userInfo->shift == 6 || $userInfo->shift == 7 ){

            $result = [
                'tankId' => $tank->id,
                'userId' => $userInfo->id,
                'wbiId'  => $userInfo->badge_code,
                "name"   => $userInfo->chinese_name,
                'role'   => "op",
                'shift'  => true
            ];

            //保存登录日志
            ChargingLog::saveNewBlog($tank->number,'','',$userInfo->badge_code,ChargingLog::TYPE_LOGIN,'登录成功');
            return $this->outputJson('true','请求成功',$result);

        }


        //用户角色是否可操作性
        if( !empty(ChargingUserInfo::$role_equal[$userInfo->role_id])){

            $role = ChargingUserInfo::$role_equal[$userInfo->role_id];

            $result = [
                'tankId' => $tank->id,
                'userId' => $userInfo->id,
                'wbiId'  => $userInfo->badge_code,
                "name"   => $userInfo->chinese_name,
                "shift"  => $userInfo->shift,
                'role'   => ChargingUserInfo::$role_name[$role],
            ];

            //保存登录日志
//            ChargingLog::saveNewBlog($tank->number,'','',$userInfo->badge_code,ChargingLog::TYPE_LOGIN,'登录成功');

            return $this->outputJson('true','请求成功',$result);

        }else{

//            ChargingLog::saveNewBlog('','','',$userInfo->badge_code,ChargingLog::TYPE_LOGIN,'登录失败！没有权限');

            return $this->outputJson('false','没有权限');
        }
    }

    /*
     * damon：通过 用户信息 和 选择的设备类型，分配设备
     */
    public function GetEquipment($params){

//        $params = $this->validateRequest();

        $model = new ChargingWarehouse();

        $data = $this->byUserStypeResult($params['userId'],$params['stype'],$params['tankId']);

        if (!$data)
            return $this->outputJson('false','无可领取设备');

        $result = array_merge($data);

        if ($data['isEqu'] == 0){
//            $model = new ChargingLog();
//            $model->setAttributes([
//                'user_id' => $params['userId'],
//                'wbi_code' => (new UserInfo())->getUserPid($params['userId']),
//                'type' =>  2, //1：登录；2：借；3：还；4：换电池；5：异常
//                'tank_number' => '1',
//                'warehouse_number' => $data['boxId'],
//                'equipment_number' => $data['boxId'],
//                'equipment_stype' => $params['stype'],//1：PDA；2：SURFACE
//                'comment' => '领取设备'
//            ]);
//            $model->save();

        }

        return $this->outputJson('true',$result);

    }

    
    /*
     * damon: 通过 用户信息 和 选择的设备类型
     */
    public function byUserStypeResult($user_id,$stype,$tankId){
        $warehouse = ChargingWarehouse::findOne(['user_id' => $user_id,'tank_id' => $tankId,'stype' => $stype,'status' => ChargingWarehouse::STATUS_YES]);

        if(!empty($warehouse)){
            //有设备
            $result['isEqu'] = 1;
            $result['boxId'] = $warehouse->equipment_number;

        } else {
            //无设备
            $temp = $this->getStypeAvailable($tankId, $stype);

            if ($temp){
                $result['isEqu'] = 0;
                $result['boxId'] = $temp->number;

                // 保存数据
                $model = ChargingWarehouse::findOne(['number' => $temp->number,'tank_id' => $tankId]);

                $model->setAttributes([
                    'user_id' => $user_id,
                    'type' => ChargingWarehouse::TYPE_USE
                ]);
                if ($model->save()) {
                    return $result;
                } else {
                    return false;
                }
            }

            return false;

        }
        return $result;

    }

    
    /**
     * damon: 区分设备类型查询
     * 查找充电时间最长使用次数相对低的设备
     * @return array|bool|\yii\db\ActiveRecord|null
     */
    public function getStypeAvailable($tank_id,$stype){

        $model = ChargingWarehouse::find()->select('number,equipment_number')
                    ->where(['status' => ChargingWarehouse::STATUS_YES,'tank_id' => $tank_id,'stype'=>$stype])
                    ->andWhere(['user_id' => null])
                    ->andWhere(['type' => ChargingWarehouse::TYPE_CHARGING])
                    ->orderBy([
                        'start_time' => SORT_ASC,
                        'charging_num' => SORT_ASC
                    ])
                    ->limit(1)
                    ->one();

        if(!empty($model)){
            return $model;
        }else{
            return false;
        }
    }

    /*
     * damon: 保存使用记录
     */
    public function SaveLog($params){

//        $params = $this->validateRequest();

        $model = new ChargingLog();
        $processing = '';

        if ($params['type'] == 5){
            $processing = '1';

            $model_warehouse = ChargingWarehouse::findOne(['number'=>$params['number'],'tank_id'=>$params['tank_number']]);
            if (!$model_warehouse)
                return  $this->outputJson('false','$model_warehouse-5-异常');

            $model_warehouse->type = 3;

            if (!$model_warehouse->save(false))
                return $this->outputJson('false','数据异常');

        }
        elseif ($params['type'] == 3){

            $model_warehouse = ChargingWarehouse::findOne(['number'=>$params['number'],'tank_id'=>$params['tank_number']]);
            if (!$model_warehouse)
                return  $this->outputJson('false','$model_warehouse');

            $startTime = date('Y-m-d H:i:s');
            $endTime = date('Y-m-d H:i:s',strtotime($startTime.' +3 hour'));

            $model_warehouse->user_id       = '';
            $model_warehouse->type          = 1;
            $model_warehouse->start_time    = $startTime;
            $model_warehouse->end_time      = $endTime;

            if (!$model_warehouse->save(false))
                return $this->outputJson('false','$model_warehouse-save数据异常');
        }


        $model->setAttributes([
            'user_id' => $params['userId'],
            'wbi_code' => $params['wbi_code'],
            'type' =>  $params['type'], //1：登录；2：借；3：还；4：换电池；5：异常
            'tank_number' => $params['tank_number'],
            'warehouse_number' => $params['number'],
            'equipment_number' => $params['number'],
            'processing' => $processing,
            'equipment_stype' => $params['equipment_stype'],//1：PDA；2：SURFACE
            'comment' => $params['comment']
        ]);
        if($model->save()){
            if($params['type']==5){
                $modelAbn = new ChargingAbnormal();
                $modelAbn->setAttributes([
                    'operation_user_id'=>$params['userId'],
                    'warehouse_id'=>$params['number'],
                    'tank_id'=>$params['tank_number'],
                    'type'=>2,
                    'status'=>2,
                    'abnormal_msg'=>$params['comment'],
                    'created_at'=>time()

                ]);
                $modelAbn->save();
            }
            return $this->outputJson('true');
        }else{
            throw new Exception($model->errorToString());
        }


    }

    /**
     *  申请交接
     */
    public function actionApplyHandover(){
        $params = $this->validateRequest();
        if(empty($params['wbiId'])){

            return $this->outputJson('false','缺少参数wbiId');
        }
        $userInfo = ChargingUserInfo::findOne(['badge_code' => $params['wbiId']]);

        if(empty($userInfo) || $userInfo->role_id !== $userInfo::SQUAD_LEADER){

            return $this->outputJson('false','请确认是否是带班');
        }
        //得到所有当班的充电柜
        $tank = ChargingTank::findAll(['shift' => $userInfo->shift]);

        if(empty($tank)){

            return $this->outputJson('false','非当班人员');
        }

        foreach ($tank as $value){

            $model = new ChargingWarehouse();
            //如果有一个充电仓异常，就禁止交接
            if(!$model->warehouseIsOk($value['id'])){
                return $this->outputJson('false','有未归还设备或未处理异常');
            }

        }
        return $this->outputJson('true','请求成功');
    }

    /**
     * 交班
     */
    public function actionHandover(){
        $params = $this->validateRequest();
        $base = ['handWbiId','pickWbiId'];
        $this->checkParameter($params,$base);
        //拿到两个操作员的数据
        $userInfo = (new ChargingUserInfo())->getShiftUserInfo($params['handWbiId'],$params['pickWbiId']);

        //如果没有拿到交班与接班人的数据
        if(empty($userInfo) || count($userInfo) < 2){
            return $this->outputJson('false','请求失败!人员不匹配！');
        }
        $userInfo = ArrayHelper::index($userInfo,'badge_code');
        //交班人
        $handUser =  $userInfo[$params['handWbiId']];
        //接班人
        $pickUser =  $userInfo[$params['pickWbiId']];
//        //拿到当前值班对应充电仓
        $tank = ChargingTank::findOne(['shift' =>$handUser['shift']]);
        //没有查到说明交班人没当班
        if(empty($tank)){
            return $this->outputJson('false','非当班人员');
        }
        //如果相等  说明接班人不符合要求
        if($tank->shift == $pickUser['shift']){
            return $this->outputJson('false','同一班别无法交接班！');
        }

        $tank->setAttributes([
            'shift'     => $pickUser['shift'],
            'shift_user'=> $pickUser['id']
        ]);
        //修改当班信息
        if($tank->save()){
            return $this->outputJson('true','交接成功');
        }else{
            return $this->outputJson('false','服务器错误');
        }

    }

    /**
     * 统一入口
     */
    public function actionCharging(){

        $params = $this->validateRequest();

        if ($params['api'] == 'login'){
            return $this->login($params);

        }elseif ($params['api'] == 'get-equipment'){
            return $this->GetEquipment($params);

        }elseif ($params['api'] == 'save-log'){
            return $this->SaveLog($params);

        }elseif ($params['api'] == 'show-status'){
            return $this->ShowStatus($params);

        }else{
            return $this->outputJson('false','无对应接口');
        }

    }


    /**
     *  充电仓信息
     */
    public function ShowStatus($params){
//        $params = $this->validateRequest();
        if(empty($params['tankId'])){
            return $this->outputJson('false','tankId不能为空');
        }
        $warehouseList = ChargingWarehouse::find();
        $warehouseList->where(['tank_id' => $params['tankId']]);
        $warehouseList->andFilterWhere(['number' => $params['boxId']]);
        $warehouseList->asArray();
        $datatemp = $warehouseList->all();

//        if($params['boxId'] == 1){
//            $this->sendEmail();
//        }

        if($datatemp){
            $result = [];
            foreach ($datatemp as $key => $value) {

                //状态信息中文
                $result[$key]['statusText'] = !empty(ChargingWarehouse::$typeArr[$value['type']])?ChargingWarehouse::$typeArr[$value['type']]:'' ;
                $result[$key]['status'] = intval($value['status']);
                $result[$key]['enable'] = intval($value['status']);
                $result[$key]['type']   = intval($value['type']);
                $result[$key]['boxId']  = $value['number'];
                $result[$key]['stype']  = $value['stype'];
                switch ($value['type']){
                    //使用中
                    case ChargingWarehouse::TYPE_USE:
                        $userInfoModel = UserInfo::findOne(['id' => $value['user_id']]);
                        if(empty($userInfoModel)){
                            $result[$key]['userName'] ='数据异常' ;
                        }else{
                            $result[$key]['userName'] = $userInfoModel->chinese_name;
                            $result[$key]['shift'] = $userInfoModel->shift;
                        }

                        break;
                    //充电中
                    case ChargingWarehouse::TYPE_CHARGING:
                        $date = strtotime($value['end_time']) - time();
                        if($date > 0){

                            $result[$key]['endDate'] = round($date/60,0);
                        }else{

                            $result[$key]['type'] = ChargingWarehouse::TYPE_CAT_USE;
                            $result[$key]['statusText'] = ChargingWarehouse::$typeArr[ChargingWarehouse::TYPE_CAT_USE];

                        }

                        break;
                    //可使用
                    case ChargingWarehouse::TYPE_CAT_USE:
                        $result[$key]['type'] = ChargingWarehouse::TYPE_CAT_USE;
                        $result[$key]['statusText'] = ChargingWarehouse::$typeArr[ChargingWarehouse::TYPE_CAT_USE];

                        break;
                    //异常
                    case ChargingWarehouse::TYPE_ABNORMAL:
                        $abnormal = ChargingAbnormal::find()->select('deal_with_msg')->where(['tank_id' => $value['tank_id'],'warehouse_id' => $value['id'] ])->limit(1)->one();
                        if(!empty($abnormal)){

                            $result[$key]['deal_with_msg'] = $abnormal['deal_with_msg'];
                        }else{
                            $result[$key]['deal_with_msg'] = '未处理';
                        }
                        break;
                    default:
                        $result[$key]['statusText'] = '未知的充电仓状态！';
                        break;
                }

            }
            return $this->outputJson('true','查询成功',$result);
        }else{

            return $this->outputJson('false','充电仓不存在');
        }

    }

    /**
     * 发送邮件
     * @return bool
     */
    public function sendEmail(){


        $j=date("H:i");//获得当前小时和分钟的时间
        $h=strtotime($j);//获得当前小时和分钟的时间时间戳
        $startTime_day=strtotime('09:00');
        $endTime_day=strtotime('09:10');
        $startTime_night=strtotime('21:00');
        $endTime_night=strtotime('21:10');

        if (($h >= $startTime_day && $h < $endTime_day) || ($h >= $startTime_night && $h < $endTime_night)){

            try{

                //获取未归还领用人信息
                $query = ChargingWarehouse::find();
                $query->andWhere(['status'=>1]);
                $query->andWhere(['type'=>2]);
                $data = $query->asArray()->all();

                $mail = Yii::$app->mailer->compose();
                if (!empty($data)){

                    $dataProvider = [];
                    $i = 0;
                    foreach ($data as $key=>$value){

                        //判断两个小时内有没有记录，如果有的话，就不发邮件
                        $query_temp = ChargingLog::find();
                        $query_temp->andWhere(['warehouse_number'=>$value['number']]);
                        $query_temp->andWhere(['user_id'=>$value['user_id']]);
                        $query_temp->andWhere(['type'=>2]);
                        $query_temp->andWhere(['>','created_at', strtotime("-2hour")]);
                        $query_temp->orderBy('created_at desc');
                        $data_temp1 = $query_temp->asArray()->all();

                        if (empty($data_temp1)){

                            //如果两个小时内没有领用记录，就取两个小时外最后一条领用记录
                            $query_log = ChargingLog::find();
                            $query_log->andWhere(['warehouse_number'=>$value['number']]);
                            $query_log->andWhere(['user_id'=>$value['user_id']]);
                            $query_log->andWhere(['type'=>2]);
                            $query_log->andWhere(['<','created_at', strtotime("-2hour")]);
                            $query_log->andWhere(['>','created_at', strtotime("-14hour")]);
                            $query_log->orderBy('created_at desc');
                            $query_log->limit(1);
                            $data_temp = $query_log->asArray()->one();


                            if (!empty($data_temp)){

                                $dataProvider[$i]['user_name'] = (new UserInfo())->getUserName($data_temp['user_id']);
                                $dataProvider[$i]['user_pid'] = (new UserInfo())->getUserPid($data_temp['user_id']);
                                $dataProvider[$i]['type'] = ArrayHelper::getValue(ChargingWarehouse::$stypeArr, $value['stype'],'');
                                $dataProvider[$i]['number'] = $value['number'];
                                $dataProvider[$i]['time'] = date('Y-m-d H:i:s',$data_temp['created_at']);
                                $i++;

                            }
                        }

                    }
                    if ($i == 0){
                        $mail->setHtmlBody('今日无未归还设备');

                    }else{
                        $html = $this->renderFile('@common/mail/charging/time-out-charging.php',['dataProvider' => $dataProvider]);
                        $mail->setHtmlBody($html);
                    }
                }else{
                    $mail->setHtmlBody('今日无未归还设备');
                }

                $mail->setTo('yong.yu@nxp.com'); //要发送给那个人的邮箱
//                $mail->setTo(['zhx703@163.com','yong.yu@nxp.com']); //要发送给那个人的邮箱
//                $mail->setTo(['zhx703@163.com','xin.zhang@mcdkj.cn']); //要发送给那个人的邮箱
                $mail->setSubject('充电柜未归还异常提醒'); //邮件主题

                if($mail->send()){
                    return true;
                }else{
                    echo '发送失败！';
                    return false;
                }

            }catch (\Exception $e){
                echo $e->getMessage();
                return false;
            }
        }

    }

    /**
     * 发送邮件
     * @return bool
     */
    public function actionSendEmail(){


        $j=date("H:i");//获得当前小时和分钟的时间
        $h=strtotime($j);//获得当前小时和分钟的时间时间戳
        $startTime_day=strtotime('09:00');
        $endTime_day=strtotime('09:05');
        $startTime_night=strtotime('21:00');
        $endTime_night=strtotime('21:05');

        if (($h >= $startTime_day && $h < $endTime_day) || ($h >= $startTime_night && $h < $endTime_night)){

            try{

                //获取未归还领用人信息
                $query = ChargingWarehouse::find();
                $query->andWhere(['status'=>1]);
                $query->andWhere(['type'=>2]);
                $data = $query->asArray()->all();

                $mail = Yii::$app->mailer->compose();
                if (!empty($data)){
					
					$dataProvider = [];
                    $i = 0;
                    foreach ($data as $key=>$value){

                        //判断两个小时内有没有记录，如果有的话，就不发邮件
                        $query_temp = ChargingLog::find();
                        $query_temp->andWhere(['warehouse_number'=>$value['number']]);
                        $query_temp->andWhere(['user_id'=>$value['user_id']]);
                        $query_temp->andWhere(['type'=>2]);
                        $query_temp->andWhere(['>','created_at', strtotime("-2hour")]);
                        $query_temp->orderBy('created_at desc');
                        $data_temp1 = $query_temp->asArray()->all();

                        if (empty($data_temp1)){

                            //如果两个小时内没有领用记录，就取两个小时外最后一条领用记录
                            $query_log = ChargingLog::find();
                            $query_log->andWhere(['warehouse_number'=>$value['number']]);
                            $query_log->andWhere(['user_id'=>$value['user_id']]);
                            $query_log->andWhere(['type'=>2]);
                            $query_log->andWhere(['<','created_at', strtotime("-2hour")]);
                            $query_log->andWhere(['>','created_at', strtotime("-14hour")]);
                            $query_log->orderBy('created_at desc');
                            $query_log->limit(1);
                            $data_temp = $query_log->asArray()->one();


                            if (!empty($data_temp)){

                                $userInfoOne = UserInfo::findOne($data_temp['user_id']);
                                if($userInfoOne){
                                    $dataProvider[$i]['user_name'] = $userInfoOne->chinese_name;
                                    $dataProvider[$i]['user_pid'] = $userInfoOne->pid;
                                    $dataProvider[$i]['shift'] = ArrayHelper::getValue(UserInfo::$shiftArr, $userInfoOne->shift,'');
                                    $dataProvider[$i]['area'] = ArrayHelper::getValue(UserInfo::$areaArr, $userInfoOne->area,'');
                                }else{
                                    $dataProvider[$i]['user_name'] = '';
                                    $dataProvider[$i]['user_pid'] = '';
                                    $dataProvider[$i]['shift'] = '';
                                    $dataProvider[$i]['area'] = '';
                                }

                                // $dataProvider[$i]['user_name'] = (new UserInfo())->getUserName($data_temp['user_id']);
                                // $dataProvider[$i]['user_pid'] = (new UserInfo())->getUserPid($data_temp['user_id']);
                                $dataProvider[$i]['type'] = ArrayHelper::getValue(ChargingWarehouse::$stypeArr, $value['stype'],'');
                                $dataProvider[$i]['tank'] = $value['tank_id'];
                                $dataProvider[$i]['number'] = $value['number'];
                                $dataProvider[$i]['time'] = date('Y-m-d H:i:s',$data_temp['created_at']);
                                $i++;

                            }
                        }

                    }
                    if ($i == 0){
                        $mail->setHtmlBody('今日无未归还设备');

                    }else{
                        $html = $this->renderFile('@common/mail/charging/time-out-charging.php',['dataProvider' => $dataProvider]);
                        $mail->setHtmlBody($html);
                    }
                }else{
                    $mail->setHtmlBody('今日无未归还设备');
                }

                // $mail->setTo(['yong.yu@nxp.com','fred.han@nxp.com','guoli.wang@nxp.com','P.Wang@nxp.com','hongliang.ji@nxp.com','shaolei.wu@nxp.com']); //要发送给那个人的邮箱
               $mail->setTo([
                // 'jianguo.zhao@nxp.com',
                'datagroup.nonlead@nxp.com',
                'zhibin.sun@nxp.com',
                'youpeng.che@nxp.com',
                'K.Li@nxp.com',
                'Y.Zhao@nxp.com',
                'hong.wu@nxp.com',
                'quansheng.ge@nxp.com',
                'gang.zhao@nxp.com',
                'lei.ma_2@nxp.com',
                'qi.li_2@nxp.com',
                'baofeng.liu@nxp.com'
            ]); //要发送给那个人的邮箱
//                $mail->setTo(['zhx703@163.com','xin.zhang@mcdkj.cn']); //要发送给那个人的邮箱
                $mail->setSubject('充电柜未归还异常提醒'); //邮件主题

                if($mail->send()){
                    return true;
                }else{
                    echo '发送失败！';
                    return false;
                }

            }catch (\Exception $e){
                echo $e->getMessage();
                return false;
            }
        }
    }

}