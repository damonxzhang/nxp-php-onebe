<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "nxp_flow_line".
 *
 * @property int $id
 * @property int $flow_id
 * @property int $prev_node_id
 * @property int $next_node_id
 */
class FlowLine extends Base
{
    const LINE_NODE_ID = 0; //流程起始和截止值
    const LINE_NODE_OTHER = 99999999; //流程常量用于标识是否被驳回
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_flow_line';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['flow_id', 'prev_node_id', 'next_node_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'           => 'ID',
            'flow_id'      => '流程id',
            'prev_node_id' => '上一节点id',
            'next_node_id' => '下一节点id',
        ];
    }

    /**
     * 添加数据
     * @param $data
     * @return array
     */
    public function create($data)
    {
        $this->flow_id      = $data['flow_id'];
        $this->prev_node_id = $data['prev_node_id'];
        $this->next_node_id = $data['next_node_id'];
        if (!$this->save()) {
            $error = array_values($this->getFirstErrors())[0];
            return $this->arrData(100, $error);
        }
        return $this->arrData(200, '添加成功');
    }

    public function edit($data)
    {
        $model               = self::findOne($data['id']);
        $model->flow_id      = $data['flow_id'];
        $model->prev_node_id = $data['prev_node_id'];
        $model->next_node_id = $data['next_node_id'];
        if (!$model->save()) {
            $error = array_values($model->getFirstErrors())[0];
            return $this->arrData(100, $error);
        }
        return $this->arrData(200, '更新成功');
    }

    /**
     * 根据流程ID获取流程线数组
     * @param $flowId
     * @return array
     */
    public function getFlowLine($flowId)
    {
        $query = self::find();
        $query->select(['next_node_id']);
        $query->where(['flow_id' => $flowId]);
        $query->andWhere(['>', 'next_node_id', self::LINE_NODE_ID]);
        $query->orderBy(['id' => SORT_ASC]);
        return $query->column();
    }
}
