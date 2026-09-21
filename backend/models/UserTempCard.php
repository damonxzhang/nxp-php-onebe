<?php

namespace backend\models;

use phpDocumentor\Reflection\Types\Parent_;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;

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
class UserTempCard extends Base
{
    //状态
    const STATUS_YES = 1;
    const STATUS_NO = 2;

    static public $statusArr =[
        self::STATUS_YES => '开启',
        self::STATUS_NO  => '关闭'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_user_temp_card';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status','user_id'], 'integer'],
            [['number','created_at','updated_at'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'=>'',
            'number'=>'',
            'status'=>'',
            'user_id'=>'',
            'created_at'=>'',
            'updated_at'=>'',

        ];
    }

    public function search($param){
        $this->setAttributes($param);

        $query = UserTempCard::find();
        $query->alias('utc');
        $query->select(['utc.*','ui.chinese_name as chinese_name']);
        $query->leftJoin(UserInfo::tableName() . ' ui', 'ui.id = utc.user_id');
        if (isset($request['number']))
            $query->andFilterWhere(['like','utc.number',$request['number']]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;

    }

    public function getCardNumber($id){

        if(empty($id)){
            return '';
        }
        $query = UserTempCard::findOne(['id' => $id]);
        return empty($query) ? '' : $query->number;

    }
}
