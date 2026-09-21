<?php

namespace backend\models;

use yii\data\ActiveDataProvider;

class WashClothesMessage extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_wash_clothes_message';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['rfid','message','create_time'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => '',
            'message'     => '',
            'rfid'        => '',
            'create_time' => ''
        ];
    }

    public function insertMessage($rfid,$message)
    {
        $this->rfid        = $rfid;
        $this->message     = $message;
        $this->create_time = date('Y-m-d H:i:s', time());
        $this->save(false);
        return 1;
    }

}
