<?php
namespace frontend\controllers;

use backend\models\ChargingAbnormal;
use backend\models\ChargingLog;
use backend\models\ChargingTank;
use backend\models\ChargingWarehouse;
use backend\models\UserInfo;
use common\models\GlobalFun;
use yii\db\Exception;

class ChargingController extends FrontendBaseController{

    /**
     * 查询能使用的充电仓
     */
    public function actionTake(){

        $params = $this->validateRequest();
        !empty($params['wbiId']) && $userInfo = $this->getUserInfo( $params['wbiId'] );
        if(empty($userInfo)){

            return $this->outputJson('false','用户不存在');
        }
        $model = new ChargingWarehouse();
        $model->setScenario($model::GET_TANK_RETURN_BOXID);
        $model->setAttributes($model->replaceKey($params));
        if($model->validate()){
            $model->user_id = $userInfo->id;
            //是否持有设备
            if(!$model->confirmUserStatus()){

                $boxModel = $model->getAvailable($model->tank_id);

                if($boxModel){

                     return $this->outputJson('true','操作成功',[
                        'boxId'     => $boxModel['number'],
                        'deviceId'  => $boxModel['equipment_number']
                    ]);
                }else{
                    return $this->outputJson('false','无可用设备');
                }

            }else{

                return $this->outputJson('false','已持有设备');
            }

        }else{
            return $this->outputJson('false',$model->errorToString());
        }
    }
    /**
     * 查询还设备的充电仓
     */
    public function actionReturn(){

        $params = $this->validateRequest();
        !empty($params['wbiId']) && $userInfo = $this->getUserInfo( $params['wbiId'] );
        if(empty($userInfo)){

            return $this->outputJson('false','用户不存在');
        }
        $model = new ChargingWarehouse();
        $model->setScenario($model::GET_TANK_RETURN_BOXID);
        $model->setAttributes($model->replaceKey($params));
        if($model->validate()){
            $model->user_id = $userInfo->id;
            //是否持有设备
            $boxModel = $model->confirmUserStatus();
            if($boxModel){

                return $this->outputJson('true','操作成功',[
                    'boxId'     => $boxModel['number'],
                    'deviceId'  => $boxModel['equipment_number']
                ]);

            }else{

                return $this->outputJson('false','未持有设备');
            }

        }else{
            return $this->outputJson('false',$model->errorToString());
        }
    }

    /**
     * 取设备
     */
    public function actionEndTake(){

        $params = $this->validateRequest();
        !empty($params['wbiId']) && $userInfo = $this->getUserInfo( $params['wbiId'] );
        if(empty($userInfo)){

            return $this->outputJson('false','用户不存在');
        }
        $model = new ChargingWarehouse();
        $model->setScenario($model::TAKE_OR_ALSO_SCEN);
        $model->setAttributes(array_merge($model->replaceKey($params),['user_id' => $userInfo->id]));
        $model->number = $model->equipment_number;
        //验证请求参数
        if($model->validate()){
            $tank = ChargingTank::isOnDuty($userInfo->shift,$model->tank_id);
            //是否当班
            if(!$tank){
                ChargingLog::saveNewBlog('',$model->number,$model->equipment_number,$userInfo->badge_code,ChargingLog::TYPE_TAKE,'非当班人员');

                return $this->outputJson('false','非当班人员');
            }
            //用户是否持有设备
            if($model->confirmUserStatus()){
                ChargingLog::saveNewBlog($tank->number,$model->number,$model->equipment_number,$userInfo->badge_code,ChargingLog::TYPE_TAKE,'已持有设备');

                return $this->outputJson('false','已持有设备');
            }
            $res = $model->endTake();
            //取设备
            if($res === true){
                ChargingLog::saveNewBlog($tank->number,$model->number,$model->equipment_number,$userInfo->badge_code,ChargingLog::TYPE_TAKE,'取设备成功');
                return $this->outputJson('true','保存成功');
            }else{

                ChargingLog::saveNewBlog($tank->number,$model->number,$model->equipment_number,$userInfo->badge_code,ChargingLog::TYPE_TAKE,'取设备失败');
                return $this->outputJson('false',$res);
            }

        }else{
            return $this->outputJson('false',GlobalFun::errorToString($model->getErrors()));
        }

    }

    /**
     * 还设备
     */
    public function actionEndReturn(){

        $params = $this->validateRequest();
        !empty($params['wbiId']) && $userInfo = $this->getUserInfo( $params['wbiId'] );
        if(empty($userInfo)){

            return $this->outputJson('false','用户不存在');
        }
        $model = new ChargingWarehouse();
        $model->setScenario($model::TAKE_OR_ALSO_SCEN);
        $model->setAttributes(array_merge($model->replaceKey($params),['user_id' => $userInfo->id]));
        $model->number = $model->equipment_number;
        //验证请求参数
        if($model->validate()){
            $tank = ChargingTank::isOnDuty($userInfo->shift,$model->tank_id);
            //是否当班
            if(!$tank){
                return $this->outputJson('false','非当班人员');
            }
            //用户是否持有设备
            if(!$model->confirmUserStatus()){

                return $this->outputJson('false','未持有设备');
            }

            $res = $model->endReturn();
            //还设备
            if($res === true){
                ChargingLog::saveNewBlog($tank->number,$model->number,$model->equipment_number,$userInfo->badge_code,ChargingLog::TYPE_ALSO,'还设备成功');
                return $this->outputJson('true','操作成功');
            }else{
                ChargingLog::saveNewBlog($tank->number,$model->number,$model->equipment_number,$userInfo->badge_code,ChargingLog::TYPE_ALSO,'还设备失败');

                return $this->outputJson('false',$res);
            }

        }else{

            return $this->outputJson('false',GlobalFun::errorToString($model->getErrors()));
        }
    }

    /**
     *  保存异常信息
     */
    public function actionAbnormal(){

        $params = $this->validateRequest();

        $model = new ChargingAbnormal();
        $model->setScenario($model::AUTO_SCEN);
        $model->setAttributes(array_merge( [
            'type'   => $model::AUTO_TYPE,//硬件
            'status' => $model::NOT_STATUS,//未处理
        ],$model->replaceKey($params) ));
        !empty($params['comment']) && $model->abnormal_msg = $params['comment'];
        if(!$model->validate()) {
            return $this->outputJson('false',$model->errorToString());
        }
        $userInfo = UserInfo::findOne(['badge_code' => $model->wbi_code]);
        if(empty($userInfo)){
            return $this->outputJson('false','用户不存在');
        }

        $tra = \Yii::$app->db->beginTransaction();

        try{
            $warehouse = ChargingWarehouse::findOne(['tank_id' => $model->tank_id, 'number' => $model->warehouse_number]);
            if(!$warehouse){
                throw new Exception('充电柜或充电仓编号不存在');
            }

            $model->create_time      = date('Y-m-d H:i:s');
            $model->warehouse_id     = $warehouse->id;
            $model->operation_user_id= $userInfo->id;

            //保存异常数据
            if ($model->save()) {

                $warehouse->type  = $warehouse::TYPE_ABNORMAL;
                $warehouse->user_id = null;
                //更新充电仓状态
                if($warehouse->save()){
                    ChargingLog::saveNewBlog( $warehouse->chargingTank->number,$warehouse->number,$warehouse->equipment_number,$userInfo->badge_code,ChargingLog::TYPE_ABNORMAL,'异常信息保存成功');

                    $tra->commit();

                    return $this->outputJson('true','保存成功');
                }else{

                    throw new Exception($model->errorToString());
                }

            }else{

                throw new Exception($model->errorToString());
            }
        }catch (\Exception $e){

            $tra->rollBack();

            ChargingLog::saveNewBlog( '','','',$model->wbi_code,ChargingLog::TYPE_ABNORMAL,'保存异常失败');

            return $this->outputJson('false',$e->getMessage());
        }

    }

    /**
     *  充电仓信息
     */
    public function actionShowStatus(){
        $params = $this->validateRequest();
        if(empty($params['tankId'])){
            return $this->outputJson('false','tankId不能为空');
        }
        $warehouseList = ChargingWarehouse::find()->where(['tank_id' => $params['tankId']])->asArray()->all();
        if($warehouseList){
            $result = [];
            $types = array_keys(ChargingWarehouse::$typeArr);
            foreach ($warehouseList as $key => $value) {


                //状态信息中文
                $result[$key]['statusText'] = !empty(ChargingWarehouse::$typeArr[$value['type']])?ChargingWarehouse::$typeArr[$value['type']]:'' ;
                $result[$key]['status'] = intval($value['status']);
                $result[$key]['type']   = intval($value['type']);
                $result[$key]['boxId']  = $value['number'];
                switch ($value['type']){
                    //使用中
                    case ChargingWarehouse::TYPE_USE:
                        $userInfoModel = UserInfo::findOne(['id' => $value['user_id']]);
                        if(empty($userInfoModel)){
                            $result[$key]['userName'] ='数据异常' ;
                        }else{
                            $result[$key]['userName'] = $userInfoModel->chinese_name;
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
     * 获取用户数据
     * @param $wbiCode
     * @return UserInfo|bool|null
     */
    public function getUserInfo($wbiCode){
        if(!empty($wbiCode)){
            return UserInfo::findOne(['badge_code' => $wbiCode]);
        }else{
            return false;
        }
    }


    /**
     *  异常信息接口
     */
    public function actionGetAbnormal(){
        $params = $this->validateRequest();
        if(empty($params['tankId'])){
           return $this->outputJson('false','缺少必填参数tankId');
        }
        $model =  (new ChargingAbnormal())->getAbnormalInfo($params['tankId']);
        if(!empty($model)){
            $result = array_map(function ($abnormal){
                return [
                    'boxId'      => $abnormal['boxId'],
                    'comment'    => $abnormal['deal_with_msg'] ?:'',
                    'create_time'=> $abnormal['create_time'],
                ];

            },$model);
            return $this->outputJson('true','请求成功！',$result);
        }else{
            return $this->outputJson('true','请求成功！',[]);
        }
    }

    /**
     *  查询USER当前持有设备状况
     */
    public function actionGetMachineStatus(){
        $params = $this->validateRequest();
        if(empty($params['wbiId'])){
           return $this->outputJson('false','缺少必填参数wbiId');
        }
        $userInfo = $this->getUserInfo( $params['wbiId'] );
        if (!$userInfo){
            return $this->outputJson('false','没有该用户信息');

        }
        $warehouse = ChargingWarehouse::find();
        $warehouse->where(['user_id'=>$userInfo->id]);
        $data = $warehouse->count();

        //0:未持有任何设备；1：只持有PDA；2：只持有SURAFCE；3：同时持有
        if(!$data){
            return $this->outputJson('true','请求成功！',[
                'status'     => "0"
            ]);
        }elseif ($data == 2){

            return $this->outputJson('true','请求成功！',[
                'status'     => "3"
            ]);
        }

        $warehouse = ChargingWarehouse::find();
        $warehouse->andWhere(['user_id'=>$userInfo->id]);
        $warehouse->andWhere(['stype'=>1]);
        $data_PDA = $warehouse->count();
        if(!$data_PDA){
            return $this->outputJson('true','请求成功！',[
                'status'     => "2"
            ]);
        }else{
            return $this->outputJson('true','请求成功！',[
                'status'     => "1"
            ]);
        }

    }

}