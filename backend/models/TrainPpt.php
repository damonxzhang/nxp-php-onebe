<?php

namespace backend\models;

use phpDocumentor\Reflection\Types\Parent_;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "nxp_reain_ppt".
 *
 * @property int $id
 * @property string $name
 * @property string $path 文件路径
 * @property int $type 课件类型
 * @property int $status 状态
 * @property int $create_user
 * @property string $comment
 * @property int $create_time
 * @property int $update_time
 * @property int $delete_time

 */
class TrainPpt extends Base
{
    //状态
    const STATUS_NO  = 0;
    const STATUS_YES = 1;
    const STATUS_DIE = 2;//删除

    static public $statusArr =[
        self::STATUS_YES => '开启',
        self::STATUS_NO  => '关闭'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_train_ppt';
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
            [['create_user', 'create_time','type','status', 'update_time', 'delete_time'], 'integer'],
            [['comment','path'], 'string'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    public static function getPathByids($ids){
        $data = self::find()->select('name,path')->asArray()->where(['id'=>$ids])->all();
        if($data){
            $newArray = array_column( $data,'path');
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
            'type' => '课件类型',
            'path' => '路径',
            'status' => '状态',
            'create_user' => '创建人',
            'comment' => '备注',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'delete_time' => '删除时间',
        ];
    }
}
