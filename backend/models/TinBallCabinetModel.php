<?php

namespace backend\models;

use Yii;

class TinBallCabinetModel extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_tin_ball_cabinet_model';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id','each'], 'integer'],
            [[
                'materialcode',
                'part_no',
                'create_time',
                'update_time',
            ], 'string'],
            ['bottle','safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'           => 'ID',
            'user_id'      => '制单人id',
            'part_no'      => 'Part No',
            'create_time'  => '创建时间',
            'update_time'  => '更新时间',
        ];
    }

    /**
     * 人员信息表
     * @return \yii\db\ActiveQuery
     */
    public function getUserinfo()
    {
        return $this->hasOne(UserInfo::className(), ['id' => 'user_id']);
    }

    /**
     * 添加数据
     * @param $data
     * @return bool
     */
    public function create($data)
    {
        $this->materialcode = trim($data['materialcode']);
        $this->each         = trim($data['each']);
        $this->bottle       = trim($data['bottle']);
        $this->part_no      = trim($data['part_no']);
        $this->user_id      = trim($data['user_id']);
        $this->create_time  = date('Y-m-d H:i:s', time());
        $this->update_time  = date('Y-m-d H:i:s', time());
        if (!$this->save()) {
            $error = array_values($this->getFirstErrors())[0];
            return false;
        }
        return true;
    }

    /**
     * 根据用户ID获取名称
     * @param $id
     * @return string
     */
    public function getPartNo($id)
    {
        if(empty($id)){
            return '';
        }
        $query = self::findOne(['id' => $id]);
        return empty($query) ? '' : $query->part_no;
    }

}
