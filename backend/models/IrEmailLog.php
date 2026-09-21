<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "nxp_ir_email_log".
 *
 * @property int $id
 * @property int|null $ir_id ir
 * @property int|null $type 类型
 * @property int|null $create_time 创建时间
 */
class IrEmailLog extends Base
{
    //从创建到评估超时
    const ASSESS_TYPE = 1;

    //从初评到评估超时
    const FIRST_ASSESS_TYPE = 2;

    //从创建到主管超时
    const BEFORE_ASSESS_TYPE = 3;

    //负责人超时
    const HEAD_TYPE = 4;

    //实施人超时
    const WORK_TYPE = 5;

    //完成确认超时
    const CONFIRM_TYPE  = 6;
    //奖励发放超时
    const REWARD_TYPE   = 7;
    /**
     * 超时提醒类型
     * @var array
     */
    public static $timeOut_arr = [
        self::ASSESS_TYPE,
        self::FIRST_ASSESS_TYPE,
        self::HEAD_TYPE,
        self::WORK_TYPE
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_ir_email_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ir_id', 'type', 'create_time'], 'integer'],
        ];
    }

    /**
     * @return bool
     */
    public function irIsSend($id,$type){
        return self::find()->where(['ir_id' => $id,'type' => $type])->exists();
    }



    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ir_id' => 'Ir ID',
            'type' => 'Type',
            'create_time' => 'Create Time',
        ];
    }
}
