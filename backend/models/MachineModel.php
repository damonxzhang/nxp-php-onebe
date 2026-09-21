<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "nxp_machine_model".
 *
 * @property int $id
 * @property string|null $name 机型
 * @property int $status
 * @property int|null $createby 发布人
 * @property string|null $createdt 发布时间
 * @property string|null $updatedt 更新时间
 */
class MachineModel extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_machine_model';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'createby'], 'integer'],
            [['name', 'createdt', 'updatedt'], 'string', 'max' => 255],
            [['name'], 'unique'],
            [['name','status'], 'required']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '设备型号',
            'status' => '状态',
            'createby' => '发布人',
            'createdt' => '发布时间',
            'updatedt' => '更新时间',
        ];
    }

    public static $machineStatus = [
        '1' => '启用',
        '0' => '禁用',
    ];


    /**
     * 检查重名
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

    public function edit($data)
    {
        $model           = self::findOne($data['id']);
        $model->name  = $data['name'];
        $model->status   = $data['status'];
        $model->updatedt = (string)time();
        if (!$model->save()) {
            $error = array_values($model->getFirstErrors())[0];
            return $this->arrData(100, $error);
        }
        return $this->arrData(200, '更新成功');
    }

    public function create($data)
    {
        $this->name     = trim($data['name']);
        $this->status   = $data['status'];
        $this->createby = $data['createby'];
        $this->createdt = (string)time();
        $this->updatedt = (string)time();
        if (!$this->save()) {
            $error = array_values($this->getFirstErrors())[0];
            return $this->arrData(100, $error);
        }
        return $this->arrData(200,  $this->id);
    }

    /**
     * 获取机型by id
     * @param $id
     * @return array|bool
     */
    public function getNamesById($id){

        if(empty($id)){
            return false;
        }
        if(!empty($id) && is_string($id)){
            $id = explode(',',$id);
        }
        if(empty($id) || count($id) <= 0){
            return false;
        }
        $userArr = $this->getAllNameById($id);
        $userArr = array_column($userArr,'name','id');
        $userNameStr = [];
        foreach ($id as $k => &$v){
            $userNameStr[$v] = $userArr[$v];
        }

        return $userNameStr;
    }

    /**
     * 获取所有机型by id
     * @param $id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getAllNameById($id){
        return self::find()->select('id,name')
            ->where(['id' => $id])
            ->asArray()
            ->all();
    }

    /**
     * 获取机型 by id
     * @param $id
     * @return string
     */
    public static function getNameById($id)
    {
        if(empty($id)){
            return '';
        }
        $query = self::findOne(['id' => $id]);
        return empty($query) ? '' : $query->name;
    }

    public function getMachineModel()
    {
        $query = self::find();
        $query->select(['id', 'name']);
        $query->where(['status' => 1]);
        $query->asArray();
        return $query->all();
    }
}
