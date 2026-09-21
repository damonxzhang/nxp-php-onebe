<?php

namespace backend\models;

use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "nxp_maintain_feedback".
 *
 * @property int $id
 * @property int|null $m_id 异常
 * @property int|null $type 类型
 * @property int|null $feedback_type 反馈类型
 * @property int|null $feedback_user 反馈用户
 * @property int|null $other_feedback 其他反馈类型
 * @property int|null $action 行动类型
 * @property string|null $supplement 行动补充说明
 * @property int|null $other_action 其他行动
 * @property int|null $solve_status 解决状态
 * @property int|null $predict_date 预计完成时间
 * @property int|null $complete_date 实际完成时间
 * @property int|null $create_time 反馈时间
 * @property int|null $update_time 更新时间
 */
class MaintainFeedback extends \backend\models\Base
{

    /**
     * 行动事项
     */
    const SOLVE_ACTION      = 1;
    const CHANGE_ACTION     = 2;
    const HARDWARE_ACTION   = 3;
    const SOFTWARE_ACTION   = 4;
    const OTHER_ACTION      = 5;

    public static $actionList = [
        self::SOLVE_ACTION    => '及时修复',
        self::CHANGE_ACTION   => '更换配件后及时修复',
        self::HARDWARE_ACTION => '临时解决，后续完善硬件',
        self::SOFTWARE_ACTION => '临时解决，后续完善软件',
        self::OTHER_ACTION    => '其他',
    ];
    public $rejects;

    // const SOLVE_STATUS_NonStart  = 2;
    const SOLVE_STATUS_DONE= 3;
    const SOLVE_STATUS_YES = 1;
    const SOLVE_STATUS_NO  = 2;
    
    public static $solveStatusList = [
        // self::SOLVE_STATUS_NonStart=>'Non-Start'
        self::SOLVE_STATUS_DONE => 'Pending',
        self::SOLVE_STATUS_YES  => 'Done',
        self::SOLVE_STATUS_NO   => 'Ongoing'
        
    ];

    const TYPE_SEND_EMAIL = 1;
    const TYPE_NORMAL     = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_maintain_feedback';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['predict_date','predict_date','solve_status'],'required'],
            [['m_id', 'type', 'feedback_type', 'feedback_user','rejects', 'solve_status', 'predict_date', 'complete_date', 'create_time', 'update_time'], 'integer'],
            [['other_feedback', 'other_action','supplement'],'string']
        ];
    }

    /**
     * 查询按时间完成的反馈
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getTimeOutFeedback(){
        $time = time();
        return self::find()->alias('mf')
            ->innerJoin(['m' => Maintain::tableName()],'mf.m_id = m.id')
            ->andWhere(['mf.solve_status' => self::SOLVE_STATUS_NO,'mf.type' => self::TYPE_NORMAL])
            ->andWhere(['<=','mf.predict_date',$time])
            ->andWhere(['m.status' => Maintain::STATUS_MARCH])
            ->asArray()
            ->all();
    }


    /**
     * 保存反馈
     * @return bool
     */
    public function saveFeedback($m_id){
        $this->feedback_user = Yii::$app->user->identity->user_id;
        $this->create_time = time();
        $this->m_id = $m_id;
        $this->predict_date = strtotime($this->predict_date);
        !empty($this->complete_date) && $this->complete_date = strtotime($this->complete_date);
        return $this->save();
    }

    /**
     * 用户是否完成反馈
     * @param $m_id
     * @return bool
     */
    public static function isFinishFeedback(Maintain $maintain){

        $query = self::find();
        $query->where(['m_id' => $maintain->id]);
        $query->andWhere(['feedback_user' => Yii::$app->user->identity->user_id]);
        $query->andWhere(['in', 'solve_status', [self::SOLVE_STATUS_YES,self::SOLVE_STATUS_DONE]]);

        if($maintain->feedback_user == $maintain->other_feedback_user){
            if($query->count() == 2){
                return '已完成反馈';
            }
            return false;
        }

        if($query->exists()){
            return '已经反馈';
        }
        return false;
    }

    public function updateTypeBySendEmail($id){
        self::updateAll(['type' => self::TYPE_SEND_EMAIL],['id' => $id]);
    }

    /**
     * 检查是否全部反馈
     * @param $feedbackUser
     * @param $otherFeedbackUser
     * @return bool
     */
    public function checkAllFinishFeedback($feedbackUser,$otherFeedbackUser){
        $feedback_users =  $this->getFinishFeedbackUser($this->m_id);
        if(!$feedback_users){
            return false;
        }
        //反馈用户
        if(!empty($feedbackUser) && !in_array($feedbackUser,$feedback_users)){
            return false;
        }
        //其他反馈用户
        if(!empty($otherFeedbackUser) && !in_array($otherFeedbackUser,$feedback_users)){
            return false;
        }
        //所以反馈用户
        if(!empty($otherFeedbackUser) && !empty($feedbackUser) && (!in_array($otherFeedbackUser,$feedback_users) || !in_array($feedbackUser,$feedback_users) || count($feedback_users) != 2)){
            return false;
        }
        return true;
    }

    public function getMaintain(){
        return $this->hasOne(Maintain::className(),['id' => 'm_id']);
    }

    /**
     * 根据异常获取所有已完成反馈的用户
     * @param $m_id
     * @return array
     */
     public function getFinishFeedbackUser($m_id){
        $query = self::find();
        $query->select('feedback_user');
        $query->where(['m_id' => $m_id]);
        $query->andWhere(['in', 'solve_status', [self::SOLVE_STATUS_YES,self::SOLVE_STATUS_DONE]]);
        return $query->column();
    }

    /**
     * 获得创建反馈的model
     * @param Maintain $maintain
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function getCreateModel(Maintain $maintain){
        $model = self::find()->where(['m_id' => $maintain->id,'feedback_user' => Yii::$app->user->identity->user_id]);
        if($maintain->feedback_user == $maintain->other_feedback_user && $model->count() < 2){
            $model->andWhere(['solve_status' => self::SOLVE_STATUS_NO]);
        }
        $dataModel = $model->one();
        if(!$dataModel){
            return new self();
        }
        return $dataModel;
    }

    /**
     * 转换时间格式
     */
    public function replaceTime(){
        !empty($this->predict_date)  && $this->predict_date = date('Y-m-d',$this->predict_date);
        !empty($this->complete_date) && $this->complete_date = date('Y-m-d',$this->complete_date);
        if(empty($this->create_time)){
            $this->create_time = time();
        }
        $this->create_time = date('Y-m-d',$this->create_time);
    }

    /**
     * 获取异常反馈用户
     * @param $m_id
     * @return array
     */
    public static function getFeedbackUserIdByMain($m_id){
        return self::find()->select('feedback_user,solve_status')->where(['m_id' => $m_id])->asArray()->all();
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'm_id' => '异常',
            'type' => '类型',
            'feedback_type' => '反馈类型',
            'rejects' => '次品数量',
            'feedback_user' => '反馈用户',
            'other_feedback' => '其他反馈类型',
           // 'action' => '行动',
            'supplement' => '行动内容补充说明',
          //  'other_action' => '其他行动',
            'solve_status' => '完成状态',
            'predict_date' => '预计完成时间',
            'complete_date' => '实际完成时间',
            'create_time' => '反馈时间',
            'update_time' => '更新时间',
        ];
    }
}
