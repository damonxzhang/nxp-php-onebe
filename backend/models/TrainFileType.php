<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "nxp_train_file_type".
 *
 * @property int $id
 * @property string|null $name 类型名称
 * @property int|null $type 类型
 * @property int|null $create_user 创建人
 * @property int|null $status 状态
 * @property int|null $create_time
 * @property int|null $update_time
 * @property int|null $delete_time
 */
class TrainFileType extends Base
{
    //分类
    const TYPE_PPT   = 1;
    const TYPE_VIDEO = 2;

    public static $types = [
        self::TYPE_PPT => '课件',
        self::TYPE_VIDEO => '视频'
    ];

    //状态
    const STATUS_YES = 1;
    const STATUS_NO  = 0;

    public static $statusArr = [
        self::STATUS_YES => '开启',
        self::STATUS_NO  => '关闭'
    ];

    public function behaviors()
    {
        return [
             $this->getTimestampBehaviorArray()
        ];
    }

    public function beforeSave($insert)
    {
        if($insert){
            $this->create_user = Yii::$app->user->identity->user_id;
        }
        return parent::beforeSave($insert);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_train_file_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name','type'],'required'],
            [['type', 'status',  'create_user', 'create_time', 'update_time'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['name'],'repeat']
        ];
    }

    /**
     * 验证相同类型下的名称
     * @param $attribute
     * @param $params
     * @return bool
     */
    public function repeat($attribute,$params){
        $query = self::find()->where(['name' => $this->$attribute,'type' => $this->type,'create_user' => Yii::$app->user->identity->user_id]);

        if(!$this->isNewRecord){
            $query->andWhere(['!=','id',$this->id]);
        }
        $exists = $query->exists();
        if($exists){
            $this->addError($attribute,'分类名称已存在！');
            return false;
        }else{
            return true;
        }
    }

    /**
     * 根据分类类型获得分类
     * @param $type
     * @return array|bool
     */
    public static function getTypes($type){
        $types = self::find()->select('id,name')->where(['type' => $type,'status' => self::STATUS_YES,'create_user' => Yii::$app->user->identity->user_id])->asArray()->all();
        if($types){
            return ArrayHelper::map($types,'id','name');
        }
        return [];
    }

    /**
     * 查询分类名称
     * @param $id
     * @return bool
     */
    public static function getTypeName($id){
        $types = self::find()->select('name')->where(['id' => $id])->column();
        if(!empty($types[0])){
            return $types[0];
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
            'name' => '分类名称',
            'create_user' => '创建人',
            'type' => '类型',
            'status' => '状态',
            'create_time' => 'Create Date',
            'update_time' => 'Update Date',
        ];
    }
}
