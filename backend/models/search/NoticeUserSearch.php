<?php
namespace backend\models\search;

use backend\models\Base;
use backend\models\Notice;
use backend\models\NoticeUser;
use backend\models\Train;
use backend\models\TrainCheck;
use backend\models\User;
use backend\models\UserInfo;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use Yii;
use yii\helpers\ArrayHelper;

class NoticeUserSearch extends NoticeUser
{
    public $title;
    public $name;
    public $status;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['title', 'name', 'status'], 'safe'],
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
     * 获取培训状态
     * @param $status
     * @return string
     */
    public function getStatus($status)
    {
        $result = '';
        switch ($status) {
            case TrainCheck::STATUS_CHECKED:
                $str    = TrainCheck::$statusArr[$status]; //已签到
                $result = '<button type="button" class="layui-btn layui-btn-xs">' . $str . '</button>';
                break;
            case TrainCheck::STATUS_NO_SIGNED:
                $str    = TrainCheck::$statusArr[$status]; //未签到
                $result = '<button type="button" class="layui-btn layui-btn-xs layui-btn-danger">' . $str . '</button>';
                break;
        }

        return $result;
    }

    /**
     * 数据查询
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $this->setAttributes($params);
        $uId   = (new Base())->getUid();
        $query = new Query();
        $query->select([
            'nu.notice_id',
            'n.title',
            'n.type',
            'n.create_user_id',
            'nu.id',
            'nu.create_time',
            'nu.status',
        ]);
        $query->from(NoticeUser::tableName() . ' nu');
        $query->leftJoin(Notice::tableName() . ' n', 'nu.notice_id = n.id');
        $query->where(['nu.user_id' => $uId]);
        $query->andWhere(['nu.status' => NoticeUser::STATUS_UNREAD]);
        $query->andFilterWhere(['like', 'n.title', trim($this->title)]);
        $query->orderBy(['nu.status' => SORT_DESC,'nu.id' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }

}