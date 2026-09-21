<?php
namespace backend\models\search;

use backend\models\Base;
use backend\models\Train;
use backend\models\TrainCheck;
use backend\models\User;
use backend\models\UserInfo;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use Yii;

class TrainCheckSearch extends TrainCheck
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
            'tc.train_id',
            't.title',
            'u.chinese_name',
            'tc.id',
            'tc.create_time',
            'tc.status',
        ]);
        $query->from(TrainCheck::tableName() . ' tc');
        $query->leftJoin(Train::tableName() . ' t', 'tc.train_id = t.id');
        $query->leftJoin(UserInfo::tableName() . ' u', 't.user_id = u.id');
        $query->where(['tc.user_id' => $uId]);
        $query->andFilterWhere(['tc.status' => $this->status]);
        $query->andFilterWhere(['like', 'u.chinese_name', trim($this->name)]);
        $query->andFilterWhere(['like', 't.title', trim($this->title)]);
        $query->orderBy(['tc.id' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }

}