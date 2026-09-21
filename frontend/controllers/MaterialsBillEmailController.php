<?php

namespace frontend\controllers;

use backend\models\UserInfo;
use Yii;

class MaterialsBillEmailController extends FrontendBaseController
{

    /**
     * 物料单生成6小时没有结束，报警大小材料
     */
    public function actionMaterials()
    {
        // 执行原生SQL命令
        $query = Yii::$app->db->createCommand("SELECT * FROM nxp_shelf_materials_bill WHERE TIMEDIFF( NOW(), create_time ) > '06:00:00' and `status` = 1");
        $materialsBillData = $query->queryAll();
        if(empty($materialsBillData)){
            return false;
        }
        foreach ($materialsBillData as $value) {
            $create_user = UserInfo::getUserEmailAndName($value['user_id']);
            if (empty($create_user['email'])) continue;
            $body =  "<html><body><div>Hello ".$create_user['chinese_name']."</div><h2 style='color: red;'>物料单：" . $value['document_no'] . "已经超过6小时未能完成，请及时完成并更新状态</h2><div>创建时间: " . $value['create_time'] . "</div></body></html>";
            $mail = Yii::$app->mailer->compose();
            $mail->setHtmlBody($body);
            $mail->setTo($create_user['email']); //要发送给那个人的邮箱
            $mail->setSubject('Handover Record Overdue Reminder'); //邮件主题		
            $mail->send();
        }
        return true;
    }

    /**
     * 早晚7点半检查是否有未完成的物料单，报警大小材料
     */
    public function actionCheckMaterials()
    {
        // 执行原生SQL命令
        $query = Yii::$app->db->createCommand("SELECT * FROM nxp_shelf_materials_bill WHERE `status` = 1");
        $materialsBillData = $query->queryAll();
        if(empty($materialsBillData)){
            return false;
        }
        foreach ($materialsBillData as $value) {
            $create_user = UserInfo::getUserEmailAndName($value['user_id']);
            if (empty($create_user['email'])) continue;
            $body =  "<html><body><div>Hello ".$create_user['chinese_name']."</div><h2 style='color: red;'>物料单：" . $value['document_no'] . "未完成，请及时完成并更新状态</h2></body></html>";
            $mail = Yii::$app->mailer->compose();
            $mail->setHtmlBody($body);
            $mail->setTo($create_user['email']); //要发送给那个人的邮箱
            $mail->setSubject('Handover Record Overdue Reminder'); //邮件主题		
            $mail->send();
        }
        return true;
    }
  
}