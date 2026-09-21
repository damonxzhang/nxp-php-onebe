<?php
namespace frontend\controllers;

use backend\models\ChargingAbnormal;
use backend\models\ChargingLog;
use backend\models\ChargingTank;
use backend\models\ChargingUserInfo;
use backend\models\ChargingWarehouse;
use backend\models\UserInfo;
use backend\models\UserTempCard;
use common\models\GlobalFun;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

class CuiController extends FrontendBaseController{



    /**
     * 统一入口
     */
    public function actionCharging(){

        $params = $this->validateRequest();

        if ($params['api'] == 'login'){
            return $this->actionLogin($params);

        }elseif ($params['api'] == 'get-machine-status'){
            return $this->actionGetMachineStatus($params);

        }elseif ($params['api'] == 'apply-handover'){
            return $this->actionApplyHandover($params);

        }elseif ($params['api'] == 'handover'){
            return $this->actionHandover($params);

        }elseif ($params['api'] == 'take'){
            return $this->actionTake($params);

        }elseif ($params['api'] == 'end-take'){
            return $this->actionEndTake($params);

        }elseif ($params['api'] == 'return'){
            return $this->actionReturn($params);

        }elseif ($params['api'] == 'end-return'){
            return $this->actionEndReturn($params);

        }elseif ($params['api'] == 'abnormal'){
            return $this->actionAbnormal($params);

        }elseif ($params['api'] == 'show-status'){
            return $this->actionShowStatus($params);

        }elseif ($params['api'] == 'change-status'){
            return $this->actionChangeStatus($params);

        }else{
            return $this->outputJson('false','无对应接口');
        }

    }


    /**
     *  变更状态 5->1
     */
    public function actionChangeStatus($params){
        if(empty($params['boxId'])){
            return $this->outputJson('false','缺少必填参数boxId');
        }
        if(empty($params['tankId'])){
            return $this->outputJson('false','缺少必填参数boxId');
        }
        $warehouse = ChargingWarehouse::findOne(['tank_id' => $params['tankId'], 'number' => $params['boxId']]);
        if(!$warehouse){
            throw new Exception('充电柜或充电仓编号不存在');
        }

        $warehouse->type      = 1;
        $warehouse->user_id      = null;

        //保存异常数据
        if ($warehouse->save()) {

        }else{
            throw new Exception($warehouse->errorToString());
        }

        return $this->outputJson('true','请求成功！',[]);

    }


    /**
     *  充电仓信息
     */
    public function actionShowStatus($params){
        if(empty($params['tankId']) || empty($params['boxId'])){
            return $this->outputJson('false','tankId or boxId 不能为空');
        }
        $warehouseList = ChargingWarehouse::find();
        $warehouseList->where(['tank_id' => $params['tankId']]);
        $warehouseList->andFilterWhere(['number' => $params['boxId']]);
        $warehouseList->asArray();
        $data = $warehouseList->all();
        if($data){
            $result = [];
            $types = array_keys(ChargingWarehouse::$typeArr);
            foreach ($data as $key => $value) {


                //获取当前班别
                $shift = ChargingTank::findOne(['id'=>1]);
                switch ($shift->shift){
                    case 1:
                        $result[$key]['shift']  = "A";
                        break;
                    case 2:
                        $result[$key]['shift']  = "B";
                        break;
                    case 3:
                        $result[$key]['shift']  = "C";
                        break;
                    case 4:
                        $result[$key]['shift']  = "D";
                        break;
                    default:
                        $result[$key]['shift']  = "";
                        break;

                }
                //状态信息中文
//                $result[$key]['statusText'] = ArrayHelper::getValue(ChargingWarehouse::$typeArr, $value['type'] , '');
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
                    //异常变为充电中
                    case ChargingWarehouse::TYPE_CHANGE:
                        $date = strtotime($value['end_time']) - time();
                        if($date > 0){

                            $result[$key]['endDate'] = round($date/60,0);
                        }else{

                            $result[$key]['type'] = ChargingWarehouse::TYPE_CHANGE;
                            $result[$key]['statusText'] = ChargingWarehouse::$typeArr[ChargingWarehouse::TYPE_CHANGE];

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
     *  保存异常信息
     */
    public function actionAbnormal($params){


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
//                $warehouse->user_id = null;
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
     * 查询能使用的充电仓
     */
    public function actionTake($params){

        $userInfo = $this->getUserInfo( $params['wbiId'] );
        if(empty($userInfo)){

            return $this->outputJson('false','用户不存在');
        }
        $model = new ChargingWarehouse();
        $model->setScenario($model::GET_TANK_RETURN_BOXID);
        $model->setAttributes($model->replaceKey($params));
        if($model->validate()){
            $model->user_id = $userInfo->id;
            $model->stype = $params['stype'];

            //是否持有设备
            if(!$model->confirmUserStatusAll()){

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
    public function actionReturn($params){

        !empty($params['wbiId']) && $userInfo = $this->getUserInfo( $params['wbiId'] );
        if(empty($userInfo)){

            return $this->outputJson('false','用户不存在');
        }
        $model = new ChargingWarehouse();
        $model->setScenario($model::GET_TANK_RETURN_BOXID);
        $model->setAttributes($model->replaceKey($params));
        if($model->validate()){
            $model->user_id = $userInfo->id;
            $model->stype = $params['stype'];
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
    public function actionEndTake($params){

        !empty($params['wbiId']) && $userInfo = $this->getUserInfo( $params['wbiId'] );
        if(empty($userInfo)){

            return $this->outputJson('false','用户不存在');
        }
        $model = new ChargingWarehouse();
        $model->setScenario($model::TAKE_OR_ALSO_SCEN);
        $model->setAttributes(array_merge($model->replaceKey($params),['user_id' => $userInfo->id]));
        $model->number = $model->equipment_number;
        $model->stype = $params['stype'];
        //验证请求参数
        if($model->validate()){

//            判断班别
            if ($userInfo->shift <= 4){
                $tank = ChargingTank::isOnDuty($userInfo->shift,$model->tank_id);
                //是否当班
                if(!$tank){
                    ChargingLog::saveNewBlog($params['tankId'],$model->number,$model->equipment_number,$userInfo->badge_code,ChargingLog::TYPE_TAKE,'非当班人员');

                    return $this->outputJson('false','非当班人员');
                }
            }


            //用户是否持有设备
            if($model->confirmUserStatusAll()){
                ChargingLog::saveNewBlog($params['tankId'],$model->number,$model->equipment_number,$userInfo->badge_code,ChargingLog::TYPE_TAKE,'已持有设备');

                return $this->outputJson('false','已持有设备');
            }
            $res = $model->endTake($params['tankId']);
            //取设备
            if($res === true){
                ChargingLog::saveNewBlog($params['tankId'],$model->number,$model->equipment_number,$userInfo->badge_code,ChargingLog::TYPE_TAKE,'取设备成功');
                return $this->outputJson('true','保存成功');
            }else{

                ChargingLog::saveNewBlog($params['tankId'],$model->number,$model->equipment_number,$userInfo->badge_code,ChargingLog::TYPE_TAKE,'取设备失败');
                return $this->outputJson('false',$res);
            }

        }else{
            return $this->outputJson('false',GlobalFun::errorToString($model->getErrors()));
        }

    }

    /**
     * 还设备
     */
    public function actionEndReturn($params){

        !empty($params['wbiId']) && $userInfo = $this->getUserInfo( $params['wbiId'] );
        if(empty($userInfo)){

            return $this->outputJson('false','用户不存在');
        }
        $model = new ChargingWarehouse();
        $model->setScenario($model::TAKE_OR_ALSO_SCEN);
        $model->setAttributes(array_merge($model->replaceKey($params),['user_id' => $userInfo->id]));
        $model->number = $model->equipment_number;
        $model->stype = $params['stype'];

        //验证请求参数
        if($model->validate()){
            if ($userInfo->shift <= 4) {
                $tank = ChargingTank::isOnDuty($userInfo->shift,$model->tank_id);
                //是否当班
                if(!$tank){
                    return $this->outputJson('false','非当班人员');
                }
            }
            //用户是否持有设备
            if(!$model->confirmUserStatus()){

                return $this->outputJson('false','未持有设备');
            }

            $res = $model->endReturn();
            //还设备
            if($res === true){
                ChargingLog::saveNewBlog($params['tankId'],$model->number,$model->equipment_number,$userInfo->badge_code,ChargingLog::TYPE_ALSO,'还设备成功');
                return $this->outputJson('true','操作成功');
            }else{
                ChargingLog::saveNewBlog($params['tankId'],$model->number,$model->equipment_number,$userInfo->badge_code,ChargingLog::TYPE_ALSO,'还设备失败');

                return $this->outputJson('false',$res);
            }

        }else{

            return $this->outputJson('false',GlobalFun::errorToString($model->getErrors()));
        }
    }


    /**
     *  查询USER当前持有设备状况
     */
    public function actionGetMachineStatus($params){
//        $params = $this->validateRequest();
        if(empty($params['wbiId'])){
            return $this->outputJson('false','缺少必填参数wbiId');
        }
        $userInfo = $this->getUserInfo( $params['wbiId'] );
        if (!$userInfo){
            return $this->outputJson('false','没有该用户信息');

        }

        //判断该用户是否有异常
        $query = ChargingWarehouse::find();
        $query->andWhere(['user_id'=>$userInfo->id]);
        $query->andWhere(['type'=>3]);
        $check_query = $query->asArray()->all();

        if (!empty($check_query)){
            return $this->outputJson('true','请求成功！',[
                'status'     => "4"
            ]);
        }

        $warehouse = ChargingWarehouse::find();
        $warehouse->andWhere(['user_id'=>$userInfo->id]);
        if ($params['type'] == 2)
            $warehouse->andWhere(['tank_id'=>$params['tankId']]);
        $data = $warehouse->count();

        //1:未持有任何设备；2：只持有PDA；3：只持有SURAFCE；4：同时持有
        if(!$data){
            return $this->outputJson('true','请求成功！',[
                'status'     => "1"
            ]);
        }elseif ($data == 2){

            return $this->outputJson('true','请求成功！',[
                'status'     => "4"
            ]);
        }

        $warehouse = ChargingWarehouse::find();
        $warehouse->andWhere(['user_id'=>$userInfo->id]);
        if ($params['type'] == 2)
            $warehouse->andWhere(['tank_id'=>$params['tankId']]);
        $warehouse->andWhere(['stype'=>1]);
        $data_PDA = $warehouse->count();
        if(!$data_PDA){
            return $this->outputJson('true','请求成功！',[
                'status'     => "3"
            ]);
        }else{
            return $this->outputJson('true','请求成功！',[
                'status'     => "2"
            ]);
        }

    }

    /**
     * 充电仓登录
     */
    public function actionLogin($params){
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
    }

    /*
     * 根据用户选择的设备类型返回用户当前所持有的设备
     */
    public function actionResultByStype(){

        $params = $this->validateRequest();

        //获取该用户持有设备情况
        $query = ChargingWarehouse::find();
        $query->where(['user_id'=>$params['user_id']]);
        $query->andwhere(['stype'=>$params['stype']]);
        $query->asArray();

        $dataProvider = $query->all();

        $data = [];

        if (!empty($dataProvider)) {
            foreach ($dataProvider as $key => $value) {
                $data[$key]['user_id']              = $value['user_id'];
                $data[$key]['stype']                = $value['stype'];
                $data[$key]['number']               = $value['number'];
            }
        }

        return $this->outputJson('true','成功',$data);

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
        if ($type == 2){

            $role = ChargingUserInfo::$role_equal[$userInfo->role_id];

            $result = [
                'tankId' => $tank->id,
                'userId' => $userInfo->id,
                'wbiId'  => $userInfo->badge_code,
                "name"   => $userInfo->chinese_name,
                'role'   => ChargingUserInfo::$role_name[$role],
                'shift'  => true
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
                'role'   => ChargingUserInfo::$role_name[$role],
            ];

            //是否当班
            if($tank->shift == $userInfo->shift){

                $result['shift'] = true;
            }else{
                $result['shift'] = false;
            }

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
        }else{

            ChargingLog::saveNewBlog('','','',$userInfo->badge_code,ChargingLog::TYPE_LOGIN,'登录失败！没有权限');

            return $this->outputJson('false','没有权限');
        }
    }

    /**
     *  申请交接
     */
    public function actionApplyHandover($params){
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


        //判断当前是否有未归还和异常的仓位
        $query = ChargingWarehouse::find();
        $query->andWhere(['in','type',[2,3]]);
        $check_query = $query->asArray()->all();

        if (!empty($check_query)){
            return $this->outputJson('false','仍有未处理的仓位，请及时处理！');
        }


        return $this->outputJson('true','请求成功');
    }

    /**
     * 交班
     */
    public function actionHandover($params){
        $base = ['handWbiId','pickWbiId'];
        $this->checkParameter($params,$base);

        //拿到两个操作员的数据
        $userInfo = (new ChargingUserInfo())->getShiftUserInfo($params['handWbiId'],$params['pickWbiId']);

        //如果没有拿到交班与接班人的数据
        if(empty($userInfo) || count($userInfo) < 2){
            return $this->outputJson('false','请求失败!人员不匹配！');
        }

        foreach ($userInfo as $value){
            if ($value['role_id'] != 4)
                return $this->outputJson('false','请求失败!交接人员不是带班！');

        }

        $userInfo = ArrayHelper::index($userInfo,'badge_code');
        //交班人
        $handUser =  $userInfo[$params['handWbiId']];
        //接班人
        $pickUser =  $userInfo[$params['pickWbiId']];
//        //拿到当前值班对应充电仓
//        $tank = ChargingTank::findOne(['shift' =>$handUser['shift']]);
        $tank = ChargingTank::findAll(['shift' =>$handUser['shift']]);
        //没有查到说明交班人没当班
        if(empty($tank)){
            return $this->outputJson('false','非当班人员');
        }
        //如果相等  说明接班人不符合要求
        if($handUser['shift'] == $pickUser['shift']){
            return $this->outputJson('false','同一班别无法交接班！');
        }

        \Yii::$app->db->createCommand()->update(ChargingTank::tableName(), ['shift' => $pickUser['shift']], ['shift' =>$handUser['shift']])->execute();
//        $tank->shift     = $pickUser['shift'];
//        $tank->shift_user = $pickUser['id'];

        //修改当班信息
//        if($tank->save()){
            return $this->outputJson('true','交接成功');
//        }else{
//            return $this->outputJson('false','服务器错误');
//        }

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
}