<?php

namespace backend\models;


use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "nxp_user_station".
 *
 * @property int $id
 * @property string $station 站别
 * @property int $status 状态 1:启用 2:禁用
 * @property int $create_time 创建时间
 */
class UserStation extends Base
{
    const STATUS_OPEN  = 1;
    const STATUS_CLOSE = 2;

    //启用状态
    public static $statusArr = [
        self::STATUS_OPEN  => '启用',
        self::STATUS_CLOSE => '禁用',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_user_station';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['station', 'create_time'], 'string'],
            [['station'], 'unique']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'station'     => '站别',
            'status'      => '状态',
            'create_time' => '创建时间',
        ];
    }

    /**
     * 添加站别
     * @param $data
     * @return array
     */
    public function create($data)
    {
        $this->station     = trim($data['station']);
        $this->status      = $data['status'];
        $this->create_time = date('Y-m-d H:i:s', time());
        if (!$this->save()) {
            $error = array_values($this->getFirstErrors())[0];
            return $this->arrData(100, $error);
        }
        return $this->arrData(200, '添加成功');
    }

    /**
     * 修改站别
     * @param $data
     * @return array
     * @throws \yii\base\Exception
     */
    public function edit($data)
    {
        $model           = self::findOne($data['id']);
        $model->station  = $data['station'];
        $model->status   = $data['status'];
        if (!$model->save()) {
            $error = array_values($model->getFirstErrors())[0];
            return $this->arrData(100, $error);
        }
        return $this->arrData(200, '更新成功');
    }

    /**
     * 检查站别是否存在
     * @param $data
     * @return bool
     */
    public function check($data)
    {
        $query = self::find();
        $query->where(['!=', 'id', $data['id']]);
        $query->andFilterWhere(['station'  => $data['station']]);
        if ($query->one()) {
           return false;
        }
        return true;
    }

    /**
     * 获取站别
     * @return array
     */
    public function getStation()
    {
        $query = self::find();
        $query->select(['id', 'station']);
        $query->where(['status' => self::STATUS_OPEN]);
        $query->asArray();
        return $query->all();
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getStationList(){
        $data = self::find()
            ->select(['id', 'station'])
            ->where(['status' => self::STATUS_OPEN])
            ->asArray()
            ->all();
        return $data ? ArrayHelper::map($data, 'id', 'station') : [];
    }

    /**
     * 获取站别名称
     * @param $id
     * @return mixed|string
     */
    public function getStationName($id)
    {
        if(empty($id)){
            return '';
        }
        $query = self::findOne(['id' => $id]);
        return empty($query) ? '' : $query->station;
    }

    
    /**
     * 获取站别数组
     * @return array
     */
    public function getStationArr()
    {
        $query = self::find();
        $query->select(['id', 'station']);
        $query->where(['status' => self::STATUS_OPEN]);
        $query->asArray();
        $data = $query->all();
        return ArrayHelper::map($data, 'id', 'station');
    }
    
}

