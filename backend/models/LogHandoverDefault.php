<?php

namespace backend\models;

use yii\helpers\ArrayHelper;

class LogHandoverDefault extends Base
{
    const STATUS_OPEN  = 1;
    const STATUS_CLOSE = 2;

    //启用状态
    public static $statusArr = [
        self::STATUS_OPEN  => '启用',
        self::STATUS_CLOSE => '禁用',
    ];
    
    const TYPE_MATERIAL  = 1;
    const TYPE_PLANT     = 2;
    const TYPE_COMMENT   = 3;

    //类型
    public static $typeArr = [
        self::TYPE_MATERIAL  => '物料',
        self::TYPE_PLANT     => '设备',
        self::TYPE_COMMENT   => '备注',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_log_handover_default';
    }

    /**
     * 添加
     * @param $data
     * @return array
     */
    public function create($data)
    {
        $this->title          = trim($data['title']);
        $this->status         = $data['status'];
        $this->type           = $data['type'];
        $this->user_id        = $data['user_id'];
        $this->section        = $data['section'];
        $this->type_id        = ArrayHelper::getValue($data, 'type_id', 0);
        $this->batch_id       = ArrayHelper::getValue($data, 'batch_id',  0);
        $this->comment_id     = ArrayHelper::getValue($data, 'comment_id', 0);
        $this->device_type_id = ArrayHelper::getValue($data, 'device_type_id', 0);
        $this->create_time    = date('Y-m-d H:i:s', time());
        $this->update_time    = date('Y-m-d H:i:s', time());
        if (!$this->save(false)) {
            $error = array_values($this->getFirstErrors())[0];
            return $this->arrData(100, $error);
        }
        return $this->arrData(200, '添加成功');
    }

    /**
     * 修改分组
     * @param $data
     * @return array
     * @throws \yii\base\Exception
     */
    public function edit($data)
    {
        $model                 = self::findOne($data['id']);
        $model->title          = trim($data['title']);
        $model->status         = $data['status'];
        $model->section        = $data['section'];
        $model->type           = $data['type'];
        $model->type_id        = ArrayHelper::getValue($data, 'type_id', 0);
        $model->batch_id       = ArrayHelper::getValue($data, 'batch_id',  0);
        $model->comment_id     = ArrayHelper::getValue($data, 'comment_id', 0);
        $model->device_type_id = ArrayHelper::getValue($data, 'device_type_id', 0);
        $model->update_time = date('Y-m-d H:i:s', time());
        if (!$model->save(false)) {
            $error = array_values($model->getFirstErrors())[0];
            return $this->arrData(100, $error);
        }
        return $this->arrData(200, '更新成功');
    }

    /**
     * 检查是否存在
     * @param $data
     * @return bool
     */
    public function check($data)
    {
        $query = self::find();
        $query->where(['type' => $data['type'],'section' => $data['section'],'status'=> self::STATUS_OPEN]);
        if ($query->one()) {
            return true;
        }
    }


}


