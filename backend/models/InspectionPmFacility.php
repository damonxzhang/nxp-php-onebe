<?php

namespace backend\models;

use Yii;

class InspectionPmFacility extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_inspection_pm_facility';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'group_id', 'facility_id'], 'integer']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'facility_id' => '设备表id',
            'group_id'    => '检查分组表id'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFacility()
    {
        return $this->hasOne(Facility::className(), ['id' => 'facility_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInspectionPmGroup()
    {
        return $this->hasOne(InspectionPmGroup::className(), ['id' => 'group_id']);
    }

    public function getFacilityName($id)
    {
        $model = Facility::findOne($id);
        return $model ? $model->name : '';
    }

    public function getFacilityNumber($id)
    {
        $model = Facility::findOne($id);
        return $model ? $model->number : '';
    }
}


