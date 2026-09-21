<?php
namespace backend\models\Service;


use backend\models\GridApply;

class ApplyMaterialService extends  GridApply {

    const CREATE_SCE = 'create';

    public $badge_code;
    public $count;

    public function scenarios()
    {
        return [
            self::CREATE_SCE => ['part_no', 'badge_code', 'count', 'machine_id']
        ];
    }

    public function behaviors()
    {
        return [$this->getTimestampBehaviorArray()];
    }

    public function rules()
    {
        return [
            [['part_no', 'badge_code', 'amount', 'machine_id'],'required','message' => '{attribute}Cant be empty'],
            [['apply_id', 'grid_id', 'count', 'create_time'], 'integer','message' => '{attribute}The error type,requirement is int'],
            [['cont_no','cont_no'], 'string', 'max' => 255, 'message' => '{attribute}The error type,requirement is string'],
        ];
    }

    public function addApply($user_id){
        $this->status = self::STATUS_UNRECEIVE;
        $this->user_id = $user_id;
        $this->amount = $this->count;
        return $this->save();
    }

}