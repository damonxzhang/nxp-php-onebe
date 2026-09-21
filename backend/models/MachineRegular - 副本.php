<?php

namespace backend\models;


/**
 * This is the model class for table "nxp_machine_regular".
 *
 * @property int $id
 * @property int $area
 * @property int $process
 * @property int $class
 * @property int $status
 * @property int $week
 * @property int $create_user
 * @property int $type
 * @property string $facility_id
 * @property string|null $create_time
 * @property string|null $update_time
 * @property string|null $start_time
 */
class MachineRegular extends Base
{
    const TYPE_N = 1;
    const TYPE_F = 2;
    const TYPE_Y = 3;

    public static $typeList = [
        self::TYPE_N => '日检',
        self::TYPE_F => '四日检',
        self::TYPE_Y => '月检'
    ];

    const STATUS_NO   = 1;
    const STATUS_YES  = 0;

    public static $statusList = [
        self::STATUS_YES => '启用',
        self::STATUS_NO  => '禁用'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_machine_regular';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['area', 'process', 'class', 'status', 'week', 'create_user', 'type'], 'integer'],
            [['facility_id', 'create_time', 'update_time','start_time'], 'string']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'area'        => '区域',
            'process'     => '工序',
            'class'       => '白夜班',
            'status'      => '状态',
            'week'        => '星期几',
            'create_user' => '创建人',
            'facility_id' => '设备id',
            'start_time'  =>  '开始时间',
            'type'        => '检查类型',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
    
}


