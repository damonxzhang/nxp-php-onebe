<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "nxp_machine_vendor".
 *
 * @property int $id
 * @property string $name
 * @property int $status 1启用，2禁用
 * @property string|null $create_time 添加时间
 */
class MachineVendor extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_machine_vendor';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['name', 'create_time'], 'string', 'max' => 255],
            [['name'], 'unique'],
            [['name', 'status'], 'required']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'name'        => '名称',
            'status'      => '状态',
            'create_time' => '添加时间',
        ];
    }

    public static $statusArr = [
        '1' => '启用',
        '0' => '禁用',
    ];

    /**
     * @param $data
     * @return bool
     */
    public function check($data)
    {
        $query = self::find();
        $query->where(['!=', 'id', $data['id']]);
        $query->andFilterWhere(['name' => $data['name']]);
        if ($query->one()) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * @param $data
     * @return array
     */
    public function edit($data)
    {
        $model              = self::findOne($data['id']);
        $model->name        = trim($data['name']);
        $model->status      = $data['status'];
        $model->create_time = date('Y-m-d H:i:s', time());
        if (!$model->save()) {
            $error = array_values($model->getFirstErrors())[0];
            return $this->arrData(100, $error);
        }
        return $this->arrData(200, '更新成功');
    }

    /**
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
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function VendorArr()
    {
        $query = self::find();
        $query->select(['id', 'name']);
        $query->where(['status' => 1]);
        $query->asArray();
        $data = $query->all();
        if (!empty($data)) {
            return ArrayHelper::map($data, 'id', 'name');
        }
        return $data;
    }

    /**
     * 获取机型 by id
     * @param $id
     * @return string
     */
    public static function getNameById($id)
    {
        if (empty($id)) {
            return '';
        }
        $query = self::findOne(['id' => $id]);
        return empty($query) ? '' : $query->name;
    }
}

