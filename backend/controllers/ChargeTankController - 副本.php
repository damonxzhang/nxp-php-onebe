<?php
namespace backend\controllers;

use backend\models\ChargingTank;
use backend\models\User;
use backend\models\UserInfo;
use Yii;
use backend\models\ChargingWarehouse;
use yii\helpers\ArrayHelper;

class ChargeTankController extends BaseController{

    public function actionIndex(){

        $model = ChargingWarehouse::find()->all();
        return $this->render('index',[
            'model' => $model
        ]);
    }

    /**
     * 开启关闭充电仓
     * @return string
     */
    public function actionEdit(){
        $data = Yii::$app->request->post();
        $model = ChargingWarehouse::findOne($data['id']);
        if(!empty($model)){
            if($data['status'] == $model::STATUS_YES){
                $model->status = $model::STATUS_NO;
            }else{
                $model->status = $model::STATUS_YES;
            }
            if($model->save()){
                return $this->json(200,'修改成功！');
            }
        }
        return $this->json(100,'服务器异常！');
    }

    /**
     * 修改当前班别
     * @return string
     */
    public function actionUpdateShift(){

        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            if(empty($post['user_id']) || empty($post['tank_id'])){
                return $this->json(100,'请选择带班和充电柜！');
            }

            $user = UserInfo::findOne($post['user_id']);
            if(!$user){
                return $this->json(100,'用户不存在！');
            }
            $tank = ChargingTank::findOne($post['tank_id']);
            if(!$tank){
                return $this->json(100,'充电不存在！');
            }
            $tank->setAttributes([
                'shift_user' => $user->id,
                'shift' => $user->shift
            ]);
            if($tank->save()){
                return $this->json(200,'保存成功！');
            }
            return $this->json(100,'保存失败！请稍后重试！');
        }
        $tanks = ChargingTank::find()->select('id,number')->all();
        $users = UserInfo::find()->where(['role_id' => 4])->select('id,chinese_name')->all();
        return $this->render('update-shift',[
            'tanks' => $tanks,
            'users' => $users
        ]);
    }

}