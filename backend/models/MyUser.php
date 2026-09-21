<?php
namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

//我们自定义自已的用户操作模型，需要实现IdentityInterface接口中的全部方法
//我们自定义的模型主要实现的是认证逻辑，而yii\web\User是负责管理用户认证状态的，两者是有区别的。
class MyUser extends ActiveRecord implements IdentityInterface
{
    public $auth_key;
    //指定操作的表名
    public static function tableName()
    {
        return 'nxp_user_info';
    }

    //通过ID，返回用户实例
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    //通过令牌，返回用户实例，一般用于无状态的restful应用
    //如果你的应用不需要用到，直接留空就行
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    //通过用户名，返回用户实例
    public static function findByUsername($id)
    {
        return static::findOne(['id' => $id]);
    }

    //获取用户ID
    public function getId()
    {
        return $this->id;
    }

    //获取用户认证密钥
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    //生成cookie中的authkey
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
        $this->save(false);
    }

    //验证用户认证密钥
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    //验证密码是否正确，当然我们也可以自已定义加密解密方式
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->pwd);
    }
}