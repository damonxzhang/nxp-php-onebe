<?php
namespace backend\models;
use common\models\SendMqtt;
use Yii;

/**
 * This is the model class for table "nxp_bill_shelf".
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
class BillShelf extends Base
{
    //货架当前状态
    const STATUS_YES   = 1; //启用
    const STATUS_NO    = 2; //禁用

    public static $statusArr = [
        self::STATUS_YES  => '启用',
        self::STATUS_NO   => '禁用'
    ];

    //货架标识用于标识货架唯一的标识符
    public static $markArr = [
        1 => 'A01',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_bill_shelf';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'mark'], 'integer'],
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
            'status'       => '货架当前状态',
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
        $this->mark         = $data['mark'];
        $this->status       = self::STATUS_YES;
        $this->create_time  = date('Y-m-d H:i:s', time());
        if (!$this->save(false)) {
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
    public function getShelfArr()
    {
        $query = self::find();
        $query->select(['shelf_number','mark','id']);
        $query->where(['status' => self::STATUS_YES]);
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
        $query->select([ 'mark', 'status']);
        $query->andFilterWhere(['in', 'id', $idList]);
        $query->orderBy(['id' => SORT_ASC]);
        return $query->all();
    }

}
