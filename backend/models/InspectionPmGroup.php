<?php

namespace backend\models;

use Yii;

class InspectionPmGroup extends Base
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
        return 'nxp_inspection_pm_group';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'create_user_id', 'status'], 'integer'],
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
            'name'           => '名称',
            'create_user_id' => '创建人id',
            'status'         => '状态',
        ];
    }

    public function getGroupName($id)
    {
        $model = self::findOne($id);
        return $model ? $model->name : '';
    }

    public function create($data)
    {
        $this->name     = trim($data['name']);
        $this->status   = self::STATUS_YES;
        $this->create_user_id = $data['create_user_id'];
        if (!$this->save(false)) {
            $error = array_values($this->getFirstErrors())[0];
            return $this->arrData(100, $error);
        }
        $groupId = $this->attributes['id'];
        foreach ($data['group_item'] as $name){
            $groupItem = new InspectionPmGroupItem();
            $groupItem->group_id = $groupId;
            $groupItem->name = $name;
            $groupItem->save(false);
        }
        return $this->arrData(200, '添加成功');
    }

}



