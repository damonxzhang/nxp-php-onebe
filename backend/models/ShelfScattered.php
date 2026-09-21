<?php

namespace backend\models;


use common\models\SendMqtt;
use Yii;

/**
 * This is the model class for table "nxp_shelf".
 *
 * @property string $id
 * @property string $shelf_number
 * @property int $status
 * @property int $row
 * @property int $mark
 * @property int $number
 * @property int $floor
 * @property int $car_number
 * @property int $column
 * @property string $create_time 创建时间
 */
class ShelfScattered extends Base
{
    //货架当前状态
    const STATUS_YES   = 1; //有效
    const STATUS_NO  = 2; //无效

    public static $statusArr = [
        self::STATUS_YES   => '有效',
        self::STATUS_NO  => '无效',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_shelf_scattered';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['count'], 'string'],
            [['part_no', 'create_time','update_time','scattered_time'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'           => 'ID',
            'count'         => '报废数量',
            'part_no' => '报废型号',
            'update_time'       => '更新时间',
            'status'       => '货架当前状态',
            'create_time'  => '创建时间',
            'scattered_time'  => '报废时间',
        ];
    }

    /**
     * 添加数据
     * @param $data
     * @return bool
     */
    public function create($data)
    {
        // $this->part_no      = trim($data['part_no']);
        $this->count        = $data['count'];
        $this->status       = self::STATUS_YES;
        $this->scattered_time  = date('Y-m-d H:i:s', strtotime($data['scattered_time']));
        $this->create_time  = date('Y-m-d H:i:s', time());
        $this->update_time  = date('Y-m-d H:i:s', time());
        if (!$this->save()) {
            return false;
        }
        return $this->attributes['id'];
    }

}
