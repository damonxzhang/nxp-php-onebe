<?php

namespace backend\models;

use Yii;

class InspectionPmGroupItem extends Base
{
    const STATUS_YES = 1;
    const STATUS_NO  = 2;

    public static $statusList = [
        self::STATUS_YES => '启用',
        self::STATUS_NO  => '禁用'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_inspection_pm_group_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'group_id', 'status'], 'integer'],
            [['name'], 'string']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'             => 'ID',
            'name'           => '检查项',
            'group_id'       => '分组id',
            'status'         => '状态',
        ];
    }

    /**
     * 根据用户ID获取用户名称
     * @param $id
     * @return string
     */
    public function getItem($id){

        if(empty($id)){
            return '';
        }

        $query = InspectionPmGroupItem::findOne(['id' => $id]);

        return empty($query) ? '' : $query->name;

    }
}




