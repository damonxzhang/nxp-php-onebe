<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "nxp_reain_vedio".
 *
 * @property int $id
 * @property string $name 视频名称
 * @property string $video_path 视频地址
 * @property int $type 类型
 * @property int $create_user 创建人
 * @property int $update_time
 * @property int $delete_time
 * @property int $create_time
 */
class TrainVideo extends Base
{
    //状态
    const STATUS_NO  = 0;
    const STATUS_YES = 1;

    static public $statusArr =[
        self::STATUS_YES => '开启',
        self::STATUS_NO  => '关闭'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_train_video';
    }


    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' =>'create_time',
                'updatedAtAttribute' =>'update_time',
                'value' => time()
            ]
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'video_path'], 'required'],
            [['create_user','type', 'status','update_time', 'create_time', 'delete_time'], 'integer'],
            [['name', 'video_path'], 'string'],
        ];
    }

    public static function getPathByids($ids){
        $data = self::find()->select('name,video_path')->asArray()->where(['id'=>$ids])->all();
        if($data){
            $newArray = array_column( $data,'video_path');
            return (new self)->decodeTrainCoursesFile($newArray);
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
            'name' => '名称',
            'type' => '类型',
            'status' => '状态',
            'video_path' => '路径',
            'create_user' => '创建人',
            'update_time' => '更新时间',
            'create_time' => '创建时间',
            'delete_time' => '删除时间',
        ];
    }
}
