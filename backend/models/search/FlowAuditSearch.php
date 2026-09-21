<?php

namespace backend\models\search;

use backend\models\Base;
use backend\models\Flow;
use backend\models\FlowApproval;
use backend\models\FlowAudit;
use backend\models\FlowLine;
use backend\models\FlowNode;
use backend\models\User;
use backend\models\UserInfo;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class FlowAuditSearch extends FlowAudit
{
    public $title;
    public $name;
    public $flow_id;
    public $status;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['title', 'name', 'flow_id', 'status'], 'safe'],
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
        $uId   = (new Base())->getUid();
        $query = new Query();
        $query->select([
            'fad.id',
            'fad.approval_id',
            'fad.audit_info',
            'fad.status',
            'fad.create_time as audit_time',
            'fa.title',
            'fa.content_data',
            'fa.create_time',
            'u.chinese_name',
            'f.name as f_time',
        ]);
        $query->from(FlowAudit::tableName() . ' fad');
        $query->leftJoin(FlowApproval::tableName() . ' fa', 'fad.approval_id = fa.id');
        $query->leftJoin(Flow::tableName() . ' f', 'fad.flow_id = f.id');
        $query->leftJoin(UserInfo::tableName() . ' u', 'fad.user_id = u.id');
        $query->where(['fad.user_id' => $uId]);
        $query->andFilterWhere([
            'fad.status'  => $this->status,
            'fad.flow_id' => $this->flow_id,
        ]);
        $query->andFilterWhere(['like', 'u.name', trim($this->name)]);
        $query->andFilterWhere(['like', 'fa.title', trim($this->title)]);
        $query->orderBy(['fad.create_time' => SORT_DESC]);
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
            case FlowAudit::STATUS_WAIT:
                $str    = FlowAudit::$statusArr[$status]; //待办
                $result = '<button type="button" class="layui-btn layui-btn-xs layui-btn-warm">' . $str . '</button>';
                break;
            case FlowAudit::STATUS_SUCCESS:
                $str    = FlowAudit::$statusArr[$status]; //通过
                $result = '<button type="button" class="layui-btn layui-btn-xs">' . $str . '</button>';
                break;
            case FlowAudit::STATUS_REJECT:
                $str    = FlowAudit::$statusArr[$status]; //驳回
                $result = '<button type="button" class="layui-btn layui-btn-xs layui-btn-danger">' . $str . '</button>';
                break;
        }

        return $result;
    }

    /**
     * 获取审批流程
     * @param $approvalId
     * @return array
     */
    public function getAuditLine($approvalId)
    {
        if (empty($approvalId)) {
            return [];
        }
        $model       = FlowApproval::findOne(['id' => $approvalId]); //获取审批
        $flowLineArr = (new FlowLine())->getFlowLine($model->flow_id); //审批流程
        $data        = [];
        foreach ($flowLineArr as $nodeId) {
            $nodeMode        = FlowNode::findOne(['id' => $nodeId]); //获取审批节点
            $dataArr         = (new UserInfo())->getUserRoleName($nodeMode->user_id); //获取用户信息
            $approvalModel   = (new FlowAudit())->getAudit(['approval_id' => $approvalId, 'node_id' => $nodeId]);
            $dataArr['type'] = 0;
            if (!empty($approvalModel)) {
                $dataArr['type']        = 1;
                $dataArr['status']      = $approvalModel['status'];
                $dataArr['audit_info']  = $approvalModel['audit_info'];
                $dataArr['create_time'] = $approvalModel['create_time'];
            }
            $data[] = $dataArr;
        }
        return $data;
    }

    /**
     * 获取待审批数据
     * @param $id
     * @return array
     */
    public function getAuditData($id)
    {
        $model                  = FlowAudit::findOne(['id' => $id]); //获取审批
        $approvalModel          = FlowApproval::findOne(['id' => $model->approval_id]); //获取审批
        $dataArr['title']       = $approvalModel->title;
        $dataArr['content_data']= $approvalModel->content_data;
        $dataArr['status']      = $approvalModel->status;
        $dataArr['create_time'] = $approvalModel->create_time;
        $flowLineArr            = (new FlowLine())->getFlowLine($approvalModel->flow_id); //审批流程
        $dataArr['audit']       = [];
        foreach ($flowLineArr as $nodeId) {
            $nodeMode         = FlowNode::findOne(['id' => $nodeId]); //获取审批节点
            $result           = (new UserInfo())->getUserRoleName($nodeMode->user_id); //获取用户信息
            $getApprovalModel = (new FlowAudit())->getAudit(['approval_id' => $model->approval_id, 'node_id' => $nodeId]);
            if (empty($getApprovalModel)) {
                continue;
            }
            $result['status']      = $getApprovalModel['status'];
            $result['audit_info']  = $getApprovalModel['audit_info'];
            $result['create_time'] = $getApprovalModel['create_time'];
            $dataArr['audit'][]    = $result;
        }
        return $dataArr;
    }
}
