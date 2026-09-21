<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "nxp_team".
 *
 * @property string $id
 * @property string $teamName 团队名称
 * @property int $created_at 创建时间
 */
class Team extends \yii\db\ActiveRecord
{
    const STATUS_YES = 1;
    const STATUS_NO = 2;

    static $statusArr = [
        self::STATUS_YES => '启用',
        self::STATUS_NO => '禁用'
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_team';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at','status'], 'integer'],
            [['teamName'], 'required'],
            [['teamName'], 'string', 'max' => 50],
            ['teamName', 'unique']
        ];
    }

    public static function getTeamName($id){
        $teamName = self::find()->where(['id' => $id])->select('teamName')->column();
        if($teamName){
            return $teamName[0];
        }else{
            return false;
        }
    }

    static public function getAllByArray(){
        return Team::find()->where(['status' => Team::STATUS_YES])->asArray()->all();
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'teamName' => '小组名称',
            'status' => '小组状态',
            'created_at' => '创建时间',
        ];
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getTeamData(){
        $data = self::find()->select('id,teamName')
            ->where(['status' => self::STATUS_YES])
            ->asArray()
            ->all();
        return $data ? ArrayHelper::map($data, 'id', 'teamName') : [];
    }

}
