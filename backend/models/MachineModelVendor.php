<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "nxp_machine_model".
 *
 * @property int $id
 * @property int $model_id
 * @property int $vendor_id
 */
class MachineModelVendor extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_machine_model_vendor';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['model_id', 'vendor_id','id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'model_id' => '',
            'vendor_id' => '',
        ];
    }

}
