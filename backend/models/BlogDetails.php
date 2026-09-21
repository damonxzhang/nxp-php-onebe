<?php

namespace backend\models;

/**
 * This is the model class for table "nxp_blog_details".
 *
 * @property string $id
 * @property int $blog_id
 * @property int $user_id
 * @property int $status
 * @property int $type
 * @property string $reply
 * @property string $create_time
 */
class BlogDetails extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_blog_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['blog_id', 'user_id', 'status', 'type'], 'integer'],
            [['create_time', 'reply'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'reply'       => '反馈内容',
            'blog_id'     => '关联日志ID',
            'user_id'     => '修改人ID',
            'type'        => '交接类型',
            'status'      => '完成状态',
            'create_time' => '创建时间',
        ];
    }


    /**
     * 添加日志信息
     * @param $param
     * @return bool
     */
    public function create($param)
    {
        $this->reply       = $param['reply'];
        $this->blog_id     = $param['blog_id'];
        $this->user_id     = $param['user_id'];
        $this->status      = $param['status'];
        $this->type        = $param['type'];
        $this->create_time = date('Y-m-d H:i:s', time());
        if (!$this->save()) {
            return false;
        }
        return $this->attributes['id'];
    }

    /**
     * 获取日志反馈人员
     * @param $id
     * @return array
     */
    public function getDetailUserId($id)
    {
        return self::find()->select('user_id')->where(['blog_id' => $id])->distinct()->column();
    }

}
