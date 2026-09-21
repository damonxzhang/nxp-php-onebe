<?php
namespace backend\modules\MaintainApi\models;

use yii\base\BaseObject;
use yii\base\ExitException;

class SendEmail extends BaseObject {

    const CREATE_EMAIL_VIEW = 'maintain/create-remind-email-view';
    const TIMING_EMAIL_VIEW = 'maintain/timing-remind-email-view';
    const TIMEOUT_TIMING_EMAIL_VIEW = 'maintain/feedback-timeout-remind-email-view';
    const BOSS_CREATE_EMAIL_VIEW = 'boss-maintain/create-remind-remind-email-view';
    const BOSS_TIMING_EMAIL_VIEW = 'boss-maintain/feedback-timeout-remind-email-view';


    public $view;//邮件模板
    public $subject;//标题
    public $to;//收件人
    public $cc;//抄送人
    public $renderParams;//选软视图的参数

    protected $mail;

    public function init()
    {
        if (empty($this->view)){
            throw new ExitException('邮件视图必填！');
        }
        if (empty($this->to)){
            throw new ExitException('收件人必填！');
        }
        $this->setMailAttr();
    }

    protected function setMailAttr(){
        $this->mail = \Yii::$app->mailer->compose($this->view,['params' => $this->renderParams]);
        $this->mail->setTo($this->to);
        if(!empty($this->subject)){
            $this->mail->setSubject(trim($this->subject));
        }

        if(!empty($this->cc)){
            $this->mail->setCc($this->cc);
        }
    }

    public function send(){
        return $this->mail->send();
    }
}