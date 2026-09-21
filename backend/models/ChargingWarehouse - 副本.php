<?php

namespace backend\models;

use Yii;
use yii\db\Exception;

/**
 * This is the model class for table "nxp_charging_warehouse".
 *
 * @property string $id
 * @property string $number 充电仓编号
 * @property int $user_id 用户id
 * @property int $tank_id 充电柜id
 * @property string $equipment_number 设备编号
 * @property int $charging_time 默认充电时间
 * @property int $status 状态
 * @property int $type 类型（充电中、可使用、使用中、异常）
 * @property int $charging_num 充电次数
 * @property string $start_time 开始充电时间
 * @property string $end_time 结束充电时间
 */
class ChargingWarehouse extends Base
{

    const STATUS_YES = 1;
    const STATUS_NO  = 0;

    static public $statusArr = [
        self::STATUS_YES     => '启用',
        self::STATUS_NO      => '禁用'
    ];

    
    const STYPE_PDA          = 1;
    const STYPE_SURFACE      = 2;

    static public $stypeArr = [
        self::STYPE_PDA          => 'PDA',
        self::STYPE_SURFACE      => 'SURFACE',
    ];


    const TYPE_CHARGING = 1;
    const TYPE_USE      = 2;
    const TYPE_ABNORMAL = 3;
    const TYPE_CAT_USE  = 4;
    const TYPE_CHANGE   = 5;

    static public $typeArr = [
        self::TYPE_CHARGING => '充电中',
        self::TYPE_USE      => '使用中',
        self::TYPE_ABNORMAL => '异常',
        self::TYPE_CAT_USE  => '可使用',
        self::TYPE_CHANGE   => '由异常变为充电中'
    ];

    //请求借还接口验证规则
    const TAKE_OR_ALSO_SCEN = 'takeOrAlso';
    //获得可使用和使用中的充电仓接口验证规则
    const GET_TANK_RETURN_BOXID = 'getBoxId';

    public function scenarios()
    {
        return array_merge([
            self::TAKE_OR_ALSO_SCEN     => ['number','tank_id','equipment_number','user_id'],
            self::GET_TANK_RETURN_BOXID => ['tank_id','user_id']
        ], parent::scenarios());
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_charging_warehouse';
    }



    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['number', 'tank_id'], 'required'],
            [['user_id', 'tank_id', 'charging_time', 'status', 'type', 'charging_num'], 'integer'],
            [['start_time', 'end_time'], 'safe'],
            [['number', 'equipment_number'], 'string', 'max' => 30],
        ];
    }

    public function byUserStatusResult($user_id,$tank_id){
        $warehouse = self::findOne(['user_id' => $user_id,'tank_id' => $tank_id]);

        if(!empty($warehouse)){
            //有设备
            $result['deviceId'] = $warehouse->equipment_number;

        } else{
            //无设备
            $result['deviceId']  = null;
        }

        $result['boxId']     = $warehouse['number'];
        return $result;
    }

    /**
     * 是否持有设备
     * @return ChargingWarehouse|bool|null
     */
    public function confirmUserStatus(){
        $model = self::findOne(['user_id' => $this->user_id,'status' => self::STATUS_YES,'tank_id' => $this->tank_id]);
        if(!empty($model)  ){
            return $model;
        }{
            return false;
        }
    }

    /**
     * 查询正常状态的充电仓
     * @return \yii\db\ActiveQuery
     */
    public function getNormalBoxInfo(){
        return ChargingWarehouse::find()->where(['status' => ChargingWarehouse::STATUS_YES ])->andWhere(['<','type',ChargingWarehouse::TYPE_ABNORMAL])->all();
    }

    /**
     * 借
     * @return bool
     */
    public function endTake(){
        $model = self::findOne(['number' => $this->number,'type' => self::TYPE_CHARGING,'status' => self::STATUS_YES]);

        if(!empty($model)){

            $model->setAttributes([
                'user_id' => $this->user_id,
                'type' => self::TYPE_USE
            ]);
            if($model->save()){
                return true;
            }else{

                return $model->errorToString();
            }
        }else{
            return '数据错误';
        }
    }

    /**
     * 还
     * @return bool
     */
    public function endReturn(){
        $model = self::findOne(['number' => $this->number,'user_id' => $this->user_id,'type' => self::TYPE_USE,'status' => self::STATUS_YES]);
        $startTime = date('Y-m-d H:i:s');
        $endTime = date('Y-m-d H:i:s',strtotime($startTime.' +3 hour'));

        if(!empty($model)){
            $model->setAttributes([
                'user_id'     => null,
                'type'        => self::TYPE_CHARGING,
                'start_time'  => $startTime,
                'charging_num'=> ++$model->charging_num,
                'end_time'    => $endTime
            ]);
            if($model->save()){
                return true;
            }else{

                return $model->errorToString();
            }
        }else{
            return '数据错误';
        }
    }

    /**
     * 查找充电时间最长使用次数相对低的设备
     * @return array|bool|\yii\db\ActiveRecord|null
     */
    public function getAvailable($tank_id){

        $model = self::find()->select('number,equipment_number')
                    ->where(['status' => self::STATUS_YES,'tank_id' => $tank_id])
                    ->andWhere(['user_id' => null])
                    ->andWhere(['type' => self::TYPE_CHARGING])
                    ->orderBy([
                        'start_time' => SORT_ASC,
                        'charging_num' => SORT_ASC
                    ])
                    ->limit(1)
                    ->one();

        if(!empty($model)){
            return $model;
        }else{
            return false;
        }
    }

    /**
     * 修改充电仓状态
     * @param $status
     * @return bool
     */
    public function updateAnomalies($type){
        $this->type = $type;
        $this->user_id = '';
        if(!$this->save()){
            throw  new Exception( $this->errorToString());
        }
        return true;

    }

    /**
     *  交接时查询充电仓状态是否符合
     */
    public function warehouseIsOk($tank_id){

        $query = self::find()->alias('w');
        $query->innerJoin(['a' => ChargingAbnormal::tableName()],'a.warehouse_id = w.id');
        $query->where(['w.tank_id' => $tank_id,'w.status' => self::STATUS_YES]);
        $query->andWhere(['!=' ,'w.type',self::TYPE_CHARGING]);
        $query->andWhere(['!=' ,'w.type',self::TYPE_CAT_USE]);
        $query->andWhere(['=','a.status',ChargingAbnormal::NOT_STATUS]);
        if($query->count() > 0){
            return false;
        }else{
            return true;
        }
    }

    public function getChargingTank(){

        return $this->hasOne(ChargingTank::className(),['id' => 'tank_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'number' => '编号',
            'user_id' => '用户id',
            'tank_id' => '充电柜id',
            'equipment_number' => '设备编号',
            'charging_time' => '默认充电时长',
            'status' => '状态',
            'type' => '类型',
            'charging_num' => '充电次数',
            'start_time' => '开始充电时间',
            'end_time' => '结束充电时间',
        ];
    }
}
