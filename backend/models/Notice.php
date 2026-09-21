<?php

namespace backend\models;

/**
 * This is the model class for table "nxp_notice".
 *
 * @property string $title
 * @property string $content_data
 * @property int $status
 * @property int $type
 * @property int $create_user_id
 * @property string $alarm_time
 * @property int $create_time 创建时间
 */
class Notice extends Base
{
    const TYPE_TRAIN    = 1; //培训
    const TYPE_APPROVAL = 2; //审批
    const TYPE_BLOG     = 3; //日志
    const TYPE_IR       = 4; //日志
    const TYPE_APPROVE  = 5; //通知

    const STATUS_COMPLETED = 1;
    const STATUS_UNDONE    = 2;

    //完成状态
    public static $statusArr = [
        self::STATUS_COMPLETED => '已完成',
        self::STATUS_UNDONE    => '未完成',
    ];

    //培训类型
    public static $typeArr = [
        self::TYPE_TRAIN    => '培训',
        self::TYPE_APPROVAL => '审批',
        self::TYPE_BLOG     => '日志',
        self::TYPE_IR       => 'IR',
        self::TYPE_APPROVE  => '通知',
    ];


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_notice';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'create_user_id', 'type'], 'integer'],
            [['title', 'content_data', 'create_time', 'alarm_time'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'             => 'ID',
            'title'          => '标题',
            'content_data'   => '内容',
            'type'           => '培训类型',
            'create_user_id' => '创建者ID',
            'status'         => '状态',
            'alarm_time'     => '报警时间',
            'create_time'    => '创建时间',
        ];
    }

    /**
     * 添加数据
     * @param $data
     * @return bool
     */
    public function create($data)
    {
        $this->title          = trim($data['title']);
        $this->content_data   = trim($data['content_data']);
        $this->status         = self::STATUS_UNDONE;
        $this->type           = isset($data['type']) ? $data['type'] : '';
        $this->create_user_id = $this->getUid();
        $this->alarm_time     = isset($data['alarm_time']) ? $data['alarm_time'] : '';
        $this->create_time    = date('Y-m-d H:i:s', time());
        if (!$this->save()) {
            return false;
        }
        return $this->attributes['id'];
    }

    /**
     * 修改数据
     * @param $data
     * @return array
     */
    public function edit($data)
    {
        $model          = self::findOne($data['id']);
        $model->title   = $data['title'];
        $model->content_data = $data['content_data'];
        $model->status  = $data['status'];
        if (!$model->save()) {
            $error = array_values($model->getFirstErrors())[0];
            return $this->arrData(100, $error);
        }
        return $this->arrData(200, '更新成功');
    }

    /**
     * 获取站别
     * @return array
     */
    public function getStation()
    {
        $query = self::find();
        $query->select(['id', 'station']);
        $query->where(['status' => self::STATUS_UNDONE]);
        $query->asArray();
        return $query->all();
    }

}


