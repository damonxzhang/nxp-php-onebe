<?php
namespace backend\models;
use common\models\SendMqtt;
use Yii;

/**
 * This is the model class for table "nxp_bill_materials".
 *
 * @property string $id
 * @property string $shelf_number
 * @property int $status
 * @property int $row
 * @property int $mark
 * @property int $number
 * @property int $column
 * @property string $create_time 创建时间
 */
class BillMaterials extends Base
{
    //货架当前状态
    const STATUS_YES   = 1; //上架
    const STATUS_NO    = 2; //下架

    public static $statusArr = [
        self::STATUS_YES  => '上架',
        self::STATUS_NO   => '下架'
    ];


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_bill_materials';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'mark','create_user', 'down_user','shelf_id'], 'integer'],
            [['materials_number', 'create_time','down_time'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                => 'ID',
            'shelf_id'          => '货架id',
            'create_user'       => '上架人id',
            'down_user'         => '下架人id',
            'materials_number'  => '物料单号',
            'status'            => '状态',
            'create_time'       => '创建时间',
            'down_time'         => '下架时间',
        ];
    }

    /**
     * 添加数据
     * @param $data
     * @return bool
     */
    public function create($data)
    {
        $this->materials_number = trim($data['materials_number']);
        $this->shelf_id         = $data['shelf_id'];
        $this->create_user      = $data['create_user'];
        $this->status           = self::STATUS_YES;
        $this->create_time      = date('Y-m-d H:i:s', time());
        if (!$this->save(false)) {
            return false;
        }
        return $this->attributes['id'];
    }

}
