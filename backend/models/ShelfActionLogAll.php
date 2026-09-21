<?php
namespace backend\models;
use backend\models\ShelfMaterials;

class    ShelfActionLogAll extends Base{

    const SOLD_OUT = 1;
    const HIT_SHELF = 2;
    const GET = 3;
    const APPLY = 4;
    const CONFIRM = 5;
    const REVOCATION = 6;
    const GET_LIST = 7;
    const HOLD = 8;
    const TRANSFER = 9;
    const SOLD_OUT_CONFIRM = 10;
    static $actionList = [
        self::SOLD_OUT => '下架物料',
        self::HIT_SHELF => '上架物料',
        self::GET => '领取物料',
        self::APPLY => '申请物料',
        self::CONFIRM => '二次确认领取物料',
        self::REVOCATION => '取消物料申请',
        self::GET_LIST => '领取物料列表',
        self::HOLD => 'hold',
        self::TRANSFER => '物料转移',
        self::SOLD_OUT_CONFIRM => '物料下架二次确认',
    ];


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_shelf_action_log_all';
    }

    public function getUser(){
        return $this->hasOne(UserInfo::tableName(),['id' => 'user_id']);
    }

    public static function saveLog($user_id,$action,$value){

        if($action==5 || $action==10){
            $jsonValue = json_decode($value);
            foreach($jsonValue->materials_ids as $valuematerials){
                $queryM = ShelfMaterials::findOne($valuematerials);
                $model = new ShelfActionLogAll();
                $model->user_id = $vuser_id;
                $model->action = $action;
                // $model->floor = $jsonValue->floor;
                $model->lot_no = $queryM->lot_no;
                $model->part_no = $queryM->part_no;
                $model->cont_no = $queryM->cont_no;
                $model->value = $value;
                $model->created_time = date('Y-m-d H:i:s');
                $model->save();
            }
        }
        if($action==2){
            $jsonValue = json_decode($value);
            $contNo = substr($jsonValue->cont_no, 2);
            $queryM = ShelfMaterials::findOne(['cont_no'=>$contNo]);
            $model = new ShelfActionLogAll();
            $model->user_id = $vuser_id;
            $model->action = $action;
            $model->floor = $jsonValue->floor;
            $model->wbi_id = $jsonValue->wbi_id;
            $model->lot_no = $queryM->lot_no;
            $model->part_no = $queryM->part_no;
            $model->cont_no = $queryM->cont_no;
            $model->value = $value;
            $model->created_time = date('Y-m-d H:i:s');
            $model->save();
        }

        return true;
        
    }


}