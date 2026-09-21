<?php

namespace backend\models;
use Yii;
use yii\db\Exception;

/**
 * This is the model class for table "nxp_blog".
 *
 * @property string $id
 * @property int $user_id
 * @property int $status
 * @property int $read_all
 * @property int $group_id
 * @property string $title
 * @property string $content_data
 * @property int $is_draft
 * @property string $alarm_time
 * @property string $create_time
 * @property string $update_time
 */
class Blog extends Base
{
    const STATUS_PROCESSING = 2; //进行中
    const STATUS_DEFERRED   = 3; //已过期
    const STATUS_SUCCESS    = 4; //已完成

    //查询状态
    public static $searchStatusArr = [
        self::STATUS_PROCESSING => '进行中',
        self::STATUS_DEFERRED   => '已过期',
    ];

   //添加状态
    public static $addStatusArr = [
        self::STATUS_PROCESSING => '进行中',
        self::STATUS_DEFERRED   => '已过期',
        self::STATUS_SUCCESS    => '已完成',
    ];

    //完成状态
    public static $statusArr = [
        self::STATUS_PROCESSING => '进行中',
        self::STATUS_DEFERRED   => '已过期',
        self::STATUS_SUCCESS    => '已完成',
    ];

    //日志类型
    const TYPE_MATERIAL = 1;
    const TYPE_FACILITY = 2;
    const TYPE_5S       = 3;
    const TYPE_ABNORMAL = 4;
    const TYPE_OTHER    = 5;
    const TYPE_FLT      = 6;

    public static $typeList = [
        self::TYPE_MATERIAL => '物料',
        self::TYPE_FACILITY => '设备',
        self::TYPE_5S       => '5s',
        self::TYPE_ABNORMAL => '异常',
        self::TYPE_OTHER    => 'OTHER',
        self::TYPE_FLT      => 'FLT',
    ];

    const SEND_MYSELF = 1; //我发起的
    const ASSOCIATE   = 2; //与我相关

    const IS_DRAFT_YES = 1; //草稿
    const IS_DRAFT_NO  = null; //日志

    //日志相关
    public static $aboutArr = [
        self::SEND_MYSELF => '我发起的',
        self::ASSOCIATE   => '与我相关',
    ];

    const READ_FALSE = 0; //所有人不必读
    const READ_TRUE  = 1; //所有人必读

    //必读状态
    public static $readAllArr = [
        self::READ_FALSE => '不必读',
        self::READ_TRUE  => '必读',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_blog';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'status', 'read_all', 'group_id', 'is_draft','type'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['content_data', 'create_time', 'update_time', 'alarm_time'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'           => 'ID',
            'title'        => '标题',
            'read_all'     => '是否必读',
            'content_data' => '内容',
            'is_draft'     => '是否是草稿',
            'user_id'      => '关联者ID',
            'group_id'     => '分组表id',
            'type'         => '日志类型',
            'status'       => '完成状态',
            'alarm_time'   => '预警时间',
            'create_time'  => '创建时间',
            'update_time'  => '创建时间',
        ];
    }

    /**
     * 添加日志信息
     * @param $param
     * @return bool
     */
    public function create($param)
    {
        $this->title        = trim($param['title']);
        $this->content_data = $param['content_data'];
        $this->user_id      = $param['user_id'];
        $this->status       = $param['status'];
        $this->group_id     = $param['group_id'];
        $this->alarm_time   = $param['alarm_time'];
        $this->read_all     = $param['read_all'];
        $this->type         = $param['type'];
        $this->is_draft     = $param['is_draft'];
        $this->create_time  = $this->update_time = date('Y-m-d H:i:s', time());
        if(!$this->save()){
            return false;
        }
        return $this;
    }

    /**
     * 更新日志信息
     * @param $data
     * @return bool
     * @throws \yii\db\Exception
     */
    public function edit($data)
    {
        $transaction        = \Yii::$app->db->beginTransaction();
        $model              = self::findOne($data['id']);
        $model->group_id    = $data['group_id'];
        $model->status      = $data['status'];
        $model->update_time = date('Y-m-d H:i:s', time());
        if (!$model->save()) {
            return false;
        }
        $data['blog_id'] = $data['id'];;
        $detailId = (new BlogDetails())->create($data);
        if (!$detailId) {
            $transaction->rollBack();
            return false;
        }
        $transaction->commit();
        return $detailId;
    }

    public function getShowLog($idList){
        $logList = (new Blog())->find()->alias('b')
            ->select('b.*,aui.chinese_name aname,bui.chinese_name bname')
            ->innerJoin(['bu' => BlogUser::tableName()],'b.id = bu.blog_id')
            ->innerJoin(['aui' => UserInfo::tableName()],'bu.create_user_id = aui.id')
            ->innerJoin(['bui' => UserInfo::tableName()],'bu.accept_user_id = bui.id')
            ->where(['b.id' =>$idList])
            ->asArray()
            ->all();
        if($logList){
            return $logList;
        }else{
            return false;
        }
    }

    /**
     * 获取发起者姓名
     * @param $id
     * @return string
     */
    public function getBlogUserName($id)
    {
        $chineseName = '';
        $query       = self::findOne(['id' => $id]);
        if (!empty($query)) {
            $result      = UserInfo::findOne(['id' => $query->user_id]);
            $chineseName = $result->chinese_name;
        }
        return $chineseName;
    }

    /**
     *  删除草稿
     */
    public function deleteDraf(){
        $tra = Yii::$app->db->beginTransaction();
        try{
            if(!(new BlogUser)->ByBlogIdDeleteUser($this->id)){
                throw new Exception('日志用户删除失败！');
            }

            if(!(new BlogFile())->ByBlogIdDeleteFile($this->id)){
                throw new Exception('日志图片删除失败！');
            }
            if(!$this->delete()){
                throw new Exception('日志删除失败！');
            }
            $tra->commit();
            return true;

        }catch (\Exception $e){
            $tra->rollBack();
            $this->addError('id',$e->getMessage());
            return false;
        }


    }
}
