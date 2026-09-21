<?php

namespace backend\controllers;

use common\models\SendMqtt;
use Yii;
use backend\models\search\ShelfMaterialsSearch;
use backend\models\Shelf;
use backend\models\ShelfMaterials;
use backend\models\UserInfo;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class ShelfMaterialsController extends BaseController
{
    /**
     * 物料列表
     * @return string
     */
    public function actionIndex()
    {
        $request             = Yii::$app->request->queryParams;
        $request['entry_id'] = $this->getUid();
        $request['status']   = ShelfMaterials::STATUS_INFO;
        $data                = $this->searchData($request);
        return $this->render('index', $data);
    }

    /**
     * 查询数据
     * @param $request
     * @return array
     */
    public function searchData($request)
    {
        $searchModel  = new ShelfMaterialsSearch();
        $data         = $searchModel->search($request);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);
        return [
            'request'      => $request,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination
        ];
    }

    /**
     * 添加数据
     * @return string
     * @throws \yii\db\Exception
     */
    public function actionCreate()
    {
        //检查货架是否存在
        $post       = Yii::$app->request->post();
        $shelfModel = Shelf::findOne(['shelf_number' => trim($post['shelf_number'])]);
        if (!$shelfModel) {
            return $this->json(100, '货架编号不存在!');
        }
        $transaction     = Yii::$app->db->beginTransaction();
        //检查物料是否存在
        $contNo = substr(trim($post['cont_no']),2);
		$model = ShelfMaterials::findOne(['cont_no' => $contNo]);
		if (!$model) {
            return $this->json(100, '物料不存在!');
        }
        // $discard = ['11ASA10190D011','11ASA10190D013','11ARE10138D007','11ARE10145D008','11ARE10145D012'];//在此part_no里面的不能和货架绑定
		// if (in_array($model->part_no,$discard)){
        //     return $this->json(100, '物料不能和货架绑定!');
        // }

        $uncard1 = ['11ASA10154D002','11ARL10103D003','11ASA00268D001'];
        if ($shelfModel->mark == 1){
            if (!in_array($model->part_no,$uncard1)){
                return $this->json(100, '物料不能和【一号】货架绑定!');
            }
        }
        

        // $uncard2 = ['11ARL10109C001','11ARL10109C005'];
        
        // $uncard2 = ['11ASA10154D002','11ARL10103D003','11ASA00268D001'];
        // if ($shelfModel->mark == 2){
        //     if (in_array($model->part_no,$uncard2)){
        //         return $this->json(100, '物料不能和【二号】货架绑定!');
        //     }
        // }



        //保存物料货架信息
        $model->entry_id = $this->getUid();
        $model->shelf_id = $shelfModel->id;
        $model->status   = ShelfMaterials::STATUS_INFO;//入货架
        $model->update_time = date('Y-m-d H:i:s', time());//更新时间
        if (!$model->save()) {
            $transaction->rollBack();
            return $this->json(100, '保存物料货架信息失败!');
        }

        //给下位机发消息
        if ($shelfModel->id){
            $sendMessage = $this->sendMqttData($shelfModel->id, Shelf::STATUS_PROGRESS);
            if (!$sendMessage) {
                $transaction->rollBack();
                return $this->json(100, '给下位机发消息失败!!');
            }
        }
        $transaction->commit();
        return $this->json(200, '添加成功!');
    }

    /**
     * 保存物料信息
     * @return string
     * @throws \yii\db\Exception
     */
    public function actionSave()
    {
        $transaction   = Yii::$app->db->beginTransaction();
        $id            = Yii::$app->request->get('id');
        $model         = ShelfMaterials::findOne(['id' => $id]);
        $model->status = ShelfMaterials::STATUS_PENDING;//待领取
        $model->fettle = ShelfMaterials::FETTLE_UP;//上架
        $model->update_time = date('Y-m-d H:i:s', time());//更新时间
        if (!$model->save()) {
            $transaction->rollBack();
            return $this->json(100, '保存失败!');
        }
        //修改货架状态
        $sendMessage = $this->sendMqttData($model->shelf_id, Shelf::STATUS_PROGRESS);
        if (!$sendMessage) {
            $transaction->rollBack();
            return $this->json(100, '保存失败!!');
        }
        $userId = $this->getUid();
        $transaction->commit();
        return $this->json(200, '保存成功!', $userId);
    }

    /**
     * 一键保存
     * @return string
     */
    public function actionSaveAll()
    {
        $request = Yii::$app->request->queryParams;
        $idList  = ArrayHelper::getValue($request, 'idlist', []);
        if (!empty($idList)) {
            foreach ($idList as $id){
                $model         = ShelfMaterials::findOne(['id' => $id]);
                $model->status = ShelfMaterials::STATUS_PENDING;//待领取
                $model->fettle = ShelfMaterials::FETTLE_UP;//上架
                $model->update_time = date('Y-m-d H:i:s', time());//更新时间
                if (!$model->save()) {
                    return $this->json(100, '保存失败!');
                }
                //修改货架状态
                $sendMessage = $this->sendMqttData($model->shelf_id, Shelf::STATUS_PROGRESS);
                if (!$sendMessage) {
                    return $this->json(100, '保存失败!!');
                }
            }
        }
        $userId = $this->getUid();
        return $this->json(200, '一键保存成功!', $userId);
    }

    /**
     * 撤回物料
     * @return string
     * @throws \yii\db\Exception
     */
    public function actionWithdraw()
    {
        $transaction     = Yii::$app->db->beginTransaction();
        $id              = Yii::$app->request->get('id');
        $model           = ShelfMaterials::findOne($id);
        //检查是否还有别的物料
        $param = ['id' => $id, 'shelf_id' => $model->shelf_id, 'status' => ShelfMaterials::STATUS_INFO];
        $query = (new ShelfMaterials()) ->checkMaterials( $param);
        
        //不存在别的物料给货架发指令
        if (!$query) {
            $sendMessage = $this->sendMqttData($model->shelf_id, Shelf::STATUS_FREE);
            if (!$sendMessage) {
                $transaction->rollBack();
                return $this->json(100, '撤回失败!!');
            }
        }
        $model->entry_id = '';
        $model->shelf_id = '';
        $model->status   = ShelfMaterials::STATUS_WAIT;
        if (!$model->save()) {
            $transaction->rollBack();
            return $this->json(100, '撤回失败!!!');
        }

        $transaction->commit();
        return $this->json(200, '撤回成功!');
    }

   /**
     * 下架物料
     * @return string
     */
    public function actionObtained()
    {
        $request  = Yii::$app->request->queryParams;
        //检查提交参数并且去掉参数前两位
        $search = [];
        $checkArr = ['cont_no', 'part_no', 'lot_no'];
        foreach ($checkArr as $value) {
            if (!isset($request[$value]) || empty($request[$value])){
               continue;
            }
            $checkValue = ArrayHelper::getValue($request, $value, '');
            if (!empty($checkValue) && strlen(trim($request[$value])) > 2) {
                $search[$value] = substr(trim($request[$value]), 2);
            }
        }

        //查询需要下架的物料
        $query = ShelfMaterials::find();
        $query->where(['status' => ShelfMaterials::STATUS_EXPIRED]); //已过期
        $query->andWhere(['fettle' => ShelfMaterials::FETTLE_UP]);
        $query->andFilterWhere($search);
        $query->orderBy(['create_time' => SORT_DESC]);
        $data = new ActiveDataProvider(['query' => $query]);
        $pagination   = new Pagination([
            'totalCount' => $data->totalCount,
            'pageSize'   => 20,
        ]);
        return $this->render('obtained', [
            'request'      => $request,
            'dataProvider' => $data->getModels(),
            'pagination'   => $pagination
        ]);
    }

    /**
     * 下架物料
     * @return string
     */
    public function actionObtain()
    {
        if (Yii::$app->request->isPost) {
            $post  = Yii::$app->request->post();
            $model = ShelfMaterials::findOne($post['id']);
            if (!$model) {
                return $this->json(100, '物料不存在!');
            }

            //对比物料编号
            $compareContNo = substr(trim($post['compared_cont_no']), 2);
            if ($compareContNo != $model->cont_no) {
                return $this->json(100, '物料编号对比不正确!');
            }

            //更新物料状态
            $model->fettle       = ShelfMaterials::FETTLE_DOWN;//下架
            $model->library_time = date('Y-m-d H:i:s', time());//出库时间
            $model->update_time  = date('Y-m-d H:i:s', time());//更新时间
            if (!$model->save()) {
                return $this->json(100, '物料下架失败!');
            }

            //查询是否有别的物料
            $shelfId = $model->shelf_id;
            $query = ShelfMaterials::findOne(['shelf_id'=> $shelfId, 'fettle'  => ShelfMaterials::FETTLE_UP, 'status'  => ShelfMaterials::STATUS_EXPIRED]);
            //没有别的物料就发送消息
            if (!$query) {
                $shelfModel         = Shelf::findOne(['id' => $shelfId]);
                $shelfModel->status = Shelf::STATUS_FREE;
                if (!$shelfModel->save()) {
                    return $this->json(100, '物料下架失败!!');
                }
                //发送消息
                (new SendMqtt())->publishMessage($shelfModel->mark, $shelfModel->number, Shelf::STATUS_FREE);
            }
            return $this->json(200, '物料下架成功!');
        }
        $id      = Yii::$app->request->get('id');
        $contNo  = Yii::$app->request->get('cont_no');
        return $this->render('obtain', ['id' => $id, 'cont_no' => $contNo]);
    }

    /**
     * 转交物料
     * @return string
     */
    public function actionTransfer()
    {
        $request           = Yii::$app->request->queryParams;
        $data              = $this->searchData($request);
        return $this->render('transfer', $data);
    }

    /**
     * 完成转交物料
     * @return string
     */
    public function actionTransferSuccess()
    {
        //检查所有者信息
        $post          = Yii::$app->request->post();
        $userInfoModel = UserInfo::findOne(['badge_code' => $post['badge_code']]);
        if (!$userInfoModel) {
            return $this->json(100, '当前所有者信息不存在!');
        }
        //检查转交人员信息
        $transferUserInfoModel = UserInfo::findOne(['badge_code' => $post['transfer_badge_code']]);
        if (!$transferUserInfoModel) {
            return $this->json(100, '转交人员信息不存在!');
        }
        //检查物料是否存在
        //$shelfMaterialsModel = ShelfMaterials::findOne(['cont_no' => trim($post['cont_no'])]);
        $contNo = substr(trim($post['cont_no']),2);
		$shelfMaterialsModel = ShelfMaterials::findOne(['cont_no' => $contNo]);
		if (!$shelfMaterialsModel) {
            return $this->json(100, '物料编号不存在!');
        }
        //对比物料当前所有者
        if ($userInfoModel->id != $shelfMaterialsModel->user_id) {
            return $this->json(100, '物料当前所有者数据不正确!');
        }
        //转交物料信息
        $shelfMaterialsModel->user_id = $transferUserInfoModel->id;
        if (!$shelfMaterialsModel->save()) {
            return $this->json(100, '转交物料失败!');
        }
        return $this->json(200, '转交物料成功!');
    }

    /**
     * 报废物料
     * @return string
     */
    public function actionScrapped()
    {
        $request                = Yii::$app->request->queryParams;
        $request['expire_time'] = date('Y-m-d H:i:s', time());
        $request['scrapped']    = 1;//标识为查询报废物料
        $data                   = $this->searchData($request);
        return $this->render('scrapped', $data);
    }

    /**
     * 下架报废
     * @return string
     */
    public function actionReject()
    {
        if (Yii::$app->request->isPost) {
            //检查物料持有者信息
            $post          = Yii::$app->request->post();
            $userInfoModel = UserInfo::findOne(['badge_code' => $post['badge_code']]);
            if (!$userInfoModel) {
                return $this->json(100, '操作人员信息不存在!');
            }
            //直属领导信息存在
            $leaderUserInfo = UserInfo::findOne(['badge_code' => $post['leader_badge_code']]);
            if (!$leaderUserInfo) {
                return $this->json(100, '直属领导信息存在不存在!');
            }
            //对比直属领导信息
            if ($userInfoModel->leader_uid != $leaderUserInfo->id) {
                return $this->json(100, '直属领导信息对比不正确!');
            }

            //检查物料是否存在
            $transaction     = Yii::$app->db->beginTransaction();
            $model = ShelfMaterials::findOne(['id' => $post['id']]);
            if (!$model) {
                return $this->json(100, '物料信息不存在!');
            }

            //报废物料
            $model->status = ShelfMaterials::STATUS_SCRAPPED;
            $model->fettle = ShelfMaterials::FETTLE_DOWN; //设置为下架状态
            if (!$model->save()) {
                $transaction->rollBack();
                return $this->json(100, '报废物料失败!');
            }

            //给下位机发消息将指定位置变为空闲状态
            $shelfId = $model->shelf_id;
            if ($shelfId){
                $sendMessage = $this->sendMqttData($shelfId, Shelf::STATUS_FREE);
                if (!$sendMessage) {
                    $transaction->rollBack();
                    return $this->json(100, '报废物料失败!!');
                }
            }
            return $this->json(200, '报废物料成功!');
        }
        $id = Yii::$app->request->get('id');
        return $this->render('reject', ['id' => $id]);
    }

}
