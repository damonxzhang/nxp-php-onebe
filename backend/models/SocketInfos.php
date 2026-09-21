<?php


namespace backend\models;

class SocketInfos extends Base{

    const STATUS_CHANGE_ACTION = 1; // 改变状态
    const OPEN_THE_DOOR_ACTION = 2; // 开门操作
    const MOVE_CARD_ACTION = 3; // 移动车操作
    
    const STATUS_AWAIT = 1;
    const STATUS_SUCCESS = 2;
    const STATUS_ERROR = 3;

    static $actionList = [
        // self::STATUS_CHANGE_ACTION => '下架物料',
        self::OPEN_THE_DOOR_ACTION => '开门指令',
        self::MOVE_CARD_ACTION => '移动车指令',
    ];


    const STATUS_PASS = 1;
    const STATUS_WAITING = 2;

    static $statusList = [
        self::STATUS_WAITING => '指令已发送',
        self::STATUS_PASS => '等待发送指令',
    ];

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'action'     => '操作',
            'value'      => '内容',
            'status'     => '状态',
            'update_time'=> '更新时间',
            'create_time'=> '创建时间',
        ];
    }

    public static function addAction($data,$action,$status = 0){

        $model = (new self());
        $data['action'] = $action;
        $model->action = $action;
        $model->value =  json_encode($data);
        $model->status = $status;
        $model->create_time = date('Y-m-d H:i:s');
        return $model->save();
    }



}