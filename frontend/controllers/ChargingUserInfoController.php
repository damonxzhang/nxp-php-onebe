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

class ChargingUserInfoController extends FrontendBaseController{


    /**
     * 充电仓登录
     */
    public function actionLogin(){
        $params = $this->validateRequest();

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
            return $this->outputJson('false','用户不存在') ;
        }

        return $this->byUserReturnResult($userInfo,$params['tankId']);

    }

    /**
     * 根据身份返回数据
     * @param $userInfo
     */
    public function byUserReturnResult(UserInfo $userInfo,$tank_id){
        $tank = ChargingTank::findOne($tank_id);

        if(!$tank){
            return $this->outputJson('false','充电柜不存在');
        }

        //        type : 1、本人卡；2、临时卡
        if (1){

            if($userInfo->id == 152 
            || $userInfo->id == 808 
            || $userInfo->id == 57 
            || $userInfo->id == 1027 
            || $userInfo->id == 280 
            || $userInfo->id == 40 
            || $userInfo->id == 208
            || $userInfo->id == 42){
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
                $model = new ChargingWarehouse();
                $data = $model->byUserStatusResult($userInfo->id,$tank->id);
                $result = array_merge($data,$result);

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
    public function actionApplyHandover(){

        return $this->outputJson('false','此功能已取消');

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

        // if(empty($tank)){

        //     return $this->outputJson('false','非当班人员');
        // }

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
        // if(empty($tank)){
        //     return $this->outputJson('false','非当班人员');
        // }
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

}