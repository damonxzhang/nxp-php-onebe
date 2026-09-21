<?php
namespace backend\controllers;

use backend\models\ChargingTank;
use backend\models\User;
use backend\models\UserInfo;
use Yii;
use backend\models\ChargingWarehouse;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

class ChargeTankController extends BaseController{

    public function actionIndex(){

        $model = ChargingWarehouse::find()->andWhere(['tank_id'=>1])->all();
        $model2 = ChargingWarehouse::find()->andWhere(['tank_id'=>2])->all();
        $model3 = ChargingWarehouse::find()->andWhere(['tank_id'=>3])->all();

//        $dataProvider = new ActiveDataProvider([
//            'query' => $model
//        ]);
//        print_r($dataProvider);
//        die;
        return $this->render('index',[
//            'data' => $dataProvider
            'model3' => $model3,
            'model2' => $model2,
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

    /*
     * 修改充电柜状态
     */
    public function actionUpdate(){
        if (Yii::$app->request->isPost) {
            $post  = Yii::$app->request->post();
            $model = ChargingWarehouse::findOne($post['id']);
            if (!$model) {
                return $this->json(100, '修改失败!');
            }
            $model ->stype  = $post['stype'];
            $model ->status = $post['status'];
            if (!$model->save(false)) {
                return $this->json(100, '修改失败!');
            }
            return $this->json(200, '修改成功!');
        }
        $id     = Yii::$app->request->get('id');
        $model = ChargingWarehouse::find()->where(['id' => $id])->asArray()->one();
        return $this->render('update',[
            'model' => $model
        ]);
    }

    /**
     * 修改当前班别
     * @return string
     */
    public function actionUpdateShift(){

        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            if(empty($post['user_id'])){
                return $this->json(100,'请选择带班和充电柜！');
            }

            $user = UserInfo::findOne($post['user_id']);
            if(!$user){
                return $this->json(100,'用户不存在！');
            }
            $tank = ChargingTank::find();
            $tank->andWhere(['status'=>1]);
            $tankData = $tank->asArray()->all();
            foreach($tankData as $value){
                $query = ChargingTank::findOne(['id'=>$value['id']]);
                $query->setAttributes([
                    'shift_user' => $user->id,
                    'shift' => $user->shift
                ]);
                $query->save();
            }
            // if($tank->save()){
                return $this->json(200,'保存成功！');
            // }
            // return $this->json(100,'保存失败！请稍后重试！');
        }
        $tanks = ChargingTank::find()->select('id,number')->all();
        $users = UserInfo::find()->where(['role_id' => 4])->select('id,chinese_name')->all();
        return $this->render('update-shift',[
            'tanks' => $tanks,
            'users' => $users
        ]);
    }

}