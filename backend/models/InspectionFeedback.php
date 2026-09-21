<?php

namespace backend\models;

use Yii;

class InspectionFeedback extends Base
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_inspection_feedback';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['feedback','created_at'], 'string'],
            [['id', 'status', 'user_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'feedback'       => '反馈意见',
            'user_id'       => '关联用户',
            'status'        => '是否启用'
        ];
    }
}


