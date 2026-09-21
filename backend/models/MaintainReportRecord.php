<?php

namespace backend\models;

use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "nxp_maintain_report_record".
 *
 * @property int $id
 * @property string|null $searchKey 搜索关键内容
 * @property int|null $create_user 搜索人
 * @property int|null $create_time 创建时间
 * @property int|null $update_time 更新时间时间
 */
class MaintainReportRecord extends \backend\models\Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_maintain_report_record';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['create_user', 'create_time', 'update_time'], 'integer'],
            [['searchKey'], 'string', 'max' => 255],
        ];
    }

    public function createRecord(){
        $time = strtotime(date('Y-m-d'));
        $exists = self::find()->where(['>=','create_time',$time])->exists();
        if(!$exists){
            $arr = [
                'start_time' => $time,
                'end_time' => strtotime(date('Y-m-d').' +1 day')
            ];
            $this->setAttributes([
                'searchKey' => Json::encode($arr) ,
                'create_user' => Yii::$app->user->identity->user_id,
                'create_time' => $time
            ]);
            $this->save();
        }
    }

    /**
     * 保存记录
     * @param $key
     */
    public function saveRecord($key){

        $this->setAttributes([
            'searchKey' => $this->setSearchKey($key) ,
            'create_user' => Yii::$app->user->identity->user_id
        ]);
        $this->save();

    }

    public function setSearchKey(array $key){
        if(count($key) <= 0){
            return null;
        }
        $result = '';
        foreach ($key as $ke => $val){
            foreach ($val as $k => $v){
                if(!empty($v)){
                    $result .= $ke.'['.$k.']=' .$v."&";
                }

            }

        }

        if($result){
            return $result ;
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'searchKey' => 'Search Key',
            'create_user' => 'Create User',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
