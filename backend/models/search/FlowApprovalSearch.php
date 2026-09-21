<?php

namespace backend\models\search;

use backend\models\Base;
use backend\models\FlowApproval;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class FlowApprovalSearch extends FlowApproval
{
    public $status;
    public $flow_id;
    public $start_time;
    public $end_time;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['status', 'flow_id', 'start_time', 'end_time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * 数据查询
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $this->setAttributes($params);
        $startCreateTime = ArrayHelper::getValue($this, 'start_time');
        $endCreateTime   = ArrayHelper::getValue($this, 'end_time');
        $uId             = (new Base())->getUid();
        $query           = FlowApproval::find();
        $query->where(['user_id' => $uId]);
        $query->andFilterWhere([
            'status'  => $this->status,
            'flow_id' => $this->flow_id,
        ]);
        $query->andFilterWhere(['>=', 'create_time', $startCreateTime]);
        $query->andFilterWhere(['<=', 'create_time', $endCreateTime]);
        $query->orderBy(['id' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }

    /**
     * 获取审批状态
     * @param $status
     * @return string
     */
    public function getStatus($status)
    {
        $result = '';
        switch ($status) {
            case FlowApproval::STATUS_DRAFT:
                $str    = FlowApproval::$statusArr[$status]; //草稿
                $result = '<button type="button" class="layui-btn layui-btn-xs layui-btn-normal">' . $str . '</button>';
                break;
            case FlowApproval::STATUS_APPROVAL:
                $str    = FlowApproval::$statusArr[$status]; //审批中
                $result = '<button type="button" class="layui-btn layui-btn-xs layui-btn-warm">' . $str . '</button>';
                break;
            case FlowApproval::STATUS_SUCCESS:
                $str    = FlowApproval::$statusArr[$status]; //成功
                $result = '<button type="button" class="layui-btn layui-btn-xs">' . $str . '</button>';
                break;
            case FlowApproval::STATUS_REJECT:
                $str    = FlowApproval::$statusArr[$status];  //驳回
                $result = '<button type="button" class="layui-btn layui-btn-xs layui-btn-danger">' . $str . '</button>';
                break;
        }

        return $result;
    }
}

