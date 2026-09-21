<?php

namespace frontend\controllers;

use backend\models\ChargingUserInfo;
use backend\models\ExternalSystem;
use Yii;
use backend\models\Role;
use backend\models\User;
use backend\models\UserInfo;
use yii\helpers\ArrayHelper;

class UserInfoApiController extends FrontendBaseController
{

    /**
     * 用户信息接口
     * 请求方式 POST
     */
    public function actionBaseInfo()
    {
        $post = $this->validateRequest();
        if(empty($post['wbi_code'])){
            return $this->outputJson('false', 'wbi_code不能为空!');
        }
        $userInfo = UserInfo::findOne(['wbi_code' => $post['wbi_code']]);
        if (!$userInfo) {
            return $this->outputJson('false', '无用户信息!');
        }


        $data = [
            'chinese_name'        => $userInfo->chinese_name,
            'shift'               => ArrayHelper::getValue(UserInfo::$shiftArr, $userInfo->shift, ''),
            'leader_chinese_name' => $this->getLeaderChineseName($userInfo->leader_uid),
            'wbi_code'            => $userInfo->wbi_code,
            'email'               => $userInfo->email,
            'section'             => $userInfo::$sectionArr[$userInfo->section],
            'position'            => $userInfo::$positionArr[$userInfo->position],
        ];
        //获取成功
        return $this->outputJson('true', '获取成功!', $data);
    }

    /**
     * 登陆接口
     * 请求方式 POST
     */
    public function actionLogin()
    {
        //检查请求方式
        $isPost = Yii::$app->request->isPost;
        if (!$isPost) {
            return $this->outputJson('false', '请求方式不正确!');
        }
        //检查请求参数
        $post = Yii::$app->request->post();

        if (empty($post['choose'])) {
            return $this->outputJson('false', 'choose不能为空!');
        }
        if (empty($post['system'])) {
            return $this->outputJson('false', 'system不能为空!');
        }
        //判断登陆方式
        if ($post['choose'] == 1) {
            if (empty($post['wbi_code'])) {
                return $this->outputJson('false', 'wbi_code不能为空!');
            }

            if (empty($post['password'])) {
                return $this->outputJson('false', 'password不能为空!');
            }

            $user = User::find()->alias('u')
                ->select('u.password,ui.*')
                ->innerJoin(['ui' => UserInfo::tableName()], 'u.user_id = ui.id')
                ->where(['ui.wbi_code' => $post['wbi_code']])
                ->asArray()
                ->one();
            if (empty($user) || empty($user['password'])) {

                return $this->outputJson('false', '用户不存在!');
            }

            $res = Yii::$app->security->validatePassword($post['password'], $user['password']);

            if (!$res) {
                return $this->outputJson('false', '登陆密码不正确!');
            }


            if ($user['status'] == UserInfo::STATUS_CLOSE) {
                return $this->outputJson('false', '该用户已被禁用!');
            }
        } elseif ($post['choose'] == 2) {

            if (empty($post['badge_code'])) {
                return $this->outputJson('false', 'badge_code不能为空!');
            }

            $user = UserInfo::findOne(['badge_code' => $post['badge_code']]);

            if (!$user) {
                return $this->outputJson('false', '无用户信息!');
            }
        }

        //获取权限
        $permission = [];
        if ($user['role_id']){
            $roleQuery = Role::findOne(['id' => $user['role_id']]);
            $permission = $roleQuery->permission;
        }
        $authoritys = (new ExternalSystem())->getAllAuthorityBySystem($post['system'],$user['id']);

        if(empty($authoritys)){
            return $this->outputJson('false', '没有权限!');
        }
        //拼接数据
        $data = [
            'permission'        => $permission,
            'email'             => $user['email'],
            'pid'               => $user['pid'],
            'uid'               => $user['id'],
			'chinese_name'      => $user['chinese_name'],
            'english_name'      => $user['english_name'],
            'position'          => ArrayHelper::getValue(UserInfo::$positionArr, $user['position'], ''),
            'shift'             => ArrayHelper::getValue(UserInfo::$shiftListArr, $user['shift'], ''),
            'area'              => ArrayHelper::getValue(UserInfo::$areaArr, $user['area'], ''),
            'station_id'        => $user['station_id'],
            'sfc_number'        => $user['sfc_number'],
            'fsl_number'        => $user['fsl_number'],
            'meal_card_code'    => $user['meal_card_code'],
            'wbi_code'          => $user['wbi_code'],
            'badge_code'        => $user['badge_code'],
            'skill'             => $user['skill'],
            'department_number' => $user['department_number'],
            'section'           => ArrayHelper::getValue(UserInfo::$sectionArr, $user['section'], ''),
            'authoritys'          => $authoritys
        ];
        return $this->outputJson('true', '权限获取成功!', $data);

    }


      /**
     * 登陆接口
     * 请求方式 POST
     */
    public function actionLoginuser()
    {
        //检查请求方式
        $isPost = Yii::$app->request->isPost;
        if (!$isPost) {
            return $this->outputJson('false', '请求方式不正确!');
        }
        //检查请求参数
        $post = Yii::$app->request->post();

        if (empty($post['choose'])) {
            return $this->outputJson('false', 'choose不能为空!');
        }
        if (empty($post['system'])) {
            return $this->outputJson('false', 'system不能为空!');
        }
        if (empty($post['badge_code'])) {
            return $this->outputJson('false', 'badge_code不能为空!');
        }

        $user = UserInfo::findOne(['badge_code' => $post['badge_code']]);

        if (!$user) {
            return $this->outputJson('false', '无用户信息!');
        }
        //拼接数据
        $data = [
            'email'             => $user['email'],
            'pid'               => $user['pid'],
            'uid'               => $user['id'],
			'chinese_name'      => $user['chinese_name'],
            'english_name'      => $user['english_name'],
            'position'          => ArrayHelper::getValue(UserInfo::$positionArr, $user['position'], ''),
            'shift'             => ArrayHelper::getValue(UserInfo::$shiftListArr, $user['shift'], ''),
            'area'              => ArrayHelper::getValue(UserInfo::$areaArr, $user['area'], ''),
            'station_id'        => $user['station_id'],
            'sfc_number'        => $user['sfc_number'],
            'fsl_number'        => $user['fsl_number'],
            'meal_card_code'    => $user['meal_card_code'],
            'wbi_code'          => $user['wbi_code'],
            'badge_code'        => $user['badge_code'],
            'skill'             => $user['skill'],
            'department_number' => $user['department_number'],
            'section'           => ArrayHelper::getValue(UserInfo::$sectionArr, $user['section'], '')
        ];
        return $this->outputJson('true', '登录成功!', $data);

    }

    /**
     * JAVA登陆接口
     * 请求方式 POST
     */
    public function actionJavaLogin()
    {
        //检查请求方式
        $isPost = Yii::$app->request->isPost;
        if (!$isPost) {
            return $this->outputJson('false', '请求方式不正确!');
        }
        //检查请求参数
        $post = Yii::$app->request->post();

        $data = [];

        if ($post['choose'] == 1){

            if(empty( $post['pid'])){
                return $this->outputJson('false', 'pid不能为空!');
            }

            $user = User::find()->alias('u')
                ->select('u.password,u.name as name,ui.*,ui.chinese_name as chinese_name,ui.id as u_id')
                ->innerJoin(['ui' => UserInfo::tableName()],'u.user_id = ui.id')
                ->where(['ui.pid' => $post['pid']])
                ->asArray()
                ->one();

            if (empty($user)) {

                return $this->outputJson('false', '用户不存在!');
            }

            $data['pid'] = $post['pid'];
            $data['user_name'] = trim($user['name']);
            $data['chinese_name'] = trim($user['chinese_name']);
            $data['id'] = $user['u_id'];
            $data['area'] = $user['area'];

        }elseif ($post['choose'] == 2){

            if(empty( $post['user_name'])){
                return $this->outputJson('false', 'user_name不能为空!');
            }

            $user = User::find()->alias('u')
                ->select('u.password,ui.*,ui.chinese_name as chinese_name,ui.id as u_id')
                ->innerJoin(['ui' => UserInfo::tableName()],'u.user_id = ui.id')
                ->where(['u.name' => $post['user_name']])
                ->asArray()
                ->one();

            if (empty($user)) {

                return $this->outputJson('false', '用户不存在!');
            }

            $data['user_name'] = $post['user_name'];
            $data['pid'] = $user['pid'];
            $data['id'] = $user['u_id'];
            $data['area'] = $user['area'];
            $data['chinese_name'] = trim($user['chinese_name']);

        }

        if(empty( $post['password'])){
            return $this->outputJson('false', 'password不能为空!');
        }

        $res = Yii::$app->security->validatePassword($post['password'], $user['password']);

        if (!$res) {
            return $this->outputJson('false', '登陆密码不正确!');
        }

        if ($user['status'] == UserInfo::STATUS_CLOSE) {
            return $this->outputJson('false', '该用户已被禁用!');
        }

        return $this->outputJson('true', '登录成功!',$data);
    }

    /**
     * 获取leader 姓名
     * @param $leaderUid
     * @return string
     */
    public function getLeaderChineseName($leaderUid)
    {
        if(empty($leaderUid)){
            return '';
        }
        $query = UserInfo::findOne(['id' => $leaderUid]);
        return empty($query) ? '' : $query->chinese_name;
    }

}

