<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "nxp_charging_tank".
 *
 * @property string $id
 * @property string $number 充电柜编号
 * @property int $status 状态
 * @property int $shift 当班班别
 * @property int $shift_user 当班带班
 * @property string $updated_at 更新时间
 * @property string $created_at 创建时间
 */
class ChargingTank extends Base
{
    const STATUS_YES = 1;
    const STATUS_NO  = 0;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_charging_tank';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['number'], 'required'],
            [['status', 'shift', 'shift_user'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [['number'], 'string', 'max' => 30],
        ];
    }

    /**
     * 是否当班
     * @param $shift
     * @param $tank_id
     * @return bool
     */
    public static function isOnDuty($shift,$tank_id){

        $model = self::findOne(['id' => $tank_id]);
        if(!empty($model) && $shift == $model->shift){

            return $model;
        }
        return false;
    }

    public function getIdByNumber($number){


    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'number' => '编号',
            'status' => 'Status',
            'shift' => 'Shift',
            'shift_user' => 'Shift User',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }
}
