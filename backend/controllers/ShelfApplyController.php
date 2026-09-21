<?php
namespace backend\controllers;

use backend\models\search\ShelfApplySearch;
use backend\models\Shelf;
use backend\models\ShelfApply;
use backend\models\ShelfMaterials;
use backend\models\UserInfo;
use common\models\SendEmail;
use common\models\SendMqtt;
use Yii;
use yii\data\Pagination;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

class ShelfApplyController extends BaseController
{
    public $enableCsrfValidation = false;

    /**
     * 列表展示
     * @return string
     */
    public function actionIndex()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new ShelfApplySearch();
        $data         = $searchModel->search($request);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);

        return $this->render('index', [
            'request'      => $request,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination
        ]);
    }

    /**
     * 领取物料
     * @return string
     */
    public function actionPromote()
    {
        if (Yii::$app->request->isPost) {
            //查询物料信息
            $post       = Yii::$app->request->post();
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
                $model = ShelfMaterials::findOne(['cont_no' => $contNo]);
                if(!$model){
                    return $this->json(100, '物料编号:' . $contId . '不存在!');
                }
            }
            $application = ShelfApply::findOne(['id' => $post['id']]);
            //循环更新物料表状态
            foreach ($contNoArr as $value) {
                $model               = ShelfMaterials::findOne(['cont_no' => substr(trim($value),2)]);
//                $model->user_id      = $application->user_id;
                $model->library_time = date('Y-m-d H:i:s', time());
                $model->fettle       = ShelfMaterials::FETTLE_DOWN; //物料为下架
                if (!$model->save()) {
                    return $this->json(100, '更新物料状态失败!');
                }
                 //检查同一位置是否还有别的物料
                $status = Shelf::STATUS_RECEIVE; //可领取
                $shelfId = $model->shelf_id;
                $query = ShelfMaterials::findOne(['shelf_id'=> $shelfId, 'fettle'  => ShelfMaterials::FETTLE_UP, 'status'  => ShelfMaterials::STATUS_PENDING]);
                //有别的物料并且状态为待领取
                $shelfModel  = Shelf::findOne(['id' => $shelfId]);
                if (!$query) {
                    $status  = Shelf::STATUS_FREE;//空闲
                    $shelfModel->status = $status;
                    $shelfModel->save();
                }
                (new SendMqtt())->publishMessage($shelfModel->mark, $shelfModel->number, $status);
                usleep(400000);
            }
            //更新申请表状态
            $application->status = ShelfApply::STATUS_RECEIVE;
            $application->save();
            return $this->json(200, '领取成功!');
        }
        $id      = Yii::$app->request->get('id');
        $application = ShelfApply::findOne(['id' => $id]);
        $dataProvider = (new ShelfMaterials())->getSelectedMaterials(['part_no' => $application->part_no,'apply_id' => $application->id,'amount' => $application->amount,'status' => ShelfMaterials::STATUS_USING]);
        return $this->render('promote', ['id' => $id, 'dataProvider' => $dataProvider]);
    }

    /**
     * 检查物料是否可领取
     * @return string
     */
    public function actionCheckMaterials()
    {
        $post  = Yii::$app->request->post();
        $query = ShelfApply::findOne(['id' => $post['id']]);
        // $idList = (new ShelfMaterials())->getMaterials(['part_no' => $query->part_no,'amount' => $query->amount,'status' => ShelfMaterials::STATUS_PENDING]);
        $idList = (new ShelfMaterials())->getMaterialByFloorTwo(['part_no' => $query->part_no,'amount' => $query->amount,'status' => ShelfMaterials::STATUS_PENDING]);
        if (empty($idList)) {
            return $this->json(100, '库存不足!!');
        }
        //循环发送消息
        foreach ($idList as $value) {
            //更新物料的状态为领取中
            $materialsModel = ShelfMaterials::findOne(['id' => $value['id']]);
            $materialsModel->status = ShelfMaterials::STATUS_USING;//领取中
            $materialsModel->user_id = $post['uid'];
            $materialsModel->apply_id = $query->id;
            $materialsModel->save();

            //修改货架状态
            $shelfModel         = Shelf::findOne(['id' => $value['shelf_id']]);
            $shelfModel->status = Shelf::SHELF_RECEIVED;
            $shelfModel->save();
            (new SendMqtt())->publishMessage($shelfModel->mark, $shelfModel->number, Shelf::SHELF_RECEIVED);
            usleep(400000);
        }
        return $this->json(200, '');
    }

    /**
     * 是否有未二次确认
     * @return string
     */
    public function actionTwoConfirm(){
        $data = ShelfMaterials::getNoTwoConfirm();
        if(!empty($data['id'])){
            //如果程序错误 代码继续执行
            $userEmails = (new UserInfo())->getApplyUserEmails();
            array_push($userEmails,['email' => 'gang.zhao@nxp.com']);
            $params = [
                'subject' => 'compound货架二次确认未完成',
                'body' => $data['chinese_name'].'领取'.$data['cont_no'].$data['shelf_id'].',未完成二次确认'];
            //发送邮件
            @(new SendEmail())->sendEmails($userEmails,$params);

            return $this->json(100,'有未确认数据',$data);

        }else{
            return $this->json(200,'没有未确认数据');
        }
    }

    /**
     * 未二次确认列表
     */
    public function actionTwoConfirmShow(){
        $ids = $this->getNoTowConfirmIds();
        $data = ShelfApply::findAll(['id' => $ids]);
        return $this->render('two-confirm-show',['data' => $data]);
    }

    /**
     * 获取未二次确认的申请id
     * @return array
     */
    public function getNoTowConfirmIds(){
        return ShelfMaterials::find()
            ->select('apply_id')
            ->where(['status' => ShelfMaterials::STATUS_USING,'fettle' => ShelfMaterials::FETTLE_UP])
            ->andWhere(['>','apply_id',0])
            ->groupBy('apply_id')
            ->column() ;
    }
    /**
     * 撤回物料
     * @return string
     */
    public function actionWithdrawMaterials()
    {
        $post  = Yii::$app->request->post();
        $query = ShelfApply::findOne(['id' => $post['id']]);
        if (!$query) {
            return $this->json(100, '申请不存在!');
        }
        $query->status = ShelfApply::STATUS_CANCEL;
        if (!$query->save()) {
            return $this->json(100, '撤回申请状态失败!');
        }
        return $this->json(200, '撤回成功!');
    }

    /**
     *  撤回二次确认的申请
     */
    public function actionCallBackConfirm(){

        $id = Yii::$app->request->post('id');
        $tra = Yii::$app->db->beginTransaction();
        try{
            $appModel = ShelfApply::findOne($id);
            if($appModel){
                //申请对应的物料
                $mModels = ShelfMaterials::findAll(['apply_id' => $appModel->id]);
                if(empty($mModels)){
                    throw new Exception('服务器错误');
                }
                foreach ($mModels as $value){
                    $value->status = ShelfMaterials::STATUS_PENDING;
                    $value->user_id = '';
                    $value->apply_id = '';
                    if(!$value->save()){
                        throw new Exception($value->errorToString());
                    }
                    // 检查是不是1楼
                    $shelf = Shelf::find()->where(['id' => $value->shelf_id])->one();
                    if($shelf){
                        if ( $shelf->floor == 1){
                            $shelf->status = Shelf::STATUS_RECEIVE;
                            $shelf->save();
                        }
                    }
                }
                $appModel->status = $appModel::STATUS_CANCEL;//撤销
                if(!$appModel->save()){
                    throw new Exception($appModel->errorToString());
                }
                $tra->commit();
                return $this->json(200,'保存成功！');

            }
            throw new Exception('服务器错误');
        }catch (\Exception $e){

            $tra->rollBack();
            return $this->json(100,$e->getMessage());
        }


    }
    /**
     *  修改申请为已领取
     */
    public function actionReceiveApply(){
        $id = Yii::$app->request->post('id');
        $tra = Yii::$app->db->beginTransaction();
        try{
            $appModel = ShelfApply::findOne($id);
            if($appModel){
                //申请对应的物料
                $mModels = ShelfMaterials::findAll(['apply_id' => $appModel->id]);

                if(empty($mModels)){
                    throw new Exception('服务器错误');
                }
                foreach ($mModels as $value){
                    $value->fettle = $value::FETTLE_DOWN;//下架
                    $value->library_time = date('Y-m-d H:i:s', time());

                    if(!$value->save()){
                        throw new Exception($value->errorToString());
                    }
                    
                    // 检查是不是1楼
                    // $shelf = Shelf::find()->where(['id' => $value->shelf_id])->one();
                    // if($shelf){
                    //     if ( $shelf->floor == 1){
                    //         $shelf->status = Shelf::STATUS_RECEIVE;
                    //         $shelf->save();
                    //     }
                    // }
                }
                $appModel->status = $appModel::STATUS_RECEIVE;//已领取
                if(!$appModel->save()){
                    throw new Exception($appModel->errorToString());
                }
                //检查同一位置是否还有别的物料
                $status = Shelf::STATUS_RECEIVE; //可领取
                $shelfId = $value->shelf_id;
                $query = ShelfMaterials::findOne(['shelf_id'=> $shelfId, 'fettle'  => ShelfMaterials::FETTLE_UP, 'status'  => ShelfMaterials::STATUS_PENDING]);
                //有别的物料并且状态为待领取
                $shelfModel  = Shelf::findOne(['id' => $shelfId]);
                if (!$query) {
                    $status  = Shelf::STATUS_FREE;//空闲
                    $shelfModel->status = $status;
                    $shelfModel->save();
                }
                (new SendMqtt())->publishMessage($shelfModel->mark, $shelfModel->number, $status);
                usleep(400000);
                $tra->commit();
                return $this->json(200,'保存成功！');

            }
            throw new Exception('服务器错误');
        }catch (\Exception $e){

            $tra->rollBack();
            return $this->json(100,$e->getMessage());
        }

    }

}
