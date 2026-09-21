<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "nxp_boss_feedback".
 *
 * @property int $id
 * @property int|null $m_id 异常
 * @property int|null $feedback_user 反馈人
 * @property int|null $type 类型
 * @property int|null $action 行动
 * @property string|null $other_action 其他行动
 * @property int|null $solve_status 解决状态
 * @property int|null $predict_date 预计完成时间
 * @property int|null $complete_date 实际完成时间
 * @property int|null $sendMail 是否发送邮件提醒
 * @property int|null $create_time 创建时间
 * @property int|null $update_time 更新时间
 * @property string|null $files 附件
 */
class BossFeedback extends \backend\models\Base
{

    /**
     *@property int|null 反馈人反馈类型
     */
    const FEEDBACK_TYPE = 1;

    /**
     * @property int|null 反馈人主管反馈
     */
    const LEADER_TYPE   = 2;

    const SOLVE_STATUS_YES = 1;
    const SOLVE_STATUS_NO  = 2;

    public static $solveStatusList = [
        self::SOLVE_STATUS_YES => '完全解决--Done',
        self::SOLVE_STATUS_NO => '临时解决，后续需完善--Ongoing'
    ];

    public $rejects;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_boss_feedback';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['action','predict_date','predict_date','solve_status'],'required'],
            [['m_id', 'feedback_user', 'type', 'action', 'rejects', 'solve_status', 'predict_date', 'complete_date', 'sendMail', 'create_time', 'update_time'], 'integer'],
            [['other_action','files'], 'string', 'max' => 255],
        ];
    }

    /**
     * 保存反馈
     * @param $m_id
     * @return bool
     */
    public function newFeedback($m_id){
        $this->feedback_user = Yii::$app->user->identity->user_id;
        $this->create_time = time();
        $this->m_id = $m_id;
        $this->predict_date = strtotime($this->predict_date);
        !empty($this->complete_date) && $this->complete_date = strtotime($this->complete_date);
        if (!empty($this->files)) {
            $this->files        = implode(',', $this->files);
        }
        return $this->save();
    }

    public static function getTypes($m_id){
        return self::find()->select('type')->where(['m_id' => $m_id,'solve_status' => self::SOLVE_STATUS_YES])->groupBy('type')->column();
    }

    public static function feedbackExistByType($m_id,$type){
        return self::find()->where(['m_id' => $m_id,'type' => $type])->exists();
    }

    /**
     * 查询反馈超时的用户
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function unfinishedFeedback(){
        return self::find()->alias('bf')
            ->select('bf.*,bm.create_user,bm.number,bm.things')
            ->innerJoin(['bm' => BossMaintain::tableName()],'bf.m_id = bm.id')
            ->where(['bf.sendMail' => self::SELECT_NO])
            ->andWhere(['<','bf.predict_date',time()])
            ->andWhere([ 'bf.solve_status' => self::SOLVE_STATUS_NO])
            ->andWhere(['bm.status' => BossMaintain::STATUS_MARCH])
            ->asArray()->all();
    }

    /**
     * 获得反馈类型
     * @param BossMaintain $bossMaintain
     */
    public function getFeedbackType(BossMaintain $bossMaintain){
        $user = Yii::$app->user->identity->user_id;
        $isLider = $bossMaintain->isLeader($user);
        //是待反馈人和主管
        if($bossMaintain->head_user == $user || $bossMaintain->feedback_user == $user){
            if($isLider && $this->userTypeExists($bossMaintain->id,self::FEEDBACK_TYPE)){
                $this->type = self::LEADER_TYPE;
            }else{
                $this->type = self::FEEDBACK_TYPE;
            }

        //区域负责人
        }elseif($isLider){

            $this->type = self::LEADER_TYPE;
        }else{
            $this->addError('head_user','设备错误');
            return false;
        }
        return true;
    }

    /**
     * 批量有个反馈邮件发送结果
     * @param $id
     * @return int
     */
    public static function changeSendMailById($id){
        return self::updateAll(['sendMail' => self::SELECT_YES],['id' => $id]);
    }

    public function userTypeExists($m_id,$type){
        return self::find()->where(['m_id' => $m_id,'type' => $type])->exists();
    }

    public function getMaintain(){
        return $this->hasOne(BossMaintain::className(),['id' => 'm_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'm_id' => Yii::t('app', '异常'),
            'feedback_user' => Yii::t('app', '反馈用户'),
            'type' => Yii::t('app', '类型'),
            'action' => Yii::t('app', '行动'),
            'other_action' => Yii::t('app', '其他行动'),
            'solve_status' => Yii::t('app', '解决状态'),
            'predict_date' => Yii::t('app', '预计完成时间'),
            'complete_date' => Yii::t('app', '实际完成时间'),
            'rejects' => Yii::t('app', '次品数量'),
            'sendMail' => Yii::t('app', 'Send Mail'),
            'create_time' => Yii::t('app', '反馈时间'),
            'update_time' => Yii::t('app', 'Update Time'),
            'files' => Yii::t('app', '附件'),
        ];
    }
}
