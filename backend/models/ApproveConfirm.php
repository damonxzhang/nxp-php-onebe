<?php

namespace backend\models;

use yii\db\Query;

/**
 * This is the model class for table "nxp_approve_confirm".
 *
 * @property int $id
 * @property int $approve_id
 * @property int $user_id
 * @property int $status
 * @property int $unusual
 * @property int $reason
 * @property string $create_time
 * @property string $confirm_time
 */
class ApproveConfirm extends Base
{
    const STATUS_NO  = 0; //未确认
    const STATUS_YES = 1; //已确认

    //确认状态
    public static $statusArr = [
        self::STATUS_NO  => '未确认',
        self::STATUS_YES => '已确认'
    ];

    const UNUSUAL_TRUE  = 0; //正常
    const UNUSUAL_FALSE = 1; //异常

    //是否正常
    public static $unusualArr = [
        self::UNUSUAL_TRUE  => '正常',
        self::UNUSUAL_FALSE  => '异常',
    ];

    //请假原因
    const REASON_REAL = 0; //正常
    const REASON_LEAVE = 1; //请假
    const REASON_SPECIAL = 2; //特殊情况
    public static $reasonArr = [
        self::REASON_REAL  => '正常',
        self::REASON_LEAVE  => '请假',
        self::REASON_SPECIAL => '特殊情况'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_approve_confirm';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['approve_id', 'user_id', 'status', 'unusual', 'reason'], 'integer'],
            [['create_time', 'confirm_time'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'           => 'ID',
            'approve_id'   => '关联ID',
            'user_id'      => '通知人id',
            'unusual'      => '是否异常',
            'reason'       => '异常原因',
            'status'       => '是否确认',
            'create_time'  => '创建时间',
            'confirm_time' => '确认时间',
        ];
    }


    /**
     * 添加信息
     * @param $param
     * @return bool
     */
    public function create($param)
    {
        $this->approve_id  = $param['approve_id'];
        $this->user_id     = $param['user_id'];
        $this->status      = $param['status'];
        $this->unusual     = $param['unusual'];
        $this->reason      = $param['reason'];
        $this->create_time = date('Y-m-d H:i:s', time());
        if (!$this->save()) {
            return FALSE;
        }
        return $this->attributes['id'];
    }

    /**
     * 签到
     * @param array $param
     * @return bool
     */
    public function signIn($param = []){
        $model = self::findOne(['user_id' => $param['user_id'], 'approve_id'=> $param['approve_id'], 'unusual' => self::UNUSUAL_TRUE]);
        if (!$model){
            return false;
        }
        $this->status = self::STATUS_NO;
        $this->confirm_time = date('Y-m-d H:i:s', time());
        if($this->save()){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 获取签到数组
     * @param $param
     * @return array
     */
    public function getSignPerson($param = [])
    {
        $query = new Query();
        $query->select(['ac.id', 'u.chinese_name', 'u.leader_uid', 'u.shift']);
        $query->from(self::tableName() . ' ac');
        $query->leftJoin(UserInfo::tableName() . ' u', 'ac.user_id = u.id');
        $query->where(['ac.approve_id' =>  $param['approve_id']]);
        $query->andWhere(['u.status' =>  1]);
        if (isset($param['status'])){
            $query->andWhere(['ac.status' => $param['status']]);
        }
        if (isset($param['unusual'])){
            $query->andWhere(['ac.unusual' => $param['unusual']]);
        }
        return $query->all();
    }


}


