<?php

namespace backend\models;

/**
 * This is the model class for table "nxp_flow".
 *
 * @property string $id 主流程审批表
 * @property string $name 审批流程名称
 * @property string $remark 备注
 * @property int $status 状态
 * @property string $create_time 创建时间
 */
class Flow extends Base
{
    const STATUS_OPEN = 1;
    const STATUS_CLOSE = 2;

    public static $statusArr = [
        self::STATUS_OPEN => '启用',
        self::STATUS_CLOSE => '禁用',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_flow';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['name'], 'string', 'max' => 64],
            [['remark'], 'string', 'max' => 255],
            [['create_time'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'name'        => '审批流程名称',
            'remark'      => '备注',
            'status'      => '状态',
            'create_time' => '创建时间',
        ];
    }

    /**
     * 创建流程
     * @param $param
     * @return bool
     */
    public function create($param)
    {
        $this->name        = trim($param['name']);
        $this->remark      = $param['remark'];
        $this->status      = $param['status'];
        $this->create_time = date('Y-m-d H:i:s', time());
        if (!$this->save()) {
            return false;
        }
        return $this->attributes['id'];
    }

    /**
     * 修改流程
     * @param $param
     * @return array
     */
    public function edit($param)
    {
        $model         = self::findOne($param['id']);
        $model->name   = $param['name'];
        $model->remark = $param['remark'];
        $this->status  = $param['status'];
        if (!$model->save()) {
            $error = array_values($model->getFirstErrors())[0];
            return $this->arrData(100, $error);
        }
        return $this->arrData(200, '更新成功');
    }

    /**
     * 获取流程名称
     * @param $id
     * @return string
     */
    public function getNameById($id)
    {
        $quest = self::findOne(['id' => $id]);
        return $quest->name;
    }

    /**
     * 获取节点名称
     * @param string $status
     * @return array
     */
    public function getFlow($status = '')
    {
        $data = [];
        $quest = self::find();
        $quest->select(['id', 'name']);
        $quest->andFilterWhere(['status' => $status]);
        $quest->orderBy(['id' => SORT_DESC]);
        $flowModel = $quest->all();
        if(!empty($flowModel)){
            foreach ($flowModel as $value){
                $data[$value['id']] = $value['name'];
            }
        }
        return $data;
    }

}
