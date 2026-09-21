<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "nxp_machine_rule".
 *
 * @property int $id
 * @property string|null $name 规则
 * @property int $status 1启用，0禁用
 * @property int|null $score 分数
 * @property int $process_id 工序id
 * @property int $model_id 机型id
 * @property string|null $options_ids 检查项id
 * @property int|null $createby 发布人
 * @property string|null $createdt 发布时间
 * @property string|null $updatedt 更新时间
 */
class MachineRule extends \backend\models\Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_machine_rule';
    }

    public static $statusList = [
        self::SELECT_YES  => '开启',
        self::SELECT_NO   => '关闭'
    ];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'score', 'process_id', 'model_id', 'createby'], 'integer'],
            [['process_id', 'model_id','name','status','options_ids'], 'required'],
            [['name', 'options_ids', 'createdt', 'updatedt'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '规则',
            'status' => '状态',
            'score' => '分数',
            'process_id' => '工序',
            'model_id' => '机型',
            'options_ids' => '检查项',
            'createby' => '发布人',
            'createdt' => '发布时间',
            'updatedt' => '更新时间',
        ];
    }

    public function create($data)
    {
        $this->name     = trim($data['name']);
        $this->score   = $data['score'];
        $this->status   = $data['status'];
        $this->process_id   = $data['process_id']?:0;
        $this->model_id   = $data['model_id']?:0;
        $this->options_ids   = $data['options_ids'];
        if (!$this->save()) {
            $error = array_values($this->getFirstErrors())[0];
            return $this->arrData(100, $error);
        }
        return $this->arrData(200, '添加成功');
    }

    public function edit($data)
    {
        $model           = self::findOne($data['id']);
        $model->name  = $data['name'];
        $model->score  = $data['score'];
        $model->process_id  = $data['process_id']?:$model->process_id;
        $model->model_id  = $data['model_id']?:$model->model_id;
        $model->options_ids  = $data['options_ids'];
        $model->status   = $data['status'];
        if (!$model->save()) {
            $error = array_values($model->getFirstErrors())[0];
            return $this->arrData(100, $error);
        }
        return $this->arrData(200, '更新成功');
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord){
                $this->createby = $this->getUid();
                $this->createdt = (string)time();
            }
            $this->updatedt = (string)time();
            return true;
        } else {
            return false;
        }
    }

}
