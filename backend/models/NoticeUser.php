<?php

namespace backend\models;
use yii\db\Query;


/**
 * This is the model class for table "nxp_notice_user".
 *
 * @property int $id
 * @property int $notice_id
 * @property int $user_id
 * @property int $status
 * @property int $connect_id
 * @property string $create_time 创建时间
 * @property string $notice_time 创建时间
 */
class NoticeUser extends Base
{
    const STATUS_READ   = 1;
    const STATUS_UNREAD = 2;

    //启用状态
    public static $statusArr = [
        self::STATUS_READ   => '已读',
        self::STATUS_UNREAD => '未读',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_notice_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['notice_id', 'user_id', 'status','connect_id'], 'integer'],
            [['create_time','notice_time'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'notice_id'   => '消息ID',
            'user_id'     => '用户ID',
            'status'      => '状态',
            'connect_id'  => '关联id',
            'create_time' => '创建时间',
            'notice_time' => '查阅时间',
        ];
    }

    /**
     * 添加通知
     * @param $data
     * @return array
     */
    public function create($data)
    {
        $this->notice_id   = $data['notice_id'];
        $this->user_id     = $data['user_id'];
        $this->status      = $data['status'];
        $this->create_time = date('Y-m-d H:i:s', time());
        if (!$this->save()) {
            $error = array_values($this->getFirstErrors())[0];
            return $this->arrData(100, $error);
        }
        return $this->arrData(200, '添加成功');
    }

    /*
     * 获取签到人员名称
     * @param $id
     * @param $status
     * @return array
     */
    public function getNoticeSignPerson($id,$status)
    {
        $query = new Query();
        $query->select(['u.chinese_name', 'nu.notice_time']);
        $query->from(self::tableName() . ' nu');
        $query->leftJoin(UserInfo::tableName() . ' u', 'nu.user_id = u.id');
        $query->where(['nu.notice_id' => $id,'nu.status' => $status]);
        return $query->all();
    }

    /**
     * 检查查阅状态是否全部查阅
     * @param $id
     * @return bool
     */
    public function checkNoticeStatus($id)
    {
        $query = self::find();
        $query->where(['notice_id' => $id, 'status' => self::STATUS_UNREAD]);
        $result = $query->all();
        if($result){
            return true;
        }
        return false;
    }

    /**
     * 批量更新消息为已读
     * @param array $param
     * @return bool
     * @throws \yii\db\Exception
     */
    public function updateStatus($param = [])
    {
        $query = self::find();
        $query->select('id');
        $query->where([
            'user_id' => $param['user_id'],
            'connect_id' => $param['connect_id'],
            'status' => self::STATUS_UNREAD
        ]);
        $search = $query->column();
        if($search){
            \Yii::$app->db->createCommand()->update(self::tableName(), ['status' => self::STATUS_READ], ['in','id', $search])->execute();
        }
        return true;
    }

}


