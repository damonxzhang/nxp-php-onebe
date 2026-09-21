<?php

namespace backend\models;


/**
 * This is the model class for table "nxp_flow_approval".
 *
 * @property int $id 路由表
 * @property int $user_id
 * @property string $title
 * @property string $content_data
 * @property int $flow_id
 * @property int $node_id
 * @property int $status
 * @property string $create_time 创建时间
 */

class FlowApproval extends Base
{
    const STATUS_DRAFT    = 0; //草稿
    const STATUS_APPROVAL = 1; //审批中
    const STATUS_SUCCESS  = 2; //审批通过
    const STATUS_REJECT   = 3; //审批驳回

    public static $statusArr = [
        self::STATUS_DRAFT    => '草稿',
        self::STATUS_APPROVAL => '审批中',
        self::STATUS_SUCCESS  => '已批准',
        self::STATUS_REJECT   => '被驳回',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_flow_approval';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'flow_id', 'node_id', 'status'], 'integer'],
            [['title', 'content_data', 'create_time'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'user_id'     => '用户 ID',
            'title'       => '审批标题',
            'content_data'=> '审批内容',
            'flow_id'     => '流程ID',
            'node_id'     => '当前节点ID',
            'status'      => '审批状态',
            'create_time' => '创建时间',
        ];
    }

    /**
     * 添加数据
     * @param $data
     * @return bool
     */
    public function create($data)
    {
        $this->user_id     = $data['user_id'];
        $this->title       = $data['title'];
        $this->content_data= $data['content_data'];
        $this->flow_id     = $data['flow_id'];
        $this->node_id     = $data['status'];
        $this->status      = $data['status'];
        $this->create_time = date('Y-m-d H:i:s', time());
        if (!$this->save()) {
            return false;
        }
        return $this->attributes['id'];
    }

}
