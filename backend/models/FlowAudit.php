<?php

namespace backend\models;

/**
 * This is the model class for table "nxp_flow_audit".
 *
 * @property int $id
 * @property int $flow_id
 * @property int $approval_id
 * @property int $node_id
 * @property int $user_id
 * @property int $status
 * @property string $audit_info
 * @property string $create_time
 * @property string $update_time
 */
class FlowAudit extends Base
{

    const STATUS_WAIT    = 0; //待办
    const STATUS_SUCCESS = 1; //通过
    const STATUS_REJECT  = 2; //驳回

    public static $statusArr = [
        self::STATUS_WAIT    => '待办',
        self::STATUS_SUCCESS => '通过',
        self::STATUS_REJECT  => '驳回',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_flow_audit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['approval_id', 'node_id', 'user_id', 'status', 'flow_id'], 'integer'],
            [['audit_info', 'create_time', 'update_time'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'flow_id'     => '审批流程id',
            'approval_id' => '待审批表id',
            'node_id'     => '审批节点id',
            'user_id'     => '审批人id',
            'status'      => '审批状态',
            'audit_info'  => '审批意见',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }

    /**
     * 添加数据
     * @param $data
     * @return bool
     */
    public function create($data)
    {
        $this->approval_id = $data['approval_id'];
        $this->flow_id     = $data['flow_id'];
        $this->node_id     = $data['node_id'];
        $this->user_id     = $data['user_id'];
        $this->status      = $data['status'];
        $this->audit_info  = '';
        $this->create_time = date('Y-m-d H:i:s', time());
        if (!$this->save()) {
            return false;
        }
        return true;
    }

    /**
     * 更新数据
     * @param $param
     * @return bool
     */
    public function edit($param)
    {
        $model = self::findOne(['id' => $param['id']]);
        $model -> status = $param['status'];
        $model -> audit_info = trim($param['audit_info']);
        $model -> update_time = date('Y-m-d H:i:s', time());
        if (!$model->save()) {
            return false;
        }
        return true;
    }

    /**
     * 根据节点及申请ID获取审批数据
     * @param $param
     * @return array|null|\yii\db\ActiveRecord
     */
    public function getAudit($param)
    {
        $where = [
            'and',
            ['=', 'approval_id', $param['approval_id']],
            ['=', 'node_id', $param['node_id']],
            ['>', 'status', self::STATUS_WAIT]
        ];
        return FlowAudit::find()->where($where)->one();
    }
}
