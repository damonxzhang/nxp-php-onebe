<?php

namespace backend\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "nxp_user".
 *
 * @property string $id
 * @property int $user_id
 * @property string $name 用户名
 * @property string $password 密码
 * @property int $create_time 创建时间
 */
class User extends Base implements IdentityInterface
{

    const LOGIN_TYPE_ID     = 1;//账号登录
    const LOGIN_TYPE_CARD   = 2; //识别证登录
    const LOGIN_TYPE_PID    = 3; //PID登录

    const STATUS_OPEN  = 1;
    const STATUS_CLOSE = 2;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_user';
    }

    public function testInvoke($params,UserInfo $obj){

    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['name', 'password','create_time'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'user_id'     => '用户信息表ID',
            'name'        => '用户名',
            'password'    => '密码',
            'create_time' => '创建时间',
        ];
    }

    /**
     * @param $data
     * @return array
     * @throws \yii\base\Exception
     */
    public function create($data)
    {
        $model             = self::findOne(['name' => trim($data['name'])]);
        if($model){
            return $this->arrData(100, '用户名已存在请重新输入!');
        }
        $this->name        = trim($data['name']);
        $this->password    = Yii::$app->security->generatePasswordHash(trim($data['password']));
        $this->user_id     = $data['user_id'];
        $this->create_time = date('Y-m-d H:i:s', time());
        if (!$this->save()) {
            $error = array_values($this->getFirstErrors())[0];
            return $this->arrData(100, $error);
        }
        return $this->arrData(200, '添加成功');
    }

    /**
     * @param $data
     * @return array
     * @throws \yii\base\Exception
     */
    public function edit($data)
    {
        $model       = self::findOne($data['id']);
        $name        = trim($data['name']);
        if($name != $model->name){
            $quest  = self::findOne(['name' => trim($data['name'])]);
            if($quest){
                return $this->arrData(100, '用户名已存在请重新输入!');
            }
        }
        $model->name = trim($data['name']);
        if ($data['password']) {
            $model->password = Yii::$app->security->generatePasswordHash($data['password']);
        }
        if (!$model->save()) {
            $error = array_values($model->getFirstErrors())[0];
            return $this->arrData(100, $error);
        }
        return $this->arrData(200, '更新成功');
    }

//    public function getRole()
//    {
//        return $this->hasOne(Role::className(), ['id' => 'role_id']);
//    }

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     * The space of such keys should be big enough to defeat potential identity attacks.
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        // TODO: Implement getAuthKey() method.
    }

    /**
     * Validates the given auth key.
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @param string $authKey
     * @return bool|void
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        // TODO: Implement validateAuthKey() method.
    }

    /**
     * 根据用户ID获取用户名称
     * @param $id
     * @return string
     */
    public function getUserName($id)
    {
        $query = self::findOne(['id' => $id]);
        return empty($query) ? '' : $query->name;
    }

    /**
     * 获取交接人ID和名字
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getConnectUserList()
    {
        $query = self::find();
        $query->select(['id', 'name']);
        $query->where(['status' => self::STATUS_OPEN]);
        $query->andWhere(['>', 'id', 1]);
        $query->asArray();
        return $query->all();
    }

    /**
     * 获取用户及角色信息
     * @return array|yii\db\ActiveRecord[]
     */
    public function getUserRoleData()
    {
        $query = self::find();
        $query->select(['nxp_user.id', 'nxp_user.name', 'r.name as role_name']);
        $query->leftJoin(Role::tableName() . ' r', 'r.id = nxp_user.role_id');
        $query->where(['nxp_user.status' => self::STATUS_OPEN]);
        $query->asArray();
        return $query->all();
    }

    /**
     * 获取用户角色名称和角色名称
     * @param string $userId
     * @return array|yii\db\ActiveRecord[]
     */
    public function getUserRoleName($userId)
    {
        $query = self::find();
        $query->select(['nxp_user.name', 'r.name as role_name']);
        $query->leftJoin(Role::tableName() . ' r', 'r.id = nxp_user.role_id');
        $query->andWhere(['nxp_user.id' => $userId]);
        $query->asArray();
        return $query->one();
    }

    public function getUserInfo(){
        return $this->hasOne(UserInfo::className(),['id' => 'user_id']);
    }

    /**
     * 查询用户信息
     * @param bool $select
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getAllUserInfo(array $ids = [],$select = false){
        $query = self::find()->alias('U');
        if($select){
            $query->select($select);
        }
        $query->innerJoin(['UI' => UserInfo::tableName()],'U.user_id = UI.id');
        $query->filterWhere(['UI.id' => $ids]);
        return $query->asArray()->all();
    }
}
