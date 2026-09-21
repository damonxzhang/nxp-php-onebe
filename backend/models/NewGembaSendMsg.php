<?php
namespace backend\models;

use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class NewGembaSendMsg extends NewGemba{

    const AUDIT_TYPE = 1;
    const AWAIT_WORK_TYPE = 2;
    const CREATE_CONFIRM_TYPE = 3;
    const LEADER_STEPS_TYPE = 4;

    public $email_title = 'New GEMBA';
    public $emailView = '@backend/views/new-gemba/email.php';
    public $gemModel;

    /**
     * 与步骤对应的处理方法
     * @var array
     */
    public static $funcBySteps = [
        NewGemba::CREATE_STEPS          => 'audit',//待审核人审核
        NewGemba::ASSESS_STEPS          => 'work',//待实施人实施
        NewGemba::CONFIRM_STEPS         => 'crateConfirm',//待创建人确认
        NewGemba::LEADER_STEPS          => 'leaderConfirm',//主管确认完成
    ];
    /**
     * 与步骤对应的操作时间的键
     * @var array
     */
    public static $actionTimeKey = [
        NewGemba::CREATE_STEPS          => 'create_time',//待审核人审核
        NewGemba::ASSESS_STEPS          => 'assess_time',//待实施人实施
        NewGemba::CONFIRM_STEPS         => 'update_time',//待创建人确认
        NewGemba::LEADER_STEPS          => 'update_time',//主管确认完成
    ];
    /**
     * 类型对应步骤
     * @var array
     */
    public static $TypeEqSteps = [
        self::AUDIT_TYPE            => NewGemba::CREATE_STEPS,//待审核人审核
        self::AWAIT_WORK_TYPE       => NewGemba::ASSESS_STEPS,//待实施人实施
        self::CREATE_CONFIRM_TYPE   => NewGemba::WORK_STEPS,//待创建人确认
        self::LEADER_STEPS_TYPE     => NewGemba::LEADER_STEPS,//主管确认完成
    ];

    /**
     * 处理数据
     * @param $data
     */
    public function setEmailParams($data){
        $emailParams = call_user_func( [self::className() ,self::$funcBySteps[$data["steps"]]]  , $data);
        if (empty($emailParams['email'])){
            return '未输入邮箱！';
        }

        $user = UserInfo::find()->select('chinese_name,email')->where(['email' => $emailParams['email'],'status'=>1])->asArray()->one();
        if(!$user)
            return true;
            
        if($this->sendEmail($emailParams,$data)){
            $model = new SendGembaEmailLog();

            if($model->saveSendEmailLog($data)){

                echo '发送成功！';
                return true;
            }else{
                echo $model->errorToString();
                return false;
            }
        }

        echo '发送异常！';
        return false;

    }
    //待审核人审核
    public static function audit($data){
        $userModel = new UserInfo();
        $area = '';
        $query = GembaChangeArea::findOne(['gemba_id' => $data['id']]);
        if ($query){
            $area = $query->new_area;
        }else{
            $create_user = $userModel->find()
                ->select('area')
                ->where(['id' => $data['create_user']])
                ->limit(1)
                ->one();
            $area = $create_user->area;
        }
        $users = $userModel->find()->select('u.id,u.email')->alias('u')
            ->innerJoin(['j' => Judge::tableName()],'j.user_id = u.id')
            ->where(['j.type' => Judge::TYPE_GEMBA,'j.status' => Judge::STATUS_YES])
            ->andWhere(['u.area' => $area])
            ->asArray()->all();
        $params = ArrayHelper::map($users,'id','email');
        return [
            'email' => array_values($params),
            'name' => '审核员'
        ];
    }

    //待创建人确认
    public function crateConfirm($data){
        $work_user = UserInfo::find()->select('chinese_name,email')->where(['id' => $data['create_user']])->asArray()->one();
        return [
            'email' => $work_user['email'],
            'name' => $work_user['chinese_name']
        ];

    }

    //待实施人确认
    public static function work($data){
        $work_user = UserInfo::find()->select('chinese_name,email')->where(['id' => $data['work_user']])->asArray()->one();
        return [
            'email' => $work_user['email'],
            'name' => $work_user['chinese_name']
        ];
    }

    //主管确认完成
    public static function leaderConfirm($data){
        $work_user = UserInfo::find()
            ->select('chinese_name,email')
            ->where(new Expression('id = (select leader_uid from '.UserInfo::tableName() .' where id ='.$data['create_user'].')'))
            ->asArray()
            ->one();
        return [
            'email' => $work_user['email'],
            'name' => $work_user['chinese_name']
        ];
    }

    /**
     * 发送邮件
     * @return bool
     */
    public function sendEmail($emailParas,$data){
        try{
            if(!empty($emailParas['email'])){

                $mail = Yii::$app->mailer->compose($this->emailView,['data' => $data,'name' => $emailParas['name']]);

                $mail->setTo($emailParas['email']);

                $mail->setSubject($this->email_title);
                if($mail->send()){
                    return true;
                }else{
                    echo '发送失败！';
                    return false;
                }
            }
        }catch (\Exception $e){
            echo $e->getMessage();
            return false;
        }
    }


}