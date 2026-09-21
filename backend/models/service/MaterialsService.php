<?php
namespace backend\models\service;

use backend\models\GoodsGrid;
use backend\models\GridApply;
use backend\models\GridMaterials;

class MaterialsService extends GridMaterials{

    const INSERT_SCEN = 'Insert';
    const UPDATE_SCEN = 'update';

    public function scenarios()
    {
        return [
             self::INSERT_SCEN => ['cont_no', 'part_no', 'lot_no', 'start_time', 'end_time', 'expire_time'],
             self::UPDATE_SCEN => ['create_user', 'shelf_id', 'status'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {

        return [
            [['cont_no', 'part_no', 'lot_no', 'start_time', 'end_time', 'expire_time'],'required', 'message' => '{attribute}Cant be empty'],
            [['shelf_id', 'grid_id', 'status', 'fettle', 'apply_id', 'user_id', 'create_user', 'start_time', 'end_time', 'expire_time', 'sold_out_time', 'create_time', 'update_time'],'integer','message' => '{attribute}The error type,requirement is int'],
            [['cont_no', 'part_no', 'lot_no'], 'string', 'max' => 255, 'message' => '{attribute}The error type,requirement is string'],
            ['cont_no','contExists']
        ];
    }

    /**
     * 验证物料重复
     * @param $attribute
     * @param $params
     * @return bool
     */
    public function contExists($attribute,$params){

        $exec = self::find()->where([$attribute => $this->$attribute])->exists();
        if($exec){
            $this->addError($attribute,'Material number already exists!');
            return false;
        }
        return true;
    }

    /**
     * 添加物料
     * @param $id 格子id
     * @return bool
     */
    public function addMaterials(GoodsGrid $model){
        $this->setScenario(self::UPDATE_SCEN);
        $this->create_user = $this->getUid();
        $this->grid_id = $model->id;
        $this->shelf_id = $model->shelf_id;
        $this->status   = GridMaterials::STATUS_INFO;//入货架
        return $this->save();
    }

    /**
     * 添加物料
     * @param $id 格子id
     * @return bool
     */
    public function shelves(){
        $this->setScenario(self::UPDATE_SCEN);
        $this->status = GridMaterials::STATUS_PENDING;//待领取
        $this->fettle = GridMaterials::FETTLE_UP;//上架
        return $this->save();
    }

    /**
     * 撤回上架
     * @return bool
     */
    public function withdraw(){
        $this->setScenario(self::UPDATE_SCEN);
        $this->create_user = '';
        $this->shelf_id = '';
        $this->grid_id  = '';
        $this->status   = GridMaterials::STATUS_WAIT;
        return $this->save();
    }

    /**
     * 保存物料信息
     * @return bool
     */
    public function saveMaterials(){
        $this->setScenario(self::UPDATE_SCEN);
        $this->status      = self::STATUS_WAIT;
        $this->fettle      = self::FETTLE_AWAIT;
        $this->create_time = time();
        $this->update_time = time();
        return $this->save();
    }

    /**
     * 状态改为待领取
     * @param GridApply $model
     * @return bool
     */
    public function waitingToReceive(GridApply $model){
        $this->setScenario(self::UPDATE_SCEN);
        $this->status = self::STATUS_USING;
        $this->apply_id = $model->id;
        $this->user_id = $model->user_id;
        return $this->save();
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'shelf_id' => '货架',
            'grid_id' => '储物格',
            'status' => '状态',
            'fettle' => '上架',
            'apply_id' => '申请',
            'user_id' => '领取人',
            'cont_no' => 'cont no',
            'part_no' => 'part no',
            'lot_no' => 'Lot No',
            'create_user' => '创建人',
            'start_time' => 'start time',
            'end_time' => 'end time',
            'expire_time' => 'expire time',
            'sold_out_time' => '下架时间',
            'create_time' => '创建时间',
            'update_time' => 'Update Time',
        ];
    }


}