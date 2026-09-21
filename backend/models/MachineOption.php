<?php

namespace backend\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "nxp_machine_option".
 *
 * @property int $id
 * @property int $status 1启用，0禁用
 * @property int $model_id 设备型号id
 * @property string|null $standard
 * @property string|null $position
 * @property string|null $create_time 发布时间
 * @property string|null $update_time 更新时间
 */
class MachineOption extends Base
{
    const SELECT_NO  = 0;
    const SELECT_YES = 1;

    public static $statusList = [
        self::SELECT_YES => '启用',
        self::SELECT_NO  => '禁用'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_machine_option';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'model_id'], 'integer'],
            [['create_time', 'update_time', 'standard', 'position'], 'string', 'max' => 255]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'status'      => '状态',
            'model_id'    => '设备型号id',
            'position'    => '位置编号',
            'standard'    => '标准',
            'create_time' => '发布时间',
            'update_time' => '更新时间',
        ];
    }

    /**
     * @param $data
     * @return bool
     */
    public function create($data)
    {
        $this->position    = trim($data['position']);
        $this->status      = self::SELECT_NO;
        $this->model_id    = $data['model_id'];
        $this->standard    = $data['standard'];
        $this->create_time = $this->update_time = date('Y-m-d H:i:s', time());
        if (!$this->save(false)) {
            return FALSE;
        }
        return $this->attributes['id'];
    }

    /**
     * @param $key
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getOptByKey($key)
    {
        $query = self::find();
        $query->select(['id', 'name as text']);
        $query->where(['status' => self::SELECT_YES]);
        $query->andFilterWhere(['=', 'model_id', $key['model_id']]);
        $query->asArray();
        return $query->all();
    }

}
