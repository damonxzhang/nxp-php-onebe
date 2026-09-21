<?php


namespace backend\models;

class SocketLog extends Base{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_socket_log';
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'socket_id'     => 'socket_id',
            'msg'      => '内容',
            'create_time'=> '创建时间',
        ];
    }



}