<?php

namespace backend\models;

use Yii;


/**
 * This is the model class for table "nxp_shelf_materials".
 *
 * @property string $id
 * @property int $shelf_id
 * @property int $entry_id
 * @property string $cont_no
 * @property string $part_no
 * @property string $lot_no
 * @property int $user_id
 * @property int $status
 * @property int $fettle
 * @property string $end_time
 * @property string $start_time
 * @property string $library_time
 * @property string $expire_time
 * @property string $create_time 创建时间
 * @property string $update_time 更新时间
 */
class ShelfMaterialsBig extends Base
{
    //物料当前状态
    const STATUS_INFO     = 1; //入货架
    const STATUS_PENDING  = 2; //待领取
    const STATUS_USING    = 3; //领取中
    const STATUS_EXPIRED  = 4; //已过期
    const STATUS_SCRAPPED = 5; //已报废
    const STATUS_WAIT     = 6; //待入架
    const STATUS_HOLD     = 7; //hold


    public static $statusArr = [
        self::STATUS_INFO     => '入货架',
        self::STATUS_WAIT     => '待入架',
        self::STATUS_USING    => '领取中',
        self::STATUS_SCRAPPED => '已报废',
    ];


    //上架状态
    const FETTLE_UP      = 1; //上架
    const FETTLE_DOWN    = 2; //下架

    public static $fettleArr = [
        self::FETTLE_UP      => '上架',
        self::FETTLE_DOWN    => '下架',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_shelf_materials_big';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[ 'entry_id', 'status', 'fettle', 'apply_id'], 'integer'],
            [[
                'cont_no',
                'part_no',
                'lot_no',
                'start_time',
                'library_time',
                'end_time',
                'expire_time',
                'create_time',
                'update_time',
            ], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'           => 'ID',
            'entry_id'     => '录入人id',
            'apply_id'     => '申请id',
            'cont_no'      => 'Cont No',
            'part_no'      => 'Part No',
            'lot_no'       => 'Lot No',
            'status'       => '物料状态',
            'fettle'       => '是否上架',
            'library_time' => '出库时间',
            'start_time'   => 'Start Time', //起始时间
            'end_time'     => 'End Time',  //截止时间
            'expire_time'  => 'Exp Time', //过期时间
            'create_time'  => '创建时间',
            'update_time'  => '更新时间',
        ];
    }

    /**
     * 添加数据
     * @param $data
     * @return bool
     */
    public function create($data)
    {
        $this->cont_no     = trim($data['cont_no']);
        $this->part_no     = trim($data['part_no']);
        $this->lot_no      = trim($data['lot_no']);
        $this->start_time  = $data['start_time'];
        $this->end_time    = $data['end_time'];
        $this->expire_time = $data['expire_time'];
        $this->status      = self::STATUS_INFO;
        $this->fettle      = self::FETTLE_UP;
        $this->create_time = date('Y-m-d H:i:s', time());
        $this->update_time = date('Y-m-d H:i:s', time());
        if (!$this->save()) {
            return false;
        }
        return true;
    }

    
}
