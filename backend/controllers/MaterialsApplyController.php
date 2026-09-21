<?php
namespace backend\controllers;

use backend\models\GoodsGrid;
use backend\models\GridApply;
use backend\models\GridMaterials;
use backend\models\search\GridMaterialsSearch;
use backend\models\search\MaterialsApplySearch;
use backend\models\service\MaterialsService;
use backend\models\UserInfo;
use common\models\SendEmail;
use common\models\SendMqtt;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

class MaterialsApplyController extends BaseController{

    /**
     * 申请列表
     * @return string
     */
    public function actionApplyList(){
        $searchModel = new MaterialsApplySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('apply-list',[
            'searchModel' => $searchModel,
            'dataProvider'=> $dataProvider
        ]);
    }

    /**
     * 创建申请
     * @return string
     */
    public function actionCreate(){
        $model = new GridApply();

        if($model->load(Yii::$app->request->post())){
            if($model->createApply()){
                return $this->json(200,'申请成功！');
            }else{
                return $this->json(100,$model->errorToString());
            }
        }
        return $this->render('create',[
            'model' => $model
        ]);
    }

    /**
     * 撤回申请
     * @param $id
     * @return string
     */
    public function actionWithdraw($id){
        $model = GridApply::findOne($id);
        if($model->receive()){
            return $this->json(200,'操作成功！');
        }
        return $this->json(100,'操作失败！');
    }

    /**
     * 查询物料是否满足条件 并修改领取状态
     * @param $id
     * @return string
     */
    public function actionAllotMaterials($id){
        $data = $this->twoConfirm();

        if(true !== $data ){
            return $this->redirect(array_merge(['get-materials'],$data));
        }

        $applyModel = GridApply::findOne($id);
        $gridModel = new MaterialsService();
        $modelList = $gridModel->getMaterials($applyModel->part_no,$applyModel->amount);

        if(empty($modelList) || count($modelList) != $applyModel->amount){
            return $this->json(100,'物料不足');
        }

        $tran = Yii::$app->db->beginTransaction();
        foreach ($modelList as $model){
            if(!$model->waitingToReceive($applyModel)){
                $tran->rollBack();
                return $this->json(100,'领取失败！');
            }
            if(Yii::$app->db->createCommand()->update(GoodsGrid::tableName(),['status' => GoodsGrid::SHELF_RECEIVED],'')->execute() === false){
                return $this->json(100,'保存失败！');
            }
        }
        if(!$applyModel->receive()){
            return $this->json(100,$applyModel->errorToString());
        }
        $tran->commit();
        return $this->json(200, '');
    }
    /**
     * 未二次确认列表
     */
    public function actionTwoConfirmShow(){
        $ids = $this->getNoTowConfirmIds();
        $data = GridApply::findAll(['id' => $ids]);
        return $this->render('two-confirm-show',['data' => $data]);
    }

    /**
     * 领取物料
     * @param $id
     * @return string
     */
    public function actionGetMaterials($id){
        $model = new GridMaterialsSearch();
        $dataProvider = $model->searchApplyMaterials($id);
        if(Yii::$app->request->isPost){
            $post       = Yii::$app->request->post();
            //查询物料信息
            $contNoArr  = $post['cont_no'];
            $constList  = ArrayHelper::getValue($post, 'const_list', []);
            if (empty($contNoArr) || empty($constList)) {
                return $this->json(100, '物料参数错误!');
            }
            //循环对比物料参数
            foreach ($contNoArr as $key => $contId) {
                $contNo = substr(trim($contId),2);
                if($contNo != $constList[$key]){
                    return $this->json(100, '物料编号:' . $constList[$key] . '不一致请核对物料批次!');
                }
                $model = GridMaterials::findOne(['cont_no' => $contNo]);
                if(!$model){
                    return $this->json(100, '物料编号:' . $contId . '不存在!');
                }
            }

            //循环更新物料表状态
            foreach ($contNoArr as $value) {
                $model  = GridMaterials::findOne(['cont_no' => substr(trim($value),2)]);
                if(empty($model) || !$model->alreadyGetMaterials()){
                    return $this->json(100,'物料领取失败！');
                }
                //检查同一位置是否还有别的物料
                $status = GoodsGrid::STATUS_RECEIVE; //可领取
                $shelfId = $model->grid_id;
                $query = GridMaterials::findOne(['grid_id'=> $shelfId, 'fettle'  => GridMaterials::FETTLE_UP, 'status'  => GridMaterials::STATUS_PENDING]);
                //有别的物料并且状态为待领取
                $shelfModel  = GoodsGrid::findOne(['id' => $shelfId]);
                if (!$query) {
                    $status  = GoodsGrid::STATUS_FREE;//空闲
                    $shelfModel->status = $status;
                    $shelfModel->save();
                }
//                (new SendMqtt())->publishMessage($shelfModel->mark, $shelfModel->number, $status);
//                usleep(400000);
            }

            return $this->json(200, '领取成功!');
        }

        return $this->render('get-materials',[
            'dataProvider' => $dataProvider
        ]);
    }


    /**
     * 未二次确认列表
     * @return string
     */
    public function actionTwoConfirmList(){
        $model = new MaterialsApplySearch();
        $dataProvider = $model->searchTwoConfirm();

        return $this->render('two-confirm-list',[
            'model' => $model,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * 完成未确认领取
     * @param $id
     * @return string
     * @throws \yii\db\Exception
     */
    public function actionComplete($id){
        $gridModel = GridApply::findOne($id);
        if(!empty($gridModel)){
            //查询申请的物料
            $materials = GridMaterials::findAll(['apply_id' => $gridModel->id]);
            if(empty($materials)){
                return $this->json(100,'操作异常！');
            }
            foreach ($materials as $k => $model){
                //状态改为领取
                if($model->alreadyGetMaterials()){
                    //检查储物格是否存在物料
                    if($model->gridExistsMaterials($model->grid_id)){

                        $status = GoodsGrid::STATUS_RECEIVE;
                    }else{

                        $status = GoodsGrid::STATUS_FREE;
                    }
                    //更新货架状态
                    if(false === GoodsGrid::updateStatusById($model->grid_id,$status)){

                        return $this->json(100,'状态更新失败！');
                    }
                }else{
                    return $this->json(100,'保存失败！');
                }
            }

            return $this->json(200,'操作成功！');

        }
        return $this->json(100,'数据错误！');
    }

    /**
     * 撤回申请
     * @param $id
     * @return string
     * @throws \yii\db\Exception
     */
    public function actionCallback($id){
        $model = GridApply::findOne($id);
        if(!empty($model)){
            //查询申请的物料
            $materials = GridMaterials::findAll(['apply_id' => $model->id]);
            if(empty($materials)){
                return $this->json(100,'操作异常！');
            }
            foreach ($materials as $k => $mod){
                //状态改为待领取
                if($mod->callbackApply()){
                    //检查储物格是否存在物料
                    if($mod->gridExistsMaterials($mod->grid_id)){

                        $status = GoodsGrid::STATUS_RECEIVE;
                    }else{

                        $status = GoodsGrid::STATUS_FREE;
                    }
                    //更新货架状态
                    if(false === GoodsGrid::updateStatusById($mod->grid_id,$status)){
                        return $this->json(100,'状态更新失败！');
                    }
                }else{
                    return $this->json(100,'保存失败！');
                }
            }
            if(!$model->cancel()){
                return $this->json(100,'保存失败！');
            }
            return $this->json(200,'操作成功！');

        }
        return $this->json(100,'数据错误！');
    }

    /**
     * 获取未二次确认的申请id
     * @return array
     */
    public function getNoTowConfirmIds(){
        return GridMaterials::find()
            ->select('apply_id')
            ->where(['status' => GridMaterials::STATUS_USING,'fettle' => GridMaterials::FETTLE_UP])
            ->andWhere(['>','apply_id',0])
            ->groupBy('apply_id')
            ->column() ;
    }
    /**
     * 是否有未二次确认
     * @return string
     */
    public function twoConfirm(){
        $data = GridApply::getNoTwoConfirm();
        if(!empty($data['id'])){
            $userEmails = (new UserInfo())->getApplyUserEmails();
            array_push($userEmails,['email' => 'gang.zhao@nxp.com']);
            $params = [
                'subject' => 'compound货架二次确认未完成',
                'body' => $data['chinese_name'].'领取'.$data['cont_no'].$data['shelf_id'].',未完成二次确认'];
            //发送邮件
            @(new SendEmail())->sendEmails($userEmails,$params);

            return $data;

        }else{
            return true;
        }
    }

}