<?php

namespace backend\models;

use common\models\Utility;
use Yii;
use yii\db\Exception;

/**
 * This is the model class for table "nxp_approve".
 *
 * @property string $id
 * @property int $user_id
 * @property int $status
 * @property int $leader_id
 * @property int $group_id
 * @property int $is_draft
 * @property int $type
 * @property int $progress
 * @property int $finish
 * @property string $title
 * @property string $area
 * @property string $number
 * @property string $station
 * @property string $shift
 * @property string $self_number
 * @property string $content_data
 * @property string $alarm_time
 * @property string $create_time
 * @property string $effective_time
 * @property string $update_time
 * @property string $first_delay_time
 * @property string $second_delay_time
 */
class Approve extends Base
{

    //审批状态
    const STATUS_PROCESSING = 1; //待审批
    const STATUS_SUCCESS    = 2; //同意
    const STATUS_DEFERRED   = 3; //不同意
    public static $statusArr = [
        self::STATUS_PROCESSING => '待审批',
        self::STATUS_SUCCESS    => '同意',
        self::STATUS_DEFERRED   => '不同意',
    ];

    //执行状态
    const INPROFRESS = 1; //进行中
    const EXPIRED    = 2; //已过期
    public static $executionStatusArr = [
        self::INPROFRESS => '进行中',
        self::EXPIRED    => '已过期',
    ];

    //完成状态
    const FINISH_FAIL    = 1; //未完成
    const FINISH_SUCCESS = 2; //已完成
    public static $finishArr = [
        self::FINISH_FAIL    => '未完成',
        self::FINISH_SUCCESS => '已完成',
    ];

    const IS_DRAFT_YES = 1; //草稿
    const IS_DRAFT_NO  = 2; //消息

    //日志相关
    const SEND_MYSELF = 1; //我发起的
    const ASSOCIATE   = 2; //与我相关
    public static $aboutArr = [
        self::SEND_MYSELF => '我发起的',
        self::ASSOCIATE   => '与我相关',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_approve';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'status', 'leader_id', 'is_draft', 'progress', 'finish'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['number', 'station', 'shift', 'self_number', 'area', 'content_data', 'create_time', 'update_time', 'effective_time', 'alarm_time', 'first_delay_time', 'second_delay_time'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                => 'ID',
            'title'             => '标题',
            'number'            => '编号',
            'self_number'       => '自增编号',
            'content_data'      => '内容',
            'is_draft'          => '是否是草稿',
            'user_id'           => '创建者id',
            'leader_id'         => '直属领导id',
            'status'            => '审批状态',
            'finish'            => '完成状态',
            'progress'          => '签到率',
            'station'           => '站别',
            'shift'             => '班别',
            'area'              => '所属区域',
            'effective_time'    => '生效日期',
            'alarm_time'        => '过期时间',
            'create_time'       => '创建时间',
            'update_time'       => '更新时间',
            'first_delay_time'  => '首次延期时间',
            'second_delay_time' => '二次延期时间',
        ];
    }

    /**
     * 添加信息
     * @param $param
     * @return bool
     */
    public function create($param)
    {
        $this->title        = trim($param['title']);
        $this->content_data = $param['content_data'];
        $this->user_id      = $param['user_id'];
        $this->status       = $param['status'];
        $this->number       = $param['number'];
        $this->self_number  = $param['self_number'];
        $this->leader_id    = $param['leader_id'];
        $this->is_draft     = $param['is_draft'];
        $this->finish       = self::FINISH_FAIL;
        $this->station      = isset($param['station']) ? implode(',', $param['station']) : '';
        $this->shift        = isset($param['shift']) ? implode(',', $param['shift']) : '';
        $this->area         = isset($param['area']) ? implode(',', $param['area']) : '';
        $this->create_time  = date('Y-m-d H:i:s', time());
        $this->alarm_time   = $param['alarm_time'];
        if (!$this->save()) {
            $error = array_values($this->getFirstErrors())[0];
            return FALSE;
        }
        return $this->attributes['id'];
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
            return FALSE;
        }
        $data['blog_id'] = $data['id'];;
        $detailId = (new BlogDetails())->create($data);
        if (!$detailId) {
            $transaction->rollBack();
            return FALSE;
        }
        $transaction->commit();
        return $detailId;
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
     * 删除草稿
     * @return bool
     * @throws \Throwable
     */
    public function deleteDraf()
    {
        $tra = Yii::$app->db->beginTransaction();
        try {
            if (!(new ApproveFile())->deleteFileById($this->id)) {
                throw new Exception('消息图片删除失败！');
            }
            if (!$this->delete()) {
                throw new Exception('消息删除失败！');
            }
            $tra->commit();
            return TRUE;
        } catch (\Exception $e) {
            $tra->rollBack();
            $this->addError('id', $e->getMessage());
            return FALSE;
        }
    }

    /**
     * 更新签到率
     * @return bool
     */
    public function updateProgress($id)
    {
        $where = ['approve_id' => $id, 'unusual' => ApproveConfirm::UNUSUAL_TRUE];
        $all   = ApproveConfirm::find()->where($where)->count();
        $yes   = ApproveConfirm::find()->where($where)->andWhere(['status' => [ApproveConfirm::STATUS_YES]])->count();
        if (empty($all) && empty($yes)) {
            return TRUE;
        }
        $progress        = round($yes / $all, 2) * 100;
        $query           = self::findOne(['id' => $id]);
        $query->progress = $progress ? $progress : 0;
        if (!$query->save(FALSE)) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * 生成编号
     * @param $uid
     * @return string
     */
    public function createNumber($uid)
    {
        $area = (new UserInfo())->getAreaById($uid);                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 //获取所属区域
        if (empty($area)) return '';                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                //如果区域为空返回空
        $area       = UserInfo::$areaArr[$area];                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          //获取站别名称
        $area       = Utility::replaceSpecialChar($area);                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 //过滤字符串中特殊符号
        $area       = strtoupper($area);                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  //字符串转大写
        $selfNumber = str_pad($this->getSelfNumber($uid), 3, '0', STR_PAD_LEFT); //形成001格式
        // return $this->getSelfNumber($uid);
        return 'BE' . $area . date('Y') . $selfNumber;            //组成编号
    }

    /**
     * 获取自增数
     * @param $uid
     * @return mixed|string
     */
    public function getSelfNumber($uid)
    {
        $area = (new UserInfo())->getAreaById($uid);//获取用户所属区域
        if (empty($area)) return '0';               //如果区域为空返回空
        $query = self::find();
        $query->where(['area' => $area]);
        $query->andWhere(['!=', 'self_number', '']);
        $selfNumber = $query->max('self_number');
        if ((int)$selfNumber >= 1) {
            $selfNumber = (int)$selfNumber + 1;
        }
        return empty($selfNumber) ? '1' : (string)$selfNumber;
    }

}

