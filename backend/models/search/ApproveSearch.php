<?php

namespace backend\models\search;

use backend\models\ApproveConfirm;
use backend\models\Base;
use backend\models\Approve;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class ApproveSearch extends Approve
{
    public $title;
    public $status;
    public $start_time;
    public $end_time;
    public $associate;
    public $area;
    public $execution;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['title', 'status', 'start_time', 'end_time', 'associate', 'area', 'execution'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * 查询
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query        = $this->getSearchQuery($params);
        $query->andWhere([ 'nxp_approve.is_draft' => self::IS_DRAFT_NO]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }

    /**
     * 查询方法
     * @param $params
     * @return \yii\db\ActiveQuery
     */
    public function getSearchQuery($params)
    {
        $this->setAttributes($params);
        $query = Approve::find();
        $uid   = (new Base())->getUid();
        //我发起的
        if ($this->associate == Approve::SEND_MYSELF) {
            $query->andWhere(['user_id' => $uid]);
        } elseif ($this->associate == Approve::ASSOCIATE) {
            $idList = ApproveConfirm::find()->select('approve_id')->where(['user_id' =>$uid])->groupBy('approve_id')->column();
            $query->andWhere(['in', 'id', $idList]);
        }
        //执行状态筛选
        if ($this->execution) {
            $sign = $this->execution == Approve::EXPIRED ? '>=': '<=';
            $nowTime = date("Y-m-d", time());//当前时间
            $query->andWhere(['or',[$sign,'effective_time',$nowTime],[$sign,'first_delay_time',$nowTime],[$sign,'second_delay_time',$nowTime]]);
        }
        $query->andFilterWhere(['area' => $this->area]); //区域筛选
        $query->andFilterWhere(['status' => $this->status]);//审批状态
        $query->andFilterWhere(['>=', 'create_time', ArrayHelper::getValue($this, 'start_time', '')]);//创建时间筛选
        $query->andFilterWhere(['<=', 'create_time', ArrayHelper::getValue($this, 'end_time', '')]);
        $query->andFilterWhere(['like', 'title', trim($this->title)]); //标题筛选
        $query->orderBy(['nxp_approve.alarm_time' => SORT_DESC]);
        return $query;
    }

    /**
     * 查询草稿
     * @param $params
     * @return ActiveDataProvider
     */
    public function searchDraft($params)
    {
        $this->setAttributes($params);
        $uid   = (new Base())->getUid();
        $query = Approve::find();
        $query->where(['user_id' => $uid]);
        $query->andWhere(['is_draft' => self::IS_DRAFT_YES]);
        $dataProvider = new ActiveDataProvider([
            'query'        => $query,
            'pagination'   => [
                'pageSize' => 20,
            ],
        ]);
        return $dataProvider;
    }

    /**
     * 获取日志状态
     * @param $status
     * @return string
     */
    public function getStatus($status)
    {
        $result = '';
        switch ($status) {
            case Approve::STATUS_PROCESSING:
                $str    = Approve::$statusArr[$status]; //进行中
                $result = '<button type="button" class="layui-btn layui-btn-xs layui-bg-orange">' . $str . '</button>';
                break;
            case Approve::STATUS_DEFERRED:
                $str    = Approve::$statusArr[$status]; //已延期
                $result = '<button type="button" class="layui-btn layui-btn-xs layui-bg-blue">' . $str . '</button>';
                break;
            case Approve::STATUS_SUCCESS:
                $str    = Approve::$statusArr[$status];  //已完成
                $result = '<button type="button" class="layui-btn layui-btn-xs layui-bg-cyan">' . $str . '</button>';
                break;
        }
        return $result;
    }

    /**
     * 检查当前人员是否可以编辑
     * @param $id
     * @return bool
     */
    public function checkUserUpdate($id)
    {
        $uid   = (new Base())->getUid();
        $model = Approve::findOne(['id' => $id]);
        if($model->user_id != $uid) return false;
        if($model->progress != '100') return false;
        if($model->finish == Approve::FINISH_SUCCESS) return false;
        return TRUE;
    }

    /**
     * 检查人员是否可以结束消息
     * @param $id
     * @return bool
     */
    public function checkFinishUpdate($id)
    {
        $uid   = (new Base())->getUid();
        $model = Approve::findOne(['id' => $id]);
        if($model->user_id != $uid) return false;
        if($model->progress != '100') return false;
        return TRUE;
    }

    /**
     * 检查人员是否可以确认消息
     * @param $id
     * @return bool
     */
    public function checkUserConfirm($id)
    {
        $model = Approve::findOne(['id' => $id]);
        if ($model->finish == Approve::FINISH_SUCCESS) return FALSE;
        if ($model->status != Approve::STATUS_SUCCESS) return FALSE;
        $uid                 = (new Base())->getUid();
        $approveConfirmModel = ApproveConfirm::findOne([
            'approve_id' => $id,
            'user_id'    => $uid,
            'status'     => ApproveConfirm::STATUS_NO,
            'unusual'    => ApproveConfirm::UNUSUAL_TRUE,
        ]);
        if (!$approveConfirmModel) return FALSE;
        return TRUE;
    }
}