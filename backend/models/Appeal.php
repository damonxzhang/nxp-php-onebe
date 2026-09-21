<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "nxp_appeal".
 *
 * @property int $id
 * @property int $relation_id
 * @property string $content 申诉内容
 * @property int $type 申诉类型
 * @property int $status 申诉状态
 * @property string $appeal_message 申诉消息
 * @property int $appeal_user 申诉人
 * @property int $audit_user 审核人
 * @property int $complete_date 完成时间
 * @property int $create_time 创建时间
 * @property int $update_time 更新时间
 */
class Appeal extends Base
{
    /**
     * 申诉类
     */
    const IR_MODEL = 'backend\modules\Appeal\models\IrAppeal';


    /**
     * 申诉类型
     */
    const IR_DIRECTOR_TYPE = 1;
    const IR_ASSESS_TYPE = 2;
    const IR_HEAD_TYPE = 3;
    const IR_WORK_TYPE = 4;
    const IR_CONFIRM_TYPE = 5;

    /**
     * @var array
     */
    public static $typeName = [
        self::IR_DIRECTOR_TYPE  => 'IR主管拒绝申诉',
        self::IR_ASSESS_TYPE    => 'IR评估员拒绝申诉',
        self::IR_HEAD_TYPE      => 'IR负责人申诉',
        self::IR_WORK_TYPE      => 'IR实施人申诉',
        self::IR_CONFIRM_TYPE   => 'IR确认人申诉',
    ];

    const STATUS_APPLY  = 1;
    const STATUS_AGREED = 2;
    const STATUS_REFUSED = 3;

    public static $status = [
        self::STATUS_APPLY => '申请中',
        self::STATUS_AGREED=> '接受',
        self::STATUS_REFUSED=> '不接受'
    ];

    public static $selectList = [
        self::STATUS_AGREED=> '接受',
        self::STATUS_REFUSED=> '不接受'
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_appeal';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'content', 'audit_user'],'required'],
            [['content', 'appeal_message'], 'string'],
            [['type', 'status', 'audit_user','appeal_user', 'relation_id', 'complete_date', 'create_time', 'update_time'], 'integer'],
        ];
    }

    public function behaviors()
    {
        return [
            $this->getTimestampBehaviorArray()
        ];
    }
    //获取申诉人名称
    public function getUserName($ir_id, $type){
        return Appeal::find()->alias('a')
            ->leftJoin(['u' => UserInfo::tableName()],'a.audit_user = u.id')
            ->select('u.chinese_name')
            ->where(['a.relation_id' => $ir_id])
            ->andWhere(['a.type' => $type])
            ->andWhere(['a.status' => self::STATUS_APPLY])
            ->scalar();
    }
    /**
     * 查询申诉和审核人姓名
     * @param $ir_id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function getAppealAndUserName($ir_id){
        return Appeal::find()->alias('a')
            ->leftJoin(['u' => UserInfo::tableName()],'a.audit_user = u.id')
            ->select('a.*,u.chinese_name')
            ->where(['a.relation_id' => $ir_id])
            ->asArray()
            ->orderBy('a.id desc')
            ->limit(1)
            ->one();
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'relation_id' => '关联ID',
            'content' => '申诉内容',
            'type' => '类型',
            'status' => '状态',
            'appeal_message' => '申诉消息',
            'appeal_user' => '申诉人',
            'audit_user' => '审核人',
            'complete_date' => '完成时间',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
