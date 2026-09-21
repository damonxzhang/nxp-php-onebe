<?php

namespace backend\models;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "nxp_ir_judge".
 *
 * @property int $id
 * @property int|null $type 类型
 * @property int|null $status 状态
 * @property int|null $user_id 用户
 * @property int|null $create_time 创建时间
 * @property int|null $update_time 创建时间
 */
class IrJudge extends Base
{
    const TYPE_AUDIT    = 1;
    const TYPE_APPEAL   = 2;

    public static $types = [
        self::TYPE_AUDIT    => '评估员',
//        self::TYPE_APPEAL   => '申诉委员会'
    ];

    const STATUS_OFF    = 0;
    const STATUS_ON     = 1;

    public static $statusList = [
        self::STATUS_ON  => '开启',
        self::STATUS_OFF => '关闭',
    ];
    public function beforeSave($insert)
    {
        if($insert){
            $this->type = self::TYPE_AUDIT;
        }
        return parent::beforeSave($insert);
    }

    public function behaviors()
    {
        return [$this->getTimestampBehaviorArray()];
    }



    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_ir_judge';
    }

    /**
     * 是否是申诉员
     * @param $user_id
     * @return bool
     */
    public static function isAppealUser($user_id,$area = null){

        $result = self::find()->alias('i')
            ->innerJoin(['u' => UserInfo::tableName()],'i.user_id = u.id')
            ->where(['i.user_id' => $user_id,'i.status' => self::STATUS_ON])
            ->andFilterWhere(['u.area' => $area])
            ->exists();
        if( $result ){
            return true;
        }else{
            return false;
        }

    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = self::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $dataProvider;
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'status', 'user_id', 'create_time', 'update_time'], 'integer'],
        ];
    }


    public function getUserInfo(){
        return $this->hasOne(UserInfo::className(),['id' => 'user_id']);

    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => '类型',
            'status' => '状态',
            'user_id' => '用户',
            'create_time' => '创建时间',
            'update_time' => '修改时间',
        ];
    }
}
