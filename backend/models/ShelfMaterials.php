<?php

namespace backend\models;


use common\models\SendMqtt;
use common\models\Utility;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;

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
class ShelfMaterials extends Base
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

    public static $statusCheckArr = [
        self::STATUS_WAIT     => '待入架',
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
        return 'nxp_shelf_materials';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['shelf_id', 'user_id', 'entry_id', 'status', 'fettle', 'apply_id'], 'integer'],
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
            'shelf_id'     => '货架id',
            'user_id'      => '持有人id',
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
     * 人员信息表
     * @return \yii\db\ActiveQuery
     */
    public function getUserinfo()
    {
        return $this->hasOne(UserInfo::className(), ['id' => 'user_id']);
    }

    /**
     * 货架表
     * @return \yii\db\ActiveQuery
     */
    public function getShelf()
    {
        return $this->hasOne(Shelf::className(), ['id' => 'shelf_id']);
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
        $this->status      = self::STATUS_WAIT;
        $this->fettle      = self::FETTLE_UP;
        $this->create_time = date('Y-m-d H:i:s', time());
        $this->update_time = date('Y-m-d H:i:s', time());
        if (!$this->save()) {
            return false;
        }
        return true;
    }

    /**
     * 申请物料
     * @param array $param
     * @return array
     */
    public function getMaterials($param = [])
    {
        $query = self::find();
        $query->where([
            'part_no' => $param['part_no'],
            'status'  => $param['status'],
            'fettle'  => self::FETTLE_UP,
        ]);
        $time = date('Y-m-d H:i:s', time());
        $query->andWhere(['<', 'end_time', $time]); //当前时间大于醒料时间
        $query->andWhere(['>', 'expire_time', $time]); //当前时间小于过期时间
        $query->orderBy(['end_time' => SORT_ASC,'cont_no' => SORT_ASC]);
        $query->limit($param['amount']);
        return $query->all();
    }
    /**
     * 申请物料
     * @param array $param
     * @return array
     */
    public function getSelectedMaterials($param = [])
    {
        $query = self::find();
        $query->where([
            'part_no' => $param['part_no'],
            'apply_id' => $param['apply_id'],
            'status'  => $param['status'],
            'fettle'  => self::FETTLE_UP,
        ]);
        $time = date('Y-m-d H:i:s', time());
        $query->andWhere(['<', 'end_time', $time]); //当前时间大于醒料时间
        $query->andWhere(['>', 'expire_time', $time]); //当前时间小于过期时间
        $query->orderBy(['end_time' => SORT_ASC,'cont_no' => SORT_ASC]);
        $query->limit($param['amount']);
        return $query->all();
    }

      /**
     * auth d
     * 2022/11/27
     * @param $param
     * @return void
     */
    public function getMaterialByFloor($param = [],$floor = 1){
        $query = self::find()->alias('m');
        $query->leftJoin(Shelf::tableName() . ' s', 'm.shelf_id = s.id')->where([
            'm.part_no' => $param['part_no'],
            'm.status'  => $param['status'],
            'm.fettle'  => self::FETTLE_UP,
            's.floor' => $floor
        ]);
        if ($floor == Shelf::FLOOR_ONE){
            $query->andWhere(['in', 's.region', [Shelf::G,Shelf::Y]]);
        }
        $time = date('Y-m-d H:i:s', time());
        $query->andWhere(['<', 'm.end_time', $time]); //当前时间大于醒料时间
        $query->andWhere(['>', 'm.expire_time', $time]); //当前时间小于过期时间
        $query->orderBy(['m.end_time' => SORT_ASC,'cont_no' => SORT_ASC]);
        $query->limit($param['amount']);
        return $query->all();
    }    /**
    * auth d
    * 2022/11/27
    * @param $param
    * @return void
    */
   public function getMaterialByFloorTwo($param = []){
       $query = self::find()->alias('m');
       $query->leftJoin(Shelf::tableName() . ' s', 'm.shelf_id = s.id')->where([
           'm.part_no' => $param['part_no'],
           'm.status'  => $param['status'],
           'm.fettle'  => self::FETTLE_UP,
           's.floor' => 2
       ]);
       $time = date('Y-m-d H:i:s', time());
       $query->andWhere(['<', 'm.end_time', $time]); //当前时间大于醒料时间
       $query->andWhere(['>', 'm.expire_time', $time]); //当前时间小于过期时间
       $query->orderBy(['m.end_time' => SORT_ASC,'cont_no' => SORT_ASC]);
       $query->limit($param['amount']);
       return $query->all();
   }

    /**
     * 获取申请物料数量
     * @param array $param
     * @return int|string
     */
    public function getCountMaterials($param = [])
    {
        $query = self::find();
        $query->where([
            'part_no' => trim($param['part_no']),
            'status'  => self::STATUS_PENDING, //物料状态为待领取
            'fettle'  => self::FETTLE_UP,   //上架状态
        ]);
        $time = date('Y-m-d H:i:s', time());
        $query->andWhere(['<', 'end_time', $time]); //当前时间大于醒料时间
        $query->andWhere(['>', 'expire_time', $time]); //当前时间小于过期时间
        return $query->count('id');
    }

    /**
     * 获取临近过期时间内的物料
     * @param string $time
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getShelfIdList($time = ''){
        $query = self::find();
        $query->select(['id', 'shelf_id']);
        $query->where([
            'status'  => self::STATUS_PENDING, //物料状态为待领取
            'fettle'  => self::FETTLE_UP,   //上架状态
        ]);
        $query->andFilterWhere(['<', 'expire_time', $time]);
        $query->asArray();
        return $query->all();
    }

    /**
     * 检查是否有已存在物料
     * @param $shelfId
     * @return ShelfMaterials|null
     */
    public function checkMaterialsByShelfId($shelfId)
    {
        $query = ShelfMaterials::findOne([
            'shelf_id'=> $shelfId,
            'fettle'  => ShelfMaterials::FETTLE_UP,
            'status'  => ShelfMaterials::STATUS_PENDING,
        ]);
        return $query;
    }

    /**
     * 获取一条未二次确认的数据
     * @return array|\yii\db\ActiveRecord|null
     */
    public static function getNoTwoConfirm(){
        return self::find()->alias('sm')->select('sa.id,sa.part_no,ui.chinese_name,sm.cont_no,sm.shelf_id')
            ->innerJoin(['sa' => ShelfApply::tableName()],'sm.user_id = sa.user_id and sm.part_no = sa.part_no and sa.id = sm.apply_id')
            ->innerJoin(['ui' => UserInfo::tableName()],'sa.user_id = ui.id')
            ->where(['sm.status' => 3,'sm.fettle' => 1])
            ->andWhere(['>','sm.user_id',0])
            ->limit(1)
            ->asArray()
            ->one();
    }

    /**
     * 检查物料的状态
     * @param $param
     * @return array|\yii\db\ActiveRecord[]
     */
    public function checkMaterials($param = [])
    {
        $query = self::find();
        $query->where([
            'shelf_id'=> $param['shelf_id'],
            'fettle'  => ShelfMaterials::FETTLE_UP,
            'status'  => $param['status'],
        ]);
        $query->andWhere(['!=', 'id', $param['id']]);
        return $query->all();
    }

    
    public function receive($floor = 2){
        $this->library_time = date('Y-m-d H:i:s', time());
        $this->fettle       = self::FETTLE_DOWN; //物料为下架
        $this->update_time  = date('Y-m-d H:i:s', time());//更新时间
        if (!$this->save()) {
            throw new \Exception('物料领取失败！保存失败');
        }
        //检查同一位置是否还有别的物料
        $shelfId = $this->shelf_id;
        if($floor == 2){
            $query = self::findOne(['shelf_id'=> $shelfId, 'fettle'  => self::FETTLE_UP, 'status'  => self::STATUS_PENDING]);
        } else {
            $query = self::find()->where(['shelf_id'=> $shelfId, 'fettle'  => self::FETTLE_UP])->andWhere(['in','status',[self::STATUS_PENDING,self::STATUS_HOLD]])->one();
        }
        //有别的物料并且状态为待领取
        $shelfModel  = Shelf::findOne(['id' => $shelfId]);
        if (!$query) {
            $status  = Shelf::STATUS_FREE;//空闲
            $shelfModel->status = $status;
            $shelfModel->save();
            if ($floor == 2){
                $this->sendMqttData($shelfModel->id, Shelf::STATUS_FREE,2);
            } else {
                $this->setShelfStatus($shelfModel->car_number);
            }
        }
    }

    /**
     * 给下位机发消息
     * @param $shelfId
     * @param string $status
     * @return bool
     */
    public function sendMqttData($shelfId, $status = '', $floor)
    {
        //检查是否还有在货架格子上的物料$
        $query = (new ShelfMaterials())->checkMaterialsByShelfId($shelfId);

        // print_r($query);die;
        if ($query) {
            return true;
        }
        $shelfModel         = Shelf::findOne(['id' => $shelfId]);
        $shelfModel->status = $status;
        if (!$shelfModel->save()) {
            return false;
        }
        // 二楼货架发送通知
        if ($floor === Shelf::FLOOR_TWO){
            $sendMessage = (new SendMqtt())->publishMessage($shelfModel->mark, $shelfModel->number, $status);
            if (!$sendMessage) {
                return false;
            }
        }

        return true;
    }


    public function setShelfStatus($car_number){
        $move = true;
        $shelfs = Shelf::find()->where(['car_number' => $car_number])->all();
        foreach ($shelfs as $shelf){
            $query = self::find()->where(['shelf_id'=> $shelf->id, 'fettle'  => self::FETTLE_UP])->andWhere(['in','status',[self::STATUS_PENDING,self::STATUS_HOLD]])->one();
            if ($query){
                $move = false;
            } else {
                $shelf->status =  Shelf::STATUS_FREE;//空闲;
                $shelf->save();
            }
        }
        if ($move){
            Shelf::updateAll(['end_shelf' => null],'car_number = '. $car_number);
            SocketInfos::addAction(['car_number' => $car_number],SocketInfos::MOVE_CARD_ACTION,SocketInfos::STATUS_AWAIT);
        }
    }
}
