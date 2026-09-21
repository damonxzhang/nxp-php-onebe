<?php

namespace backend\models;

class LogHandoverCommentType extends Base
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
        return 'nxp_log_handover_comment_type';
    }

    /**
     * 添加
     * @param $data
     * @return array
     */
    public function create($data)
    {
        $this->title      = trim($data['title']);
        $this->status     = $data['status'];
        $this->user_id    = $data['user_id'];
        $this->section    = $data['section'];
        $this->is_audit   = $data['is_audit'];
        $this->create_time = date('Y-m-d H:i:s', time());
        $this->update_time = date('Y-m-d H:i:s', time());
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
        $model->title    = trim($data['title']);
        $model->status   = $data['status'];
        $model->section  = $data['section'];
        $model->is_audit = $data['is_audit'];
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
        $query->where(['!=', 'id', $data['id']]);
        $query->andFilterWhere(['title' => $data['title'],'section' => $data['section']]);
        if ($query->one()) {
            return false;
        }
        return true;
    }


}


