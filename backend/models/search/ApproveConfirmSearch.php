<?php
namespace  backend\models\search;

use backend\models\ApproveConfirm;
use backend\models\UserInfo;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class ApproveConfirmSearch extends ApproveConfirm {

    public $start_time;
    public $end_time;
    public $shift;
    public $approve_id;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['shift', 'start_time', 'end_time', 'approve_id'], 'safe'],
        ];
    }

    /**
     * 数据查询
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $this->setAttributes($params);
        $query = ApproveConfirm::find();
        $query->leftJoin(UserInfo::tableName() . ' u', 'nxp_approve_confirm.user_id = u.id');
        $query->where(['nxp_approve_confirm.approve_id' => $this->approve_id]);
        $query->andFilterWhere(['u.shift' => $this->shift]);
        $query->andFilterWhere(['>=', 'nxp_approve_confirm.confirm_time', ArrayHelper::getValue($this, 'start_time', '')]);
        $query->andFilterWhere(['<=', 'nxp_approve_confirm.confirm_time', ArrayHelper::getValue($this, 'end_time', '')]);
        $query->orderBy(['nxp_approve_confirm.create_time' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query'      => $query
        ]);
        return $dataProvider;
    }

}