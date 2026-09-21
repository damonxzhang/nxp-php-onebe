<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "nxp_machine_check".
 *
 * @property int $id
 * @property int $class
 * @property int $status
 * @property int $createby
 * @property int $facility_id 设备id
 * @property int $regular_id
 * @property int $roster_id
 * @property int $area
 * @property int $process
 * @property string|null $check_time
 * @property string|null $create_time 发布时间
 */
class MachineCheck extends Base
{

    const CLASS_D = 1;
    const CLASS_N = 2;

    public static $classList = [
        self::CLASS_D => '白班',
        self::CLASS_N => '夜班'
    ];

    const STATUS_YES = 1;
    const STATUS_NO  = 0;

    public static $statusList = [
        self::STATUS_YES => '完成',
        self::STATUS_NO  => '未检'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_machine_check';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['class', 'facility_id', 'status', 'createby', 'area', 'process','regular_id','roster_id','eligibility'], 'integer'],
            [['create_time', 'check_time'], 'string']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'facility_id' => '设备id',
            'regular_id'  => '规则表id',
            'roster_id'   => '排班表id',
            'class'       => '白夜班',
            'status'      => '检查完成',
            'eligibility' => '得分',
            'createby'    => '检查人',
            'area'        => '区域',
            'process'     => '工序',
            'create_time' => '创建时间',
            'check_time'  => '检查时间',
        ];
    }

    /**
     * @param $data
     * @return bool
     */
    public function create($data)
    {
        $this->facility_id = $data['facility_id'];
        $this->class       = $data['class'];
        $this->status      = self::STATUS_NO;
        $this->check_time  = $data['check_time'];
        $this->create_time = date('Y-m-d H:i:s', time());
        if (!$this->save(false)) {
            return false;
        }
        return true;
    }

    //规则
    public function getMachineRegular() {
        return $this->hasOne(MachineRegular::tableName(), ['id' => 'regular_id']);
    }

    //排班
    public function getMachineRoster() {
        return $this->hasOne(MachineRoster::tableName(), ['id' => 'roster_id']);
    }

}

