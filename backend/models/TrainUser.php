<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "nxp_train_user".
 *
 * @property int $id
 * @property int $user_id 用户
 * @property int $shift_id 班别
 * @property int $status 状态
 * @property int $train_id 培训
 * @property int $courses_id 课程
 * @property int $sign_in 是否签到
 * @property int $study_date 学习时长
 * @property int $promise    承诺
 * @property int $study_type 学习类型
 * @property int $study_start_time  开始学习时间
 * @property int $study_end_time    结束学习时间
 * @property int $create_time
 * @property int $update_time
 */
class TrainUser extends Base
{
     const SIGN_IN_YES = 1;
     const SIGN_IN_NO  = null;

     public static $sign_ins = [
        self::SIGN_IN_YES => '已签到',
        self::SIGN_IN_NO  => '未签到'
     ];

     const STATUS_NORMAL = 1;
     const STATUS_LEAVE = 2;
     const STATUS_SPECIAL = 3;

     public static $statusArr = [
        self::STATUS_NORMAL => '正常',
        self::STATUS_LEAVE => '请假',
        self::STATUS_SPECIAL=> '特殊情况'
     ];

     const STUDY_TYPE = 1;
     const REVIEW_TYPE = 2;
     const LESSONS_TYPE = 3;

     static public $studyTypes = [
        self::STUDY_TYPE => '集中授课',
        self::REVIEW_TYPE => '自习',
        self::LESSONS_TYPE => '补课'
     ];


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_train_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'shift_id','promise','study_type','study_start_time','study_end_time', 'train_id','status', 'courses_id', 'sign_in', 'create_time', 'update_time'], 'integer'],
            [['study_date'],'safe']
        ];
    }


    public function behaviors()
    {
        return [
            $this->getTimestampBehaviorArray()
        ];
    }
    /**
     * 保存学员信息
     * @param array $userIds
     * @param $tainId
     * @return bool
     */
    public function saveCourseUsers(array $userIds,Train $train){
        //保存学员
        if($this->eachSaveNewParams($userIds,$train->id,$train->courses)){

            return true;
        }else{
            throw new Exception('服务器错误！');
        }
    }

    /**
     * 更新学员信息
     * @param array $userIds
     * @param Train $train
     * @return bool
     * @throws Exception
     */
    public function updateCourseUsers(array $userIds,Train $train){
        //修改或删除数旧数据
        $userIds = $this->screenUser($train,$userIds);
        //如果学员没有变动
        if($userIds === true) return true;

        //保存新用户
        if( $this->eachSaveNewParams($userIds,$train->id,$train->courses)){

            return true;
        }else{
            throw new Exception('服务器错误！');
        }
    }

    /**
     * 循环保存学员信息
     * @param $userIds
     * @param $trainId
     * @param $courses
     * @return bool
     * @throws Exception
     */
    public function eachSaveNewParams($userIds,$trainId,$courses){

        if(!empty($userIds)) {
            foreach ($userIds as $value) {
                $model = new self;
                if ($model) {
                    $model->setAttributes([
                        'user_id' => $value,
                        'courses_id' => $courses,
                        'train_id' => $trainId,
                        'status' => self::STATUS_NORMAL
                    ]);
                    if (!$model->save()) {
                        throw new Exception($model->errorToString());
                    }
                }
            }
        }
        return true;
    }
    /**
     * 修改或删除旧数据
     * @param $trainId
     * @param $userIds
     * @return array
     * @throws Exception
     */
    public function screenUser($train,$userIds){

        $oldUserIds = self::find()->where(['train_id' => $train->id])->select('id,user_id')->asArray()->all();

        if($oldUserIds){
            $oldUserIds = ArrayHelper::map($oldUserIds,'id','user_id');

            foreach ($userIds as $key => $value){
                //存在则更新
                if(in_array($value,$oldUserIds)){

                    $id = array_search($value,$oldUserIds);
                    $res = self::updateAll([
                        'courses_id' => $train->courses,
                    ],'id = '.$id);
                    if($res === false){
                        throw new Exception('学员数据更新失败！');
                    }
                    //剔除已更新的数据
                    unset($oldUserIds[$id]);
                    unset($userIds[$key]);
                }
            }
            //有数据说明被删除
            if(!empty($oldUserIds)){

                //批量删除学员
                $result = self::deleteAll(['id' => array_keys($oldUserIds)]);

                if($result !== count($oldUserIds)){
                    throw new Exception('学员数据更新失败！');
                }
            }

            if(empty($userIds) && empty($oldUserIds)){

                return true;
            }
        }
        return $userIds;
    }

    /**
     * 查询学员班别
     * @param $train_id
     * @return array|bool
     */
    public function getUserShiftsList($train_id){
        $data = $this->getShift($train_id);
        if(!empty($data)){
             $resArray = array_map(function($column){
                return UserInfo::$shiftArr[$column];
            },$data);
            if($resArray){
                return implode(' ',$resArray);
            }else{
                return false;
            }
        }
    }

    /**
     * 用户班别
     * @param $train_id
     * @return array
     */
    public function getShift($train_id){
        return self::find()->alias('tu')->select('ui.shift')
            ->innerJoin(['ui' => UserInfo::tableName()],'tu.user_id = ui.id')
            ->where(['tu.train_id' => $train_id])
            ->groupBy('ui.shift')
            ->column();
    }

    /**
     * 用户信息
     * @param $train_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function TrainUserInfo($train_id){
        return self::find()->alias('tu')->select('tu.*,ui.chinese_name')
            ->innerJoin(['ui' => UserInfo::tableName()],'tu.user_id = ui.id')
            ->where(['tu.train_id' => $train_id])
            ->asArray()
            ->orderBy('update_time desc')
            ->all();
    }

    /**
     *  签到
     * @return bool
     */
    public function signIn(){
        $courses = TrainCourses::findOne($this->courses_id);

        $this->sign_in = self::SIGN_IN_YES;
        $this->study_type = self::STUDY_TYPE;
        $this->study_date = $courses->duration;
        $this->study_start_time = time();
        $this->study_end_time = time();
        if($this->save()){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 根据培训获得学员
     * @param $train_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getAllUserIdsByTrain($train_id){
        return $this->find()->select('user_id')->where(['train_id' => $train_id])->column();

    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'shift_id' => 'Shift ID',
            'train_id' => 'Train ID',
            'status' => 'status',
            'courses_id' => 'Courses ID',
            'sign_in' => 'Sign In',
            'study_date' => '学习时长',
            'promise' => '承诺',
            'study_type' => '学习类型',
            'study_start_time' => '学习开始时间',
            'study_end_time' => '学习结束时间',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
