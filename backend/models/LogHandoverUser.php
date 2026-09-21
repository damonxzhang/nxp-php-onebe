<?php

namespace backend\models;

class LogHandoverUser extends Base
{
    const STATUS_OPEN  = 1;
    const STATUS_CLOSE = 2;

    //启用状态
    public static $statusArr = [
        self::STATUS_OPEN  => '启用',
        self::STATUS_CLOSE => '禁用',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_log_handover_user';
    }

    /**
     * 添加
     * @param $data
     * @return array
     */
    public function create($data)
    {
        $this->user_id      = $data['user_id'];
        $this->chinese_name = $data['chinese_name'];
        $this->pid          = $data['pid'];
        $this->shift        = $data['shift'];
        $this->section      = $data['section'];
        $this->status       = $data['status'];
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
        $model           = self::findOne($data['id']);
        $model->status   = $data['status'];
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
        $query->where(['!=', 'id', $data['id']]);
        $query->andFilterWhere(['user_id' => $data['user_id']]);
        if ($query->one()) {
            return false;
        }
        return true;
    }


}


