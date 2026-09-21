<?php
/**
 * 申请物料类
 * 22/11/20
 */
namespace frontend\controllers;


use backend\models\Shelf;
use backend\models\ShelfActionLog;
use backend\models\ShelfApply;
use backend\models\ShelfMaterialConfirm;
use backend\models\ShelfMaterials;

use backend\models\SocketInfos;
use backend\models\UserInfo;
use common\models\SendMqtt;
use Yii;
use yii\db\Exception;

class ShelfApplyController extends FrontendBaseController{

    /**
     * 领取列表
     * 22/12/03
     * @return null
     */
    public function actionApplyList(){
        $params = $this->validateRequest();
        if (empty($params['wbi_id']) || empty($params['floor'])){
            return $this->outputJson('false','参数错误');
        }

        $userInfo = $this->getUserInfo($params['wbi_id']);

        $query = ShelfApply::find();
        $query->leftJoin(UserInfo::tableName() . ' u', 'nxp_shelf_apply.user_id = u.id');
        $query = $query->where([
            'u.badge_code' => trim($userInfo->badge_code),
            'nxp_shelf_apply.status' => ShelfApply::STATUS_UNRECEIVE, //申请状态为未领取
        ])->all();

       $log_params = [];
       $data = [];
       if (!empty($query)){
           foreach ($query as $apply){
            if($params['floor'] == 1){
                $idList = (new ShelfMaterials())->getMaterialByFloor(['part_no' => $apply->part_no,'amount' => $apply->amount,'status' => ShelfMaterials::STATUS_PENDING],$params['floor']);
            
            }
            if($params['floor'] == 2){
                $idList = (new ShelfMaterials())->getMaterialByFloorTwo(['part_no' => $apply->part_no,'amount' => $apply->amount,'status' => ShelfMaterials::STATUS_PENDING],$params['floor']);
            
            }
            
               if (empty($idList)) {
                   continue;
               }

               foreach ($idList as $id){
                   $materialsModel = ShelfMaterials::findOne(['id' => $id['id']]);
                   $data[$materialsModel->id] = [
                       'id' => $materialsModel->id,
                       'apply_id' => $apply->id,
                       'part_no' => $materialsModel->part_no,
                       'cont_no' => $materialsModel->cont_no,
                       'shelf_number' => !empty($materialsModel->shelf) ? $materialsModel->shelf->shelf_number : '',
                       'user' => $userInfo->chinese_name
                   ];
                    if ($params['floor'] == Shelf::FLOOR_ONE){
                        $shelf = Shelf::findOne($materialsModel->shelf_id);
                        $code = $shelf->region. $shelf->column. '-'. $shelf->row;

                        $data[$materialsModel->id]['shelf_number'] = $code;
                    }
                    $log_params[$materialsModel->shelf_id][] = [
                        'part_no' => $materialsModel->part_no,
                        'cont_no' => $materialsModel->cont_no,
                    ];
               }
           }
       }
        ShelfActionLog::saveLog($userInfo->id,ShelfActionLog::GET_LIST,$log_params);
        if (empty($data)){
            return $this->outputJson('true','没有可领取的物料',[]);

        }
        return $this->outputJson('true','操作成功',array_values($data));

    }

    /**
     * 撤销申请
     * @return null
     * @throws Exception
     */
    public function actionUndoApply(){
        $params = $this->validateRequest();
        if (empty($params['wbi_id']) ||  empty($params['apply_id']) ){
            return $this->outputJson('false','参数错误');
        }

        $userInfo = $this->getUserInfo($params['wbi_id']);
        $apply_ids = array_unique($params['apply_id']);
        $log_params = [];
        $transaction = Yii::$app->db->beginTransaction();
        foreach ($apply_ids as $apply_id){

            if(empty($apply_id)){
                return $this->outputJson('false','撤销失败！参数错误');
            }

            $shelfApply = ShelfApply::find()->where(['id' => $apply_id,'user_id' => $userInfo->id,'status' => ShelfApply::STATUS_UNRECEIVE])->one();
            if (empty($shelfApply)){
                return $this->outputJson('false','撤销失败！申请记录不存在或已被撤销');
            }
            // 更新申请状态为撤销状态
            $shelfApply->status = ShelfApply::STATUS_CANCEL;
            $shelfApply->save();
            $materials_list = ShelfMaterials::find()->where(['status' => ShelfMaterials::STATUS_USING,'fettle' => ShelfMaterials::FETTLE_UP,'apply_id' => $apply_id])->all();
            if (!empty($materials_list)){
                foreach ($materials_list as $materials){
                    $materials->status = ShelfMaterials::STATUS_PENDING;
                    $materials->apply_id = null;
                    if (!$materials->save()){
                        $transaction->rollBack();
                        return $this->outputJson('false','保存失败！请稍后重试');
                    }
    
                    //更新货架状态为可领取状态
                    $shelfModel         = Shelf::findOne(['id' => $materials->shelf_id]);
                    $shelfModel->status = Shelf::STATUS_RECEIVE;
                    $shelfModel->save();
                    $log_params[$shelfModel->id][] = [
                        'part_no' => $materials->part_no,
                        'cont_no' => $materials->cont_no,
                    ];
                }
               
            }

        }

        ShelfActionLog::saveLog($userInfo->id,ShelfActionLog::REVOCATION,$log_params);

        $transaction->commit();
        return $this->outputJson('true','撤销成功！');
    }


    /**
     * 物料领取
     * @return null
     */
    public function actionApplyReceive(){
        $params = $this->validateRequest();
        if (empty($params['wbi_id']) || empty($params['apply_id'])  || (empty($params['materials_ids']) || !is_array($params['materials_ids']))){
            return $this->outputJson('false','参数错误');
        }

        $userInfo = $this->getUserInfo($params['wbi_id']);

        $resData = [];
        try{
            $transaction = Yii::$app->db->beginTransaction();
            $apply_id = $params['apply_id'];
            $log_params = [];
            $action = ShelfActionLog::GET;
            foreach ($params['materials_ids'] as $k => $materials_id){
                $materials = ShelfMaterials::find()->where(['id' => $materials_id ,'status' => ShelfMaterials::STATUS_PENDING])->one();
                if(empty($materials)){
                    continue;
                }
                $shelf = Shelf::find()->where(['id' => $materials->shelf_id])->one();

                // 判断物料是否需要二次确认
                if(ShelfMaterialConfirm::find()->where(['part_no' => $materials->part_no,'status' => ShelfMaterialConfirm::SELECT_YES])->exists()){
                    $shelfApply = ShelfApply::findOne($apply_id[$k]);
                    // 领取物料
                    $materials->receive();
                    $shelfApply->applyAccomplish();
                    $log_params[$materials->shelf_id][] = [
                        'part_no' => $materials->part_no,
                        'cont_no' => $materials->cont_no,
                    ];
                    $action = ShelfActionLog::CONFIRM;

                } else {
                    $materials->status   = ShelfMaterials::STATUS_USING;
                    $materials->apply_id = $apply_id[$k];
                    if (!$materials->save()) {
                        throw new \Exception('物料领取失败！保存失败');
                    }
                    $shelf->status = Shelf::SHELF_RECEIVED;
                    $shelf->save();
                    $resData[$k] = [
                        'id' => $materials->id,
                        'apply_id' => $materials->apply_id,
                        'part_no' => $materials->part_no,
                        'cont_no' => $materials->cont_no,
                        'shelf_number' => !empty($materials->shelf) ? $materials->shelf->shelf_number : '',
                        'user' => $userInfo->chinese_name
                    ];

                    if ($shelf->floor == Shelf::FLOOR_ONE){
                        $shelf = Shelf::findOne($materials->shelf_id);
                        $code = $shelf->region. $shelf->column. '-'. $shelf->row;

                        $resData[$k]['shelf_number'] = $code;
                    }


                    $log_params[$materials->shelf_id][] = [
                        'part_no' => $materials->part_no,
                        'cont_no' => $materials->cont_no,
                    ];
                    // 发送请求
                    if ($shelf->floor == Shelf::FLOOR_TWO){
                        (new SendMqtt())->publishMessage($shelf->mark, $shelf->number, Shelf::SHELF_RECEIVED);
                        usleep(400000);
                    } else {
                        SocketInfos::addAction(['shelf_number' =>  $resData[$k]['shelf_number'] ,'car_number' => $shelf->car_number],SocketInfos::OPEN_THE_DOOR_ACTION,SocketInfos::STATUS_AWAIT);
                    }
                }
            }
            
            ShelfActionLog::saveLog($userInfo->id,$action,$log_params);
            $transaction->commit();

        }catch (\Exception $e){
            $transaction->rollBack();
            return $this->outputJson('false',$e->getMessage().$e->getLine());
        }

        return $this->outputJson('true','操作成功！',$resData);
    }

    /**
     * 物料领取二次确认
     * @return null
     */
    public function actionConfirmApply(){
        $params = $this->validateRequest();
        if (empty($params['wbi_id']) || empty($params['materials_ids']) || !is_array($params['materials_ids']) || empty($params['cont_no']) || !is_array($params['cont_no'])){
            return $this->outputJson('false','参数错误');
        }
        $userInfo = $this->getUserInfo($params['wbi_id']);
        $materials_ids = $params['materials_ids'];
        $cont_nos = $params['cont_no'];
        $transaction = Yii::$app->db->beginTransaction();
        try{
            foreach ($materials_ids as $k => $materials_id){
                
                $materials = ShelfMaterials::find()->where(['id' => $materials_id ,'status' => ShelfMaterials::STATUS_USING])->one();
                if (empty($materials) || empty($materials->apply_id)){
                    return $this->outputJson('false','领取失败！记录不存在');
                }
                if (empty($cont_nos[$k]) || $cont_nos[$k] != $materials->cont_no){
                    return $this->outputJson('false','物料编号错误');
                }

                $shelfApply = ShelfApply::find()->where(['id' => $materials->apply_id, 'user_id' => $userInfo->id])->one();
                if (empty($shelfApply)){
                    return $this->outputJson('false','领取失败！领取记录不存在');
                }

                $shelf = Shelf::find()->where(['id' => $materials->shelf_id])->one();
                // print_r($shelf);die;
                if (empty($shelf)){
                    return $this->outputJson('false','领取失败！数据异常');
                }

                if ($shelfApply->status == ShelfApply::STATUS_UNRECEIVE){
                    $shelfApply->status = ShelfApply::STATUS_RECEIVE;
                    $shelfApply->save();
                }


                $materials->receive($shelf->floor);
                $shelfApply->applyAccomplish();

                // print_r($shelfModel->id);die;
                if ($shelf->id){
                    if ( $shelf->floor == 2){
                        // print_r($shelfModel->id);die;
                        $sendMessage = $this->sendMqttData($shelf->id, Shelf::STATUS_FREE,2);
                        if (!$sendMessage) {
                            $transaction->rollBack();
                            return $this->outputJson('false', '给下位机发消息失败!!');
                        }
                    } 
                }
                $log_params[$materials->shelf_id][] = [
                    'part_no' => $materials->part_no,
                    'cont_no' => $materials->cont_no,
                ];
            }
            ShelfActionLog::saveLog($userInfo->id,ShelfActionLog::CONFIRM,$params);
            $transaction->commit();
        }catch (\Exception $e){
            $transaction->rollBack();
            return $this->outputJson('false',$e->getMessage());
        }
        return $this->outputJson('true','操作成功！');
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
        if ($query) {
            return true;
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


}