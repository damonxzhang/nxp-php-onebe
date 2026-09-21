<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "nxp_brush_shoes".
 *
 * @property int    $id
 * @property int    $user_id
 * @property int    $status      申诉状态
 * @property string $brush_date  刷鞋时间
 * @property int    $create_time 创建时间
 */
class BrushShoes extends Base
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_brush_shoes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['create_time','brush_date'], 'string'],
            [['user_id'], 'integer'],
        ];
    }

    /**
     * 查询申诉和审核人姓名
     * @param $ir_id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function getAppealAndUserName($ir_id)
    {
        return Appeal::find()->alias('a')
            ->leftJoin(['u' => UserInfo::tableName()], 'a.audit_user = u.id')
            ->select('a.*,u.chinese_name')
            ->where(['a.relation_id' => $ir_id])
            ->asArray()
            ->orderBy('a.id desc')
            ->limit(1)
            ->one();
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'user_id'     => '用户id',
            'brush_date'  => '刷鞋时间',
            'create_time' => 'Create Time',
        ];
    }
}

