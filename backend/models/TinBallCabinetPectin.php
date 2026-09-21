<?php

namespace backend\models;

use Yii;
use yii\data\ActiveDataProvider;
class TinBallCabinetPectin extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_tin_ball_cabinet_pectin';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id','product_number','apply_number'], 'integer'],
            [[
                'document_no',
                'pectin_no',
                'create_time',
                'update_time',
            ], 'string'],
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
        $this->document_no    = trim($data['document_no']);
        $this->product_number = trim($data['product_number']);
        $this->apply_number   = trim($data['apply_number']);
        $this->pectin_no      = trim($data['pectin_no']);
        $this->user_id        = trim($data['user_id']);
        $this->create_time    = date('Y-m-d H:i:s', time());
        $this->update_time    = date('Y-m-d H:i:s', time());
        if (!$this->save(false)) {
            return false;
        }
        return true;
    }

    public function search(){
      
        $query = self::find();
        $query->orderBy(['id' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }

}
