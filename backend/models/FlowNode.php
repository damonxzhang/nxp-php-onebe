<?php

namespace backend\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "nxp_flow_node".
 *
 * @property int $id
 * @property int $flow_id
 * @property int $user_id
 */
class FlowNode extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_flow_node';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['flow_id', 'user_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'      => 'ID',
            'flow_id' => '审批id',
            'user_id' => '流程审批者ID',
        ];
    }

    /**
     * 添加数据
     * @param $data
     * @return array
     */
    public function create($data)
    {
        $this->flow_id = $data['flow_id'];
        $this->user_id = $data['user_id'];
        if (!$this->save()) {
            $error = array_values($this->getFirstErrors())[0];
            return $this->arrData(100, $error);
        }
        return $this->arrData(200, '添加成功');
    }

    /**
     * 获取流程节点名称
     * @param $flowId
     * @return array|yii\db\ActiveRecord[]
     */
    public function getIdListByFlowId($flowId)
    {
        $query = self::find();
        $query->select('id');
        $query->where(['flow_id' => $flowId]);
        $query->orderBy(['id' => SORT_ASC]);
        return $query->column();
    }

    /**
     * 获取审批人员列表
     * @param $flowId
     * @return string
     */
    public function getUserName($flowId)
    {
        $query = new Query();
        $query->select(['ui.chinese_name']);
        $query->from(self::tableName() . ' fn');
        $query->leftJoin(UserInfo::tableName() . ' ui', 'fn.user_id = ui.id');
        $query->where(['fn.flow_id' => $flowId]);
        $query->orderBy(['fn.id' => SORT_ASC]);
        $data = $query->column();
        return empty($data) ? '' : implode(',', $data);

    }
}
