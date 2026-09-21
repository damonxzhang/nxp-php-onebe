<?php
namespace backend\controllers;

use backend\models\UserInfo;
use Yii;
use yii\helpers\ArrayHelper;

class UserSyncBbsController extends \yii\web\Controller {

    /**
     * 同步用户信息到bbs表
     */
    public function actionSyncBbsUser(){

        $errorStr = [];
        $pass = md5('123456');

        $users = (new UserInfo())->getSyncUserByBbs();
        $ids =  ArrayHelper::getColumn($users,'id');
        $idStr = implode(',',$ids);
        $res = Yii::$app->bbsDb->createCommand('select nxpuid from {{%user}} where nxpuid in ('.$idStr.')')->queryAll();
        $resIds = ArrayHelper::getColumn($res,'nxpuid');
        try{
            array_map(function($user)use ($resIds,$pass,&$errorStr){
                return $this->setAttrByBbsUser($user,$resIds,$pass,$errorStr);
            } ,$users);

        }catch (\Exception $e){
            echo $e->getMessage()."<br />";die;
        }
        $str = '';
        if( !empty($errorStr) && count($errorStr)){
            foreach ($errorStr as $v){
                $str .= $v."<br />";
//                fwrite($errorLog,date('Y-m-d H:i:s').$str);
            }
        }
        echo  $str;
    }

    /**
     * 保存用户
     * @param $user
     * @param $resIds
     * @param $pass
     * @param $errorStr
     * @return bool
     */
    public function setAttrByBbsUser($user,$resIds,$pass,&$errorStr){

        if(in_array($user['id'],$resIds)){
            return false;
        }else{
            $pattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/";
            if(empty($user['email']) || !preg_match($pattern, $user['email'], $matches)){

                $errorStr[] = '用户'.$user['chinese_name'].'邮箱格式错误或未输入邮箱!';
                return false;
            }

            if($this->emailExist($user['email'])){
                $user['email'] = $user['email'].'.'.$user['id'];
            }
            $ip = ip2long(Yii::$app->request->userIP);
            $salt = $this->xn_rand();
            $time = time();

            $param = [
                'userName'      => $user['name'],
                'email'         => $user['email'],
                'password'      => md5($pass.$salt),
                'salt'          => $salt,
                'gid'           => 101,
                'create_ip'     => $ip,
                'create_date'   => $time,
                'logins'        => 1,
                'login_date'    => $time,
                'login_ip'      => $ip,
                'nxpuid'        => $user['id']
            ];

            $resultNum = Yii::$app->bbsDb->createCommand()->insert('{{%user}}', $param)->execute();
            if($resultNum === false){
                $errorStr[] = '用户'.$user['chinese_name'].'保存失败!';
            }
        }
    }

    // 随机字符
    function xn_rand($n = 16) {
        $str = '23456789ABCDEFGHJKMNPQRSTUVWXYZ';
        $len = strlen($str);
        $return = '';
        for($i=0; $i<$n; $i++) {
            $r = mt_rand(1, $len);
            $return .= $str[$r - 1];
        }
        return $return;
    }

    /**
     * 邮箱是否重复
     * @param $email
     * @return bool
     */
    public function emailExist($email){

        $res = Yii::$app->bbsDb->createCommand('select uid from bbs_user where email = :email')->bindParam(':email',$email)->queryOne();
        if($res){
            return true;
        }else{
            return false;
        }
    }

}