<?php
namespace backend\controllers;

use backend\models\ChargingLog;
use backend\models\ChargingTank;
use backend\models\ChargingUserInfo;
use backend\models\ChargingWarehouse;
use Yii;
use backend\models\ChargingAbnormal;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

class ChargeAbnormalController extends BaseController{


    public function actionIndex(){

        $query = ChargingAbnormal::find();

        $pages = new Pagination(['totalCount' => $query->count()]);
        $models = $query->offset($pages->offset)
            ->limit($pages->limit)->orderBy('id desc')
            ->all();

        return $this->render('index',[
            'models' => $models,
            'pagination' => $pages
        ]);
    }

    /**
     * 创建异常
     * @return string
     */
    public function actionCreate(){

        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            if(empty($post['box_id']) || empty($post['tank_id'])){
                return $this->json(100,'数据错误!');
            }

            $tr = Yii::$app->db->beginTransaction();
            try{
                //充电仓是否存在
                $boxModel = ChargingWarehouse::findOne($post['box_id']);

                if(!$boxModel){
                    throw new Exception('错误的充电仓编号');
                }
                //保存异常信息
                $this->saveAbnormal($post,$boxModel->id,$boxModel->number);
                //修改充电仓状态为异常
                $boxModel->updateAnomalies($boxModel::TYPE_ABNORMAL);

                $tr->commit();
                return $this->json(200,'保存成功！');
            }catch (\Exception $e){
                $tr->rollBack();
                return $this->json(100,$e->getMessage());
            }
        }

        $tanks = ArrayHelper::map(ChargingTank::findAll(['status' =>ChargingTank::STATUS_YES]),'id','number');
        $warehouses = ArrayHelper::map((new ChargingWarehouse)->getNormalBoxInfo(),'id','number');
        return $this->render('create',[
            'tanks' => $tanks,
            'warehouses' => $warehouses,
        ]);
    }

    /**
     * 处理异常
     * @param $id
     * @return string
     * @throws Exception
     */
    public function actionHandle($id){
        $model = ChargingAbnormal::findOne($id);
        if(!$model){
            throw new Exception('错误的请求！');
        }
        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            if(empty($post['status'])){
                return $this->json(100,'错误请求！');
            }
            if(empty($post['comment'])){
                return $this->json(100,'处理信息不能为空！');
            }
            $model->status = $post['status'];
            $res = $this->setAndSaveAbnormal($model,$post);
            if($res === true){
                return $this->json(200,'保存成功!');
            }
            return $this->json(100,$res);
        }
        return $this->render('handle',['model' => $model]);
    }

    /**
     * 根据处理类型 保存数据
     * @param ChargingAbnormal $model
     * @param $post
     * @return bool|string
     */
    public function setAndSaveAbnormal(ChargingAbnormal $model,$post){
        $model->setScenario($model::MANUAL_SCEN);

        $user = Yii::$app->user->identity->userInfo;
        //处理 需要带班或管理员
        if($model->status == ChargingAbnormal::DEAL_STATUS && !empty(ChargingUserInfo::$handle_role[$user->role_id])){

            $model->setAttributes([
                'deal_with_user_id' => Yii::$app->user->identity->user_id,
                'deal_with_msg' => $post['comment'],
                'deal_with_time' => date('Y-m-d H:i:s')
            ]);
            if(!$model->save()){
                return $model->errorToString();
            }else{
                return true;
            }
            //完成 需要管理员
        }elseif($model->status == ChargingAbnormal::OK_STATUS && ChargingUserInfo::ADMIN_ROLE == ChargingUserInfo::$role_equal[$user->role_id]){

            $result = $this->completeAbnormal( $model,$post);
            if(true === $result){
               return true;
           }
            return $result;
        }else{
            return '没有权限！';
        }

    }

    /**
     * 完成异常处理 解除充电仓异常
     * @param ChargingAbnormal $model
     * @param $post
     * @return bool|string
     */
    public function completeAbnormal(ChargingAbnormal $model,$post){
        $tra = Yii::$app->db->beginTransaction();
        try{
            $model->setAttributes([
                'admin_user_id' => Yii::$app->user->identity->user_id,
                'complete_msg' => $post['comment'],
                'complete_time' => date('Y-m-d H:i:s')
            ]);
            //充电仓是否存在
            $boxModel = ChargingWarehouse::findOne($model['warehouse_id']);
            if(!$boxModel){
                throw new Exception('错误的充电仓编号');
            }
            //修改充电仓状态为正常
            $boxModel->updateAnomalies($boxModel::TYPE_CHARGING);
            if(!$model->save()){
                throw new Exception($model->errorToString());
            }
            $tra->commit();
            return true;
        }catch (Exception $e){
            $tra->rollBack();
            return $e->getMessage();
        }

    }
    /**
     * 保存异常信息
     * @param $post
     * @param $boxId
     * @param $boxNumber
     * @throws Exception
     */
    public function saveAbnormal($post,$boxId,$boxNumber){
        $model = new ChargingAbnormal();
        $model->setScenario($model::MANUAL_SCEN);
        $model->setAttributes([
            'operation_user_id' => $this->getUid(),
            'tank_id' => $post['tank_id'],
            'warehouse_id' => $boxId,
            'warehouse_number' => $boxNumber,
            'deal_with_msg' => $post['deal_with_msg'],
            'status' => $model::DEAL_STATUS,
            'type' => $model::MANUAL_TYPE,
            'deal_with_time' => date('Y-m-d H:i:s'),
            'create_time' => date('Y-m-d H:i:s')
        ]);
        //保存异常信息
        if(!$model->save()){
            throw  new Exception( $model->errorToString());
        }
    }

    /**
     * 领取记录
     * @return string
     */
    public function actionGetRecord(){

        $model = new ChargingLog();
        $query = $model->find()->orderBy('id desc');
        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
        $query->where(['type' => ChargingLog::TYPE_TAKE,]);
        $query->orWhere(['type' => ChargingLog::TYPE_ALSO]);
        return $this->render('get-record',[
            'data' => $dataProvider
        ]);

    }


}