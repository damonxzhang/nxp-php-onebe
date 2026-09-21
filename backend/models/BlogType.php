<?php

namespace backend\models;
use yii\helpers\ArrayHelper;


/**
 * This is the model class for table "nxp_blog_type".
 *
 * @property string $id
 * @property string $name
 * @property int $status 状态 1:启用 2:禁用
 * @property int $create_time 创建时间
 */
class BlogType extends Base
{
    const TYPE_OPEN  = 1;
    const TYPE_CLOSE = 2;

    //启用状态
    public static $statusArr = [
        self::TYPE_OPEN  => '启用',
        self::TYPE_CLOSE => '禁用',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_blog_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['name', 'create_time'], 'string'],
            [['name'], 'unique']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'name'        => '名称',
            'status'      => '状态',
            'create_time' => '创建时间',
        ];
    }

    /**
     * 添加交接类型
     * @param $data
     * @return array
     */
    public function create($data)
    {
        $this->name        = trim($data['name']);
        $this->status      = $data['status'];
        $this->create_time = date('Y-m-d H:i:s', time());
        if (!$this->save()) {
            $error = array_values($this->getFirstErrors())[0];
            return $this->arrData(100, $error);
        }
        return $this->arrData(200, '添加成功');
    }

    /**
     * 修改交接类型
     * @param $data
     * @return array
     * @throws \yii\base\Exception
     */
    public function edit($data)
    {
        $model           = self::findOne($data['id']);
        $model->name     = $data['name'];
        $model->status   = $data['status'];
        if (!$model->save()) {
            $error = array_values($model->getFirstErrors())[0];
            return $this->arrData(100, $error);
        }
        return $this->arrData(200, '更新成功');
    }

    /**
     * 检查交接类型是否存在
     * @param $data
     * @return bool
     */
    public function check($data)
    {
        $query = self::find();
        $query->where(['!=', 'id', $data['id']]);
        $query->andFilterWhere(['name'  => $data['name']]);
        if ($query->one()) {
            return false;
        }
        return true;
    }

    /**
     * 获取交接类型
     * @return array
     */
    public function getType()
    {
        $query = self::find();
        $query->select(['id', 'name']);
        $query->where(['status' => self::TYPE_OPEN]);
        $query->asArray();
        $data = $query->all();
        return $data ? ArrayHelper::map($data, 'id', 'name') : [];
    }

}

