<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

class InspectionQuestionType extends Base
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
        return 'nxp_inspection_question_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['name', 'create_time'], 'string']
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'           => 'ID',
            'name'         => '报修问题类型',
            'status'       => '状态',
            'create_time' => '创建时间'
        ];
    }

    /**
     * 添加类型
     * @param $data
     * @return array
     */
    public function create($data)
    {
        $this->name        = trim($data['name']);
        $this->status      = $data['status'];
        $this->create_time = date('Y-m-d H:i:s', time());
        if (!$this->save()) {
            $error = array_values($this->getFirstErrors())[0];
            return $this->arrData(100, $error);
        }
        return $this->arrData(200, '添加成功');
    }

    /**
     * 修改类型
     * @param $data
     * @return array
     */
    public function edit($data)
    {
        $model           = self::findOne($data['id']);
        $model->name     = $data['name'];
        $model->status   = $data['status'];
        if (!$model->save()) {
            $error = array_values($model->getFirstErrors())[0];
            return $this->arrData(100, $error);
        }
        return $this->arrData(200, '更新成功');
    }

    /**
     * 检查类型是否存在
     * @param $data
     * @return bool
     */
    public function check($data)
    {
        $query = self::find();
        $query->where(['!=', 'id', $data['id']]);
        $query->andFilterWhere(['name'  => $data['name']]);
        if ($query->one()) {
            return false;
        }
        return true;
    }

    /**
     * 获取类型
     * @return array
     */
    public function getType()
    {
        $query = self::find();
        $query->select(['id', 'name']);
        $query->where(['status' => self::STATUS_YES]);
        $query->asArray();
        $data = $query->all();
        return $data ? ArrayHelper::map($data, 'id', 'name') : [];
    }

}



