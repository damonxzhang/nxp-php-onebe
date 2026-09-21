<?php

namespace frontend\controllers;

use backend\models\ShelfActionLog;
use backend\models\ShelfMaterialConfirm;
use backend\models\SocketInfos;
use common\models\SendMqtt;
use Yii;
use backend\models\search\ShelfMaterialsSearch;
use backend\models\Shelf;
use backend\models\ShelfMaterials;
use backend\models\UserInfo;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class ShelfMaterialsController extends FrontendBaseController
{
    /**
     * 物料上架
     * @return string
     * @throws \yii\db\Exception
     */
    public function actionCreate()
    {

        $params = $this->validateRequest();
        //检查货架是否存在
        if (empty($params['shelf_number']) || empty($params['cont_no']) || empty($params['floor'])|| empty($params['wbi_id'])){
            return $this->outputJson('false','参数错误');
        }

        $shelfModel = Shelf::findOne(['shelf_number' => trim($params['shelf_number']),'floor' => $params['floor']]);
        if (!$shelfModel) {
            return $this->outputJson('false','货架编号不存在');
        }
        if ($shelfModel->end_shelf == Shelf::END_SHELF_YES) {
            return $this->outputJson('false','货架已完成上架');
        }


        $contNo = substr(trim($params['cont_no']),2);
        $model = ShelfMaterials::findOne(['cont_no' => $contNo]);

        if (!$model) {
            return $this->outputJson('false','物料不存在');
        }
        // 增加一个逻辑，不能上不同part_no的物料
        // 先判断架子上是否有物料
        $checkShelfModel = ShelfMaterials::findOne(['shelf_id' => $shelfModel->id,'fettle'=>1,'status'=>2]);
        if ($checkShelfModel){
            // 判断是否同一个型号
            if($model->part_no == $checkShelfModel->part_no && 
            $model->start_time == $checkShelfModel->start_time){
                
            }
            else{
                return $this->outputJson('false','同一位置不能上不同类型物料');
            }
        }


        $userInfo = $this->getUserInfo($params['wbi_id']);

        if (empty($userInfo)){
            return $this->outputJson('false','请输入正确的识别证号');
        }

        if ($params['floor'] == Shelf::FLOOR_TWO){
            //检查物料是否存在


            $uncard1 = ['11ASA10154D002','11ARL10103D003','11ASA00268D001'];
            // $uncard1 = ['11ASA10154D002','11ARL10103D003','11ARE10145D012'];
            if ($shelfModel->mark == 1){
                if (!in_array($model->part_no,$uncard1)){
                    return $this->outputJson('false','物料不能和货架绑定');
                }
            }


            // $uncard2 = ['11ASA10154D002','11ARL10103D003','11ASA00268D001'];
            // if ($shelfModel->mark == 2){
            //     if (in_array($model->part_no,$uncard2)){
            //         return $this->json(100, '物料不能和【二号】货架绑定!');
            //     }
            // }
    
        }

        $transaction     = Yii::$app->db->beginTransaction();

        //保存物料货架信息
        $model->entry_id = $userInfo->id;
        $model->shelf_id = $shelfModel->id;
        $model->status   = ShelfMaterials::STATUS_PENDING;//入货架
        $model->fettle = ShelfMaterials::FETTLE_UP;//上架
        $model->update_time = date('Y-m-d H:i:s', time());//更新时间0
        if (!$model->save()) {
            $transaction->rollBack();
            return $this->outputJson('false','保存物料货架信息失败');
        }

        //给下位机发消息
        if ($shelfModel->id){
            if ( $params['floor'] == 2){
                $sendMessage = $this->sendMqttData($shelfModel->id, Shelf::STATUS_PROGRESS,$params['floor']);
                if (!$sendMessage) {
                    $transaction->rollBack();
                    return $this->outputJson('false', '给下位机发消息失败!!');
                }
            } else {
                SocketInfos::addAction(['shelf_number' => $shelfModel->shelf_number,'car_number' => $shelfModel->car_number],SocketInfos::OPEN_THE_DOOR_ACTION,SocketInfos::STATUS_AWAIT);
            }

        }

        $changeStatus = Shelf::findOne($shelfModel->id);
        $changeStatus->status = Shelf::STATUS_PROGRESS;
        $changeStatus->save();
        

        $log_params[$model->shelf_id][] = [
            'part_no' => $model->part_no,
            'cont_no' => $model->cont_no,
            'wbi_id' => $params['wbi_id']
        ];
        ShelfActionLog::saveLog($userInfo->id,ShelfActionLog::HIT_SHELF,$log_params);

        $transaction->commit();
        return $this->outputJson('true','添加成功');

    }

    
    public function actionEndUp(){
        $params = $this->validateRequest();
        //检查货架是否存在
        if (empty($params['shelf_number']) || empty($params['floor'])|| empty($params['wbi_id'])){
            return $this->outputJson('false','参数错误');
        }

        $shelfModel = Shelf::find()->where(['shelf_number' => trim($params['shelf_number']),'floor' => $params['floor']])->one();
        if (!$shelfModel) {
            return $this->outputJson('false','货架编号不存在');
        }

        $shelfModel = Shelf::find()->where(['car_number' => $shelfModel->car_number])->all();
        if (!$shelfModel) {
            return $this->outputJson('false','货架编号不存在');
        }
        $userInfo = $this->getUserInfo($params['wbi_id']);
        if (empty($userInfo)){
            return $this->outputJson('false','请输入正确的识别证号');
        }

        $transaction     = Yii::$app->db->beginTransaction();
        foreach ($shelfModel as $model){
            $model->end_shelf = Shelf::END_SHELF_YES;
            $car_id = $model->car_number;
            $model->save();
        }

        if (empty($car_id)){
            $transaction->rollBack();
            return $this->outputJson('false','设备异常');
        }

        SocketInfos::addAction(['car_number' => $car_id],SocketInfos::MOVE_CARD_ACTION,SocketInfos::STATUS_AWAIT);
        $transaction->commit();
        return $this->outputJson('true','操作成功');
    }



    /**
     * 给下位机发消息
     * @param $shelfId
     * @param string $status
     * @return bool
     */
    public function sendMqttData($shelfId, $status = '', $floor)
    {
        //检查是否还有在货架格子上的物料$
        $query = (new ShelfMaterials())->checkMaterialsByShelfId($shelfId);

        // print_r($query);die;
        if (!$query) {
            return false;
        }
        $shelfModel         = Shelf::findOne(['id' => $shelfId]);
        $shelfModel->status = $status;
        if (!$shelfModel->save()) {
            return false;
        }
        // 二楼货架发送通知
        if ($floor === Shelf::FLOOR_TWO){
            $sendMessage = (new SendMqtt())->publishMessage($shelfModel->mark, $shelfModel->number, $status);
            if (!$sendMessage) {
                return false;
            }
        }

        return true;
    }


    /**
     * 下架物料查询
     * @return string
     */
    public function actionObtained()
    {
        $params = $this->validateRequest();
        if (empty($params['floor']) && in_array($params['floor'],Shelf::$floorList)){
            return $this->outputJson('false','请输入楼层');
        }
        $floor  =$params['floor'];

        //查询需要下架的物料
        $query = ShelfMaterials::find();
        $query->alias('sm')->leftJoin(['s' => Shelf::tableName()],'sm.shelf_id = s.id' )->where(['sm.status' => ShelfMaterials::STATUS_EXPIRED]); //已过期
        $query->andWhere(['sm.fettle' => ShelfMaterials::FETTLE_UP]);
        $query->andWhere(['s.floor' => $floor]);
        // 进查询领用区的物料
        if ($floor == Shelf::FLOOR_ONE){
            $query->andWhere(['in' , 'region' , [Shelf::G , Shelf::Y]]);
        }
        $data = $query->orderBy(['sm.create_time' => SORT_DESC])
            ->asArray()
            ->select('s.shelf_number,sm.cont_no,sm.part_no,sm.status,sm.id')
            ->all();
        if (empty($data)){
            return $this->outputJson('true','没有可以下架的物料');
        }
        foreach ($data as $value){
            $value['status_txt'] = '';
            if (!empty(ShelfMaterials::$statusArr[$value['status']])){
                $value['status_txt'] = ShelfMaterials::$statusArr[$value['status']];
            }
        }

        return $this->outputJson('true','操作成功', $data);
    }

    public function actionConfirm(){
        $params = $this->validateRequest();
        if (empty($params['wbi_id']) || empty( $params['materials_id']) || !is_array( $params['materials_id']) || empty($params['cont_no']) || !is_array($params['cont_no'])){
            return $this->outputJson('false','参数错误');
        }

        $userInfo = $this->getUserInfo($params['wbi_id']);
        $this->checkMaterialsAuth($userInfo->id);


        $materialsIds = $params['materials_id'];
        $cont_nos = $params['cont_no'];
        $resData = [];
        $transaction = Yii::$app->db->beginTransaction();
        try{
            foreach ($materialsIds as $k => $id){
                $model = ShelfMaterials::findOne($id);
                if (!$model) {
                    return $this->outputJson('false','物料不存在');
                }
                // $cont_nos[$k] = substr(trim($cont_nos[$k]),2);
                if (empty($cont_nos) || $model->cont_no != $cont_nos[$k]){
                    return $this->outputJson('false','请输入正确的物料编号');
                }

                if ($model->fettle !=  ShelfMaterials::FETTLE_UP || $model->status  != ShelfMaterials::STATUS_EXPIRED) {
                    return $this->outputJson('false','请确认物料状态是否允许下架');
                }
                // $this->shelvesMaterials($model);
                
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
            }
            $transaction->commit();
        }catch (\Exception $e){
            $transaction->rollBack();
            return $this->outputJson('false',$e->getMessage());
        }

        ShelfActionLog::saveLog($userInfo->id,ShelfActionLog::SOLD_OUT_CONFIRM,$params);
        return $this->outputJson('true','操作成功');
    }


    public function shelvesMaterials($model){
        $shelfId = $model->shelf_id;

        $shelfModel = Shelf::findOne(['id' => $shelfId]);


        // 发送请求
        if ($shelfModel->floor == Shelf::FLOOR_ONE){
            $shelf = Shelf::findOne($model->shelf_id);
            if ($shelf->region == Shelf::G || $shelf->region == Shelf::Y ){
                $shelf_number = $shelf->region. $shelf->column. '-'. $shelf->row;
            } else {
                $shelf_number = $shelf->shelf_number;
            }
            SocketInfos::addAction(['shelf_number' => $shelf_number,'car_number' => $shelf->car_number],SocketInfos::OPEN_THE_DOOR_ACTION,SocketInfos::STATUS_AWAIT);
            $model->receive();

        } else {
            //更新物料状态
            $model->fettle       = ShelfMaterials::FETTLE_DOWN;//下架
            $model->library_time = date('Y-m-d H:i:s', time());//出库时间
            $model->update_time  = date('Y-m-d H:i:s', time());//更新时间
            if (!$model->save()) {
                throw new \Exception('物料下架操作失败');
            }
            //查询是否有别的物料
            $query = ShelfMaterials::findOne(['shelf_id'=> $shelfId, 'fettle'  => ShelfMaterials::FETTLE_UP, 'status'  => ShelfMaterials::STATUS_EXPIRED]);

            //没有别的物料就发送消息
            if (!$query) {
                $shelfModel->status = Shelf::STATUS_FREE;
                if (!$shelfModel->save()) {
                    throw new \Exception('操作失败');
                }
                if ($shelfModel->floor == Shelf::FLOOR_TWO){
                    (new SendMqtt())->publishMessage($model->mark, $model->number, Shelf::STATUS_FREE);
                    usleep(400000);
                }

            }else{
                $shelfModel->status = Shelf::STATUS_RECEIVE;
                if (!$shelfModel->save()) {
                    throw new \Exception('操作失败');
                }
                if ($shelfModel->floor == Shelf::FLOOR_TWO){
                    (new SendMqtt())->publishMessage($model->mark, $model->number, Shelf::STATUS_RECEIVE);
                    usleep(400000);
                }

            }
        }


    }

    /**
     * 下架物料操作
     * @return string
     */
    public function actionObtain()
    {
        $params = $this->validateRequest();
        if (empty( $params['materials_id']) || empty( $params['wbi_id']) || !is_array( $params['materials_id'])){
            return $this->outputJson('false','接口请求失败！请先搜索物料！');
        }
        file_put_contents('./log.txt',json_encode(['params' => $params]).PHP_EOL, FILE_APPEND);

        $userInfo = $this->getUserInfo($params['wbi_id']);
        $this->checkMaterialsAuth($userInfo->id);
        $materialsIds = $params['materials_id'];

        $resData = [];
        $log_params = [];
        foreach ($materialsIds as $id){
            $model = ShelfMaterials::findOne($id);
            if (!$model) {
                return $this->outputJson('false','下架的物料不存在');
            }

            if ($model->fettle !=  ShelfMaterials::FETTLE_UP || $model->status  != ShelfMaterials::STATUS_EXPIRED) {
                return $this->outputJson('false','请确认物料状态是否允许下架');
            }

            // 判断物料是否需要二次确认
            if(ShelfMaterialConfirm::find()->where(['part_no' => $model->part_no,'status' => ShelfMaterialConfirm::SELECT_YES])->exists()){
                $this->shelvesMaterials($model);
            } else {
                $resData[] = [
                    'id' => $model->id,
                    'part_no' => $model->part_no,
                    'cont_no' => $model->cont_no,
                    'shelf_number' => !empty($model->shelf) ? $model->shelf->shelf_number : ''
                ];
            }

           
            $shelf = Shelf::findOne($model->shelf_id);
            if ($shelf->floor == Shelf::FLOOR_ONE){
                if ($shelf->region == Shelf::G || $shelf->region == Shelf::Y ){
                    $shelf_number = $shelf->region. $shelf->column. '-'. $shelf->row;
                } else {
                    $shelf_number = $shelf->shelf_number;
                }
                SocketInfos::addAction(['shelf_number' => $shelf_number,'car_number' => $shelf->car_number],SocketInfos::OPEN_THE_DOOR_ACTION,SocketInfos::STATUS_AWAIT);
            }
            $log_params[$model->shelf_id][] = [
                'part_no' => $model->part_no,
                'cont_no' => $model->cont_no,
            ];

        }
        if (empty($resData)){
            return $this->outputJson('false','申请的物料库存不足');
        }
        ShelfActionLog::saveLog($userInfo->id,ShelfActionLog::SOLD_OUT,$log_params);
        return $this->outputJson('true','操作成功',$resData);
    }

    public function actionMobileShelf(){
        $params = $this->validateRequest();
        if ( empty($params['new_shelf_number']) || empty($params['cont_no']) || empty($params['floor']) || empty($params['wbi_id']) ){
            return $this->outputJson('false','参数错误！');
        }

        if ($params['floor'] != Shelf::FLOOR_ONE){
            return $this->outputJson('false','参数错误！');
        }

        $userInfo = $this->getUserInfo($params['wbi_id']);
        $newShelfModel = Shelf::findOne(['shelf_number' => trim($params['new_shelf_number']),'floor' => $params['floor']]);
        if (!$newShelfModel) {
            return $this->outputJson('false','新仓位编号不存在');
        }

        if ($newShelfModel->region != Shelf::Y){
            return $this->outputJson('false','仅允许移动到临期区');
        }
        $contNo = substr(trim($params['cont_no']),2);
        $model = ShelfMaterials::findOne(['cont_no' => $contNo]);

        if (empty($model) || empty($model->shelf_id)){
            return $this->outputJson('false','物料不存在');
        }

        // 增加一个判断，不能转移了
        if ($model->fettle == 2){

            return $this->outputJson('false','物料已不再货架上，不能进行转移');
        }
        if ($model->status == 3){

            return $this->outputJson('false','物料在二次确认中，不能进行转移');
        }

        // // 增加一个逻辑，不能上不同part_no的物料
        // // 先判断架子上是否有物料
        // $checkShelfModel = ShelfMaterials::findOne(['shelf_id' => $shelfModel->id,'fettle'=>1,'status'=>2]);
        // if ($checkShelfModel){
        //     // 判断是否同一个型号
        //     if($model->part_no == $checkShelfModel->part_no && 
        //     $model->start_time == $checkShelfModel->start_time){
                
        //     }
        //     else{
        //         return $this->outputJson('false','同一位置不能上不同类型物料');
        //     }
        // }

        $transaction     = Yii::$app->db->beginTransaction();
        try{
            $his_shelf = Shelf::findOne($model->shelf_id);
                
            $model->shelf_id = $newShelfModel->id;
        
            $model->status = ShelfMaterials::STATUS_PENDING;
            // 修改物料数据
            if (!$model->save()) {
                $transaction->rollBack();
                return $this->outputJson('false','操作失败');
            }

            $newShelfModel->status = Shelf::STATUS_RECEIVE;
            // 修改物料数据
            if (!$newShelfModel->save()) {
                $transaction->rollBack();
                return $this->outputJson('false','操作失败');
            }
            $model->setShelfStatus($his_shelf->car_number);

        }catch (\Exception $e){
            $transaction->rollBack();
            file_put_contents('./log.txt',json_encode(['msg' => $e->getMessage()]).PHP_EOL, FILE_APPEND);
            return $this->outputJson('false','操作失败');
        }

        $transaction->commit();
        $log_params[$model->shelf_id][] = [
            'part_no' => $model->part_no,
            'cont_no' => $model->cont_no,
        ];

        ShelfActionLog::saveLog($userInfo->id,ShelfActionLog::TRANSFER,$log_params);


        return $this->outputJson('true','物料转移完成');

    }

    /**
     * 物料hold
     * @return null
     */
    public function actionMobileHold(){
        
        return $this->outputJson('false','此功能暂不开放');
        
        $params = $this->validateRequest();
        if (empty($params['wbi_id']) || empty($params['cont_no']) || empty($params['floor'])){
            return $this->outputJson('false','请求失败！参数错误！');
        }
        if ($params['floor'] == Shelf::FLOOR_TWO){
            return $this->outputJson('false','操作失败！当前楼层不支持物料移动！');
        }
        $userInfo = $this->getUserInfo($params['wbi_id']);
        $contNo = substr(trim($params['cont_no']),2);

        $shelfStatus = ShelfMaterials::find()->where(['cont_no' => $contNo,'fettle' => ShelfMaterials::FETTLE_UP])->one();
        if (empty($shelfStatus)){
            return $this->outputJson('false','物料不存在');
        }

        $shelf = Shelf::find()->where(['id' => $shelfStatus->shelf_id])->one();

        try{
            $shelfStatus->status = ShelfMaterials::STATUS_HOLD;
            $shelfStatus->save();

            $shelfId = $this->shelf_id;
            $query = self::findOne(['shelf_id'=> $shelfId, 'fettle'  => ShelfMaterials::FETTLE_UP, 'status'  => ShelfMaterials::STATUS_PENDING]);
            //有别的物料并且状态为待领取
            $shelfModel  = Shelf::findOne(['id' => $shelfId]);
            if (!$query) {
                $status  = Shelf::STATUS_FREE;//空闲
                $shelfModel->status = $status;
                $shelfModel->save();
            }

        }catch (\Exception $e){
            return $this->outputJson('false','操作失败，'.$e->getMessage());
        }

        ShelfActionLog::saveLog($userInfo->id,ShelfActionLog::TRANSFER,$params);

        // 发送请求
        // SocketInfos::addAction(['shelf_number' => $shelf->shelf_number,'car_number' => $shelf->shelf_number],SocketInfos::OPEN_THE_DOOR_ACTION);


        return $this->outputJson('true','操作成功');
    }

}
