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
class UserTempCardRecord extends Base
{
    //状态
    const STATUS_YES = 1;
    const STATUS_NO = 2;

    static public $statusArr =[
        self::STATUS_YES => '领取',
        self::STATUS_NO  => '归还'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_user_temp_card_record';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status','user_id','temp_card_id'], 'integer'],
            [['created_at'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'=>'',
            'temp_card_id'=>'',
            'user_id'=>'',
            'status'=>'',
            'created_at'=>'',

        ];
    }


    public function search($param){
        $this->setAttributes($param);

        $query = UserTempCardRecord::find();
        $query->alias('utcr');
        $query->select(['utcr.*']);
        $query->leftJoin(UserTempCard::tableName() . ' utc', 'utcr.temp_card_id = utc.id');
        if (isset($request['number']))
            $query->andFilterWhere(['like','utc.number',$request['number']]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }
}
