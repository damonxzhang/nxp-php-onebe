<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "nxp_associated_appeal".
 *
 * @property int $appeal_id 申诉id
 * @property int $associated_id 关联id
 */
class AssociatedAppeal extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_associated_appeal';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['appeal_id', 'associated_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'appeal_id' => 'appeal ID',
            'associated_id' => 'Associated ID',
        ];
    }
}
