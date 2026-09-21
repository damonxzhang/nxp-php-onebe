<?php

namespace backend\models;

use Mpdf\Tag\Em;
use phpDocumentor\Reflection\Types\Self_;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "nxp_ir_user".
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $ir_id IR
 * @property int $type 类型
 * @property int $master 主次负责人/实施人
 * @property int $create_time 创建时间
 * @property int $update_time 修改时间
 */
class IrUser extends Base
{
    const HEAD_TYPE = 1;
    const WORK_TYPE = 2;

    public static $types = [
        self::HEAD_TYPE => '负责人',
        self::WORK_TYPE => '实施人'
    ];

    const MASTER_YES = 1;
    const MASTER_NO  = 0;
    public static $masterArr = [
        self::MASTER_YES => '主要',
        self::MASTER_NO  => '非主要'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_ir_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'ir_id', 'type', 'master', 'create_time', 'update_time'], 'integer'],
        ];
    }

    public function behaviors()
    {
        return [
            $this->getTimestampBehaviorArray()
        ];
    }

    /**
     * 根据ir获得主要负责人id
     * @param $irId
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function findMasterUserId($irId,$type){
        return self::find()->select('user_id')->where(['ir_id' => $irId,'master' => IrUser::MASTER_YES,'type' => $type])->limit(1)->one();
    }
    /**
     *
     * 根据类型获取用户信息
     * @param $ir_id
     * @param $type
     * @return array|bool
     */
    public function getUserInfo($ir_id,$type){
        return self::find()->alias('i')->select('i.*,u.chinese_name,u.email,u.area')
            ->innerJoin(['u' => UserInfo::tableName()],'i.user_id = u.id')
            ->where(['ir_id' => $ir_id , 'type' => $type])
            ->asArray()
            ->all();
    }

    /**
     * 查询主要用户
     * @param $ir_id
     * @param $type
     * @return array|\yii\db\ActiveRecord|null
     */
    public static function getMasterUserInfo($ir_id,$type){
        return self::find()->alias('i')
            ->innerJoin(['u' => UserInfo::tableName()],'i.user_id = u.id')
            ->select('u.*')
            ->where(['ir_id' => $ir_id,'master' => IrUser::MASTER_YES,'type' => $type])
            ->asArray()
            ->one();
    }

    /**
     * 保存或更新Ir用户信息
     * @param $ids
     * @param $irId
     * @param $type
     * @return bool
     * @throws \yii\db\Exception
     */
    public function saveUsers($master_id,$ids,$irId,$type){

        if(!$this->saveMasterUser($master_id,$irId,$type)) {
            return false;
        }
        if(!empty($ids) ){
            //删除原有数据

            self::deleteAll(['ir_id' => $irId,'type' => $type, 'master' => self::MASTER_NO  ] );
            $params = [];
            foreach ($ids as $key => $val){
                if($val == $master_id || empty($val)){
                    continue;
                }
                $params[] = [
                    'user_id' => $val,
                    'ir_id' => $irId,
                    'type' => $type,
                    'master' => self::MASTER_NO,
                    'create_time' => time()
                ];
            }

            Yii::$app->db->createCommand()->batchInsert(self::tableName(),['user_id', 'ir_id', 'type', 'master', 'create_time'],$params)->execute();
            return true;
        }
    }

    public static function getIrUserName($ir_id,$type){

        $name = self::find()->alias('iu')
                ->select('u.chinese_name')
                ->innerJoin(['u' => UserInfo::tableName()],'iu.user_id = u.id')
                ->where(['master' => self::MASTER_YES,'type' => $type,'ir_id' => $ir_id])
                ->asArray()
                ->column();
        if(!empty($name)){
            return $name[0];
        }
    }

    /**
     * 保存主负责人、实施人
     * @param $master_id
     * @param $type
     * @return bool
     */
    public function saveMasterUser($master_id,$irId,$type){
        if(empty($master_id)){
            return true;
        }
        $model = self::findOne(['ir_id' => $irId, 'master' => self::MASTER_YES,'type' => $type]);
        //如果存在并无修改
        if($model && $model->user_id == $master_id){
            return true;

        //如果存在并修改
        }elseif($model && $model->user_id != $master_id){
            $model->user_id = $master_id;

        //不存在
        }else{
            $model = new self;
            $model->setAttributes([
                'user_id' => $master_id,
                'ir_id' => $irId,
                'type' => $type,
                'master' => self::MASTER_YES,
            ]);
        }
        return $model->save();
    }

    /**
     * 登录用户是否是主负责人、实施人
     * @param $ir_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function selectIrAllMaster($ir_id){
        return self::find()->where([
            'ir_id' => $ir_id,
            'master'=> self::MASTER_YES,
            'user_id' => Yii::$app->user->identity->user_id
        ])->select('user_id,type')->all();
    }

    /**
     * 查询主要负责人
     * @param $ir_id
     * @param $type
     * @return array|\yii\db\ActiveRecord|null
     */
    public function selectWorkMaster($ir_id,$type){
        $chinese_name = self::find()->alias('i')
            ->innerJoin(['iu' => UserInfo::tableName()],'i.user_id = iu.id')
            ->where([
            'ir_id' => $ir_id,
            'master'=> self::MASTER_YES,
            'type' => $type
        ])->select('iu.chinese_name')->asArray()->limit(1)->one();
        if($chinese_name){
            return $chinese_name['chinese_name'];
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户',
            'ir_id' => 'IR',
            'type' => '类型',
            'master' => '主要',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
