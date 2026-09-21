<?php

namespace backend\models;

use yii\db\Query;

/**
 * This is the model class for table "nxp_train_check".
 *
 * @property int $id
 * @property int $user_id
 * @property int $train_id
 * @property int $status
 * @property string $create_time
 * @property string $check_time
 */

class TrainCheck extends Base
{
    //签到状态
    const STATUS_CHECKED   = 1; //已签到
    const STATUS_NO_SIGNED = 2; //未签到

    public static $statusArr = [
        self::STATUS_CHECKED   => '已签到',
        self::STATUS_NO_SIGNED => '未签到',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_train_check';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'train_id','status'], 'integer'],
            [['create_time', 'check_time'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'user_id'     => '需要培训用户ID',
            'train_id'    => '培训ID',
            'status'      => '签到状态',
            'create_time' => '创建时间',
            'check_time'  => '签到时间',
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
        $this->train_id    = $data['train_id'];
        $this->status      = self::STATUS_NO_SIGNED;
        $this->create_time = date('Y-m-d H:i:s', time());
        if (!$this->save()) {
            return false;
        }
        return true;
    }

    /**
     * 获取培训人员
     * @param $id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getTrainUserName($id)
    {
        $query = new Query();
        $query->select('u.chinese_name');
        $query->from(TrainCheck::tableName() . ' tc');
        $query->leftJoin(UserInfo::tableName() . ' u', 'tc.user_id = u.id');
        $query->where(['tc.train_id' => $id]);
        return $query->column();
    }

    /**
     * 获取签到人员名称
     * @param $id
     * @param $status
     * @return array
     */
    public function getSignPerson($id,$status)
    {
        $query = new Query();
        $query->select(['u.chinese_name', 'tc.check_time']);
        $query->from(TrainCheck::tableName() . ' tc');
        $query->leftJoin(UserInfo::tableName() . ' u', 'tc.user_id = u.id');
        $query->where(['tc.train_id' => $id]);
        $query->andWhere(['tc.status' => $status]);
        return $query->all();
    }

    /**
     * 获取培训人员ID
     * @param $id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getTrainUserId($id)
    {
        return TrainCheck::find()->select('user_id')->where(['train_id' => $id]) ->column();
    }
}
