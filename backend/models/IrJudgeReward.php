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
class IrJudgeReward extends Base
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
        return 'nxp_ir_judge_reward';
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
            [['type', 'status','area', 'user_id', 'create_time', 'update_time'], 'integer'],
        ];
    }


    public function getUserInfo(){
        return $this->hasOne(UserInfo::className(),['id' => 'user_id']);

    }
    /**
     * @param $user_id
     * @return bool
     */
    public static function isRewardUser($user_id,$area = null){
        $result = self::find()->where(['user_id' => $user_id,'status' => self::STATUS_ON])->andFilterWhere(['area' => $area])->exists();
        if($result){
            return true;
        }else{
            return false;
        }

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
            'area' => '区域',
            'user_id' => '用户',
            'create_time' => '创建时间',
            'update_time' => '修改时间',
        ];
    }
}
