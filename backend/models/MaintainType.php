<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "nxp_maintain_type".
 *
 * @property int $id
 * @property string|null $name 名称
 * @property int|null $status 状态
 * @property int|null $area 区域
 * @property int|null $process 工序
 * @property int|null $create_user 创建人
 * @property int|null $create_time 创建时间
 * @property int|null $update_time 更新时间
 * @property int|null $delete_time 删除时间
 */
class MaintainType extends Base
{
    const OTHER_TYPE = 1;//其他问题类型id
    const STATUS_OK = 1;//
    /**
     * 工序
     */
    const PRE_PROCESS = 1;
    const MOL_PROCESS = 2;
    const PMC_PROCESS = 3;
    const MAR_PROCESS = 4;
    const HPC_PROCESS = 5;
    const BAL_PROCESS = 6;
    const REF_PROCESS = 7;
    const FLU_PROCESS = 8;
    const SAW_PROCESS = 9;
    const TRI_PROCESS = 10;
    const INS_PROCESS = 11;
    const PAC_PROCESS = 12;
    const DEF_PROCESS = 13;
    const PLA_PROCESS = 14;
    const DEJUNK_PROCESS = 15;
    const FORM_PROCESS = 16;
    const VM_CHECK_PROCESS = 17;
    
    public static $processList = [
        self::PRE_PROCESS => 'Pre-mold Plasma',
        self::MOL_PROCESS => 'Molding',
        self::PMC_PROCESS => 'PMC',
        self::MAR_PROCESS => 'Marking',
        self::HPC_PROCESS => 'HPC',
        self::BAL_PROCESS => 'Ball Attachment',
        self::REF_PROCESS => 'Reflow',
        self::FLU_PROCESS => 'Flux Clean',
        self::SAW_PROCESS => 'Saw Singulation',
        self::TRI_PROCESS => 'Trim Form',
        self::INS_PROCESS => 'Inspection',
        self::PAC_PROCESS => 'Packing',
        self::DEF_PROCESS => 'Deflash',
        self::PLA_PROCESS => 'Plating',
        self::DEJUNK_PROCESS => 'DEJUNK',
        self::FORM_PROCESS => 'FORM',
        self::VM_CHECK_PROCESS => 'VM_CHECK',
    ];
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_maintain_type';
    }

    public function behaviors()
    {
        return [$this->getTimestampBehaviorArray()];
    }

    /**
     * 获得类型列表
     * @return array
     */
    public static function getTypeList(){
        $data = self::find()->where(['delete_time' => null])->select('id,name')->orderBy('id desc')->all();
        if(!empty($data)){
            return ArrayHelper::map($data,'id','name');
        }
        return [];
    }
    /**
     * @param $ids
     * @return array
     */
    public static function getDataByIds($ids){
        $data = self::find()->where(['id' => $ids])->select('id,name')->orderBy('id desc')->all();
        if(!empty($data)){
            return ArrayHelper::map($data,'id','name');
        }
        return [];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'create_user', 'area', 'process', 'create_time', 'update_time', 'delete_time'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '分类名称',
            'status' => '状态',
            'area' => '区域',
            'process' => '工序',
            'create_user' => '创建人',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'delete_time' => '删除时间',
        ];
    }
    
    public function create($data)
    {
        $this->name        = $data['name'];
        $this->status      = self::STATUS_OK;
        $this->area        = $data['area'];
        $this->process     = $data['process'];
        $this->create_user = Yii::$app->user->identity->user_id;
        $this->create_time = time();
        if (!$this->save()) {
            return FALSE;
        }
        return $this->attributes['id'];
    }

    /**
     * @param array $param
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getType($param = []){
        $query = self::find();
        $query->select(['id', 'name as text']);
        $query->where(['delete_time' => null]);
        $query->andFilterWhere(['process' => $param['process']]);
        $query->asArray();
        return $query->all();
    }
}
