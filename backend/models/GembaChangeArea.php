<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "nxp_gemba_change_area".
 *
 * @property int $id
 * @property int|null $gemba_id new gemba id
 * @property int|null $original_area 原来的区域
 * @property int|null $new_area 更新后的区域
 * @property string|null $content 备注
 * @property int|null $create_user 修改人
 * @property int|null $create_time 创建时间
 * @property int|null $update_time 更新时间
 * @property int|null $delete_time 删除时间
 */
class GembaChangeArea extends \backend\models\Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_gemba_change_area';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['original_area', 'new_area', 'gemba_id', 'create_user', 'create_time', 'update_time', 'delete_time'], 'integer'],
            [['content'], 'string', 'max' => 255],
        ];
    }


    public function behaviors()
    {
        return [$this->getTimestampBehaviorArray()];
    }

    /**
     * 是否有修改记录
     * @param $id
     * @return bool
     */
    public function isChange($id){
        $res = self::find()->where(['gemba_id' => $id])->exists();
        if($res){
            return true;
        }
        return false;
    }

    public function isExists($id){
        return self::find()->where(['gemba_id' => $id])->exists();
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'gemba_id' => 'gemba ID',
            'original_area' => '原区域',
            'new_area' => '新区域',
            'content' => '备注',
            'create_user' => '修改人',
            'create_time' => '创建时间',
            'update_time' => 'Update Date',
            'delete_time' => 'Delete Date',
        ];
    }
}
