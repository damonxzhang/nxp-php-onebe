<?php

namespace backend\models;

/**
 * This is the model class for table "nxp_approve_details".
 *
 * @property int $id
 * @property int $approve_id
 * @property int $user_id
 * @property int $status
 * @property string $reply
 * @property string $create_time
 */
class ApproveDetails extends Base
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
        return 'nxp_approve_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['approve_id', 'user_id', 'status'], 'integer'],
            [['create_time', 'reply'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'reply'       => '反馈内容',
            'approve_id'  => '关联ID',
            'user_id'     => '审批人ID',
            'status'      => '完成状态',
            'create_time' => '审批时间',
        ];
    }

    /**
     * 添加审批信息
     * @param $param
     * @return bool
     */
    public function create($param)
    {
        $this->approve_id  = $param['approve_id'];
        $this->user_id     = $param['user_id'];
        $this->status      = $param['status'];
        $this->reply       = $param['reply'];
        $this->create_time = date('Y-m-d H:i:s', time());
        if (!$this->save()) {
            return FALSE;
        }
        return $this->attributes['id'];
    }

}

