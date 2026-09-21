<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "nxp_associated_appeal".
 *
 * @property int $appeal_id 申诉id
 * @property int $associated_id 关联id
 */
class Appbutton extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_app_button';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'url'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => 'name',
            'url' => 'url',
        ];
    }
}
