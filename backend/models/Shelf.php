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
class Shelf extends Base
{
    //货架当前状态
    const STATUS_RECEIVE   = 1; //1可领取
    const STATUS_PROGRESS  = 2; //2醒料中
    const STATUS_FREE      = 3; //3空闲
    const STATUS_EXCEPTION = 4; //4异常
    const STATUS_WARNING   = 5; //5预警中
    const STATUS_EXPIRE    = 6; //6过期
    const SHELF_RECEIVED   = 7; //7领取中
    const STATUS_BASIC     = 10; //初始

    public static $statusArr = [
        self::STATUS_RECEIVE   => '可领取',
        self::STATUS_PROGRESS  => '醒料中',
        self::STATUS_FREE      => '空闲',
        self::STATUS_EXCEPTION => '异常',
        self::STATUS_WARNING   => '预警中',
        self::STATUS_EXPIRE    => '过期',
        self::SHELF_RECEIVED   => '领取中',
        self::STATUS_BASIC     => '初始',
    ];

    const FLOOR_ONE = 1;
    const FLOOR_TWO = 2;

    public static $floorList = [
        self::FLOOR_ONE => '1楼',
        self::FLOOR_TWO => '2楼'
    ];

    const SEARCH_RECEIVE = 1;
    const SEARCH_PROGRESS = 2;
    const SEARCH_WARNING = 3;
    const SEARCH_EXPIRE = 4;

    public static $searchStatusList = [
        self::SEARCH_RECEIVE => '可领取',
        self::SEARCH_PROGRESS => '解冻中',
        self::SEARCH_WARNING => '临期',
        self::SEARCH_EXPIRE => '过期'
    ];

    const SEARCH_1h = 1;
    const SEARCH_2h = 2;
    const SEARCH_4h = 4;
    const SEARCH_OTHER = 99;

    public static $searchProgressList = [
        self::SEARCH_1h => '1小时',
        self::SEARCH_2h => '2小时',
        self::SEARCH_4h => '4小时',
        self::SEARCH_OTHER => '大于4小时'
    ];


    const SEARCH_11ASA10190D013 = '11ASA10190D013';
    const SEARCH_11ARE10145D012 = '11ARE10145D012';
    const SEARCH_11ASA10190D011 = '11ASA10190D011';
    const SEARCH_11ARE10145D008 = '11ARE10145D008';
    const SEARCH_11ARL10109C001 = '11ARL10109C001';
    const SEARCH_11ARL10103D003 = '11ARL10103D003';
    const SEARCH_11ASA10154D002 = '11ASA10154D002';
    const SEARCH_11ARL10109C005 = '11ARL10109C005';
    const SEARCH_11ARE10138D007 = '11ARE10138D007';
    const SEARCH_11ASA00268D001 = '11ASA00268D001';

    public static $partnoList = [
        self::SEARCH_11ASA10190D013 => '11ASA10190D013',
        self::SEARCH_11ARE10145D012 => '11ARE10145D012',
        self::SEARCH_11ASA10190D011 => '11ASA10190D011',
        self::SEARCH_11ARE10145D008 => '11ARE10145D008',
        self::SEARCH_11ARL10109C001 => '11ARL10109C001',
        self::SEARCH_11ARL10103D003 => '11ARL10103D003',
        self::SEARCH_11ASA10154D002 => '11ASA10154D002',
        self::SEARCH_11ARL10109C005 => '11ARL10109C005',
        self::SEARCH_11ARE10138D007 => '11ARE10138D007',
        self::SEARCH_11ASA00268D001 => '11ASA00268D001',
    ];

    const END_SHELF_YES = 1;
    const END_SHELF_NO = 0;

    public static $end_shelf = [
        self::END_SHELF_NO => '允许上架',
        self::END_SHELF_YES => '禁止上架'
    ];

    public static $regions = [
        'G',
        'B',
        'N'
    ];

    const G = 'G';
    const B = 'B';
    const N = 'N';
    const Y = 'Y';


    //货架标识用于标识货架唯一的标识符
    public static $markArr = [
        1 => 'A01',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_shelf';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'mark', 'number', 'floor'], 'integer'],
            [['shelf_number', 'create_time'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'           => 'ID',
            'mark'         => '货架标识',
            'shelf_number' => '货架编号',
            'number'       => '物料位置',
            'status'       => '货架当前状态',
            'floor'        => '楼层',
            'create_time'  => '创建时间',
        ];
    }

    /**
     * 添加数据
     * @param $data
     * @return bool
     */
    public function create($data)
    {
        $this->shelf_number = trim($data['shelf_number']);
        $this->number       = $data['number'];
        $this->mark         = $data['mark'];
        $this->status       = self::STATUS_FREE;
        $this->floor        = $data['floor'];
        $this->car_number   = $data['car_number'];
        $this->create_time  = date('Y-m-d H:i:s', time());
        if (!$this->save()) {
            return false;
        }
        return $this->attributes['id'];
    }

    /**
     * 获取货架名称
     * @param $id
     * @return string
     */
    public function getShelfNumber($id)
    {
        if(!isset($id)){
            return '';
        }
        $model = self::findOne(['id' => $id]);
        return $model ? $model->shelf_number : '';
    }

    /**
     * 获取货架状态及物料位置
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getStatusNumber()
    {
        $query = self::find();
        $query->select(['status','number','id','shelf_number']);
        $query->andFilterWhere(['floor'=>2]);
        $query->asArray();
        $query->orderBy(['id' => SORT_ASC]);
        return $query->all();
    }

    /**
     * 获取货架状态及物料位置
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getStatusNumberByFloorOne()
    {
        $query = self::find();
        $query->select(['status','number','id','shelf_number']);
        $query->andFilterWhere(['floor'=>1]);
        $query->asArray();
        $query->orderBy(['id' => SORT_ASC]);
        return $query->all();
    }

    /**
     * 获取货架位置及状态
     * @param array $idList
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getShelfData($idList = []){
        $query = self::find();
        $query->select(['mark', 'number', 'status','shelf_number']);
        $query->andFilterWhere(['in', 'id', $idList]);
        $query->orderBy(['id' => SORT_ASC]);
        return $query->all();
    }

    
    public function getStorehouseNum($column,$row){
        // 列
        $columns = [
            1,2,3,4,5,6,7
        ];
        // 行
        $rows = [
            1,2,3,4,5,6
        ];
        $number = [];
        foreach ($columns as $c){
            $number[$c] = [];
            foreach ($rows as $r){
                $number[$c][$r] = '0'.strval($c) . '-'.'0'.strval($r);
            }
        }
        return isset($number[$column][$row]) ? $number[$column][$row] : '';
    }

}
