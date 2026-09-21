<?php
namespace  backend\models\search;

use backend\models\LogHandover;
use yii\data\ActiveDataProvider;
use backend\models\LogHandoverBatch;

class LogHandoverAuditSearch extends LogHandover {

    public $title;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['content','date','classes','section'], 'safe'],
        ];
    }

    /**
     * 数据查询
     * @param $param
     * @return ActiveDataProvider
     */
    public function search($param)
    {
        $batchIdArr = LogHandoverBatch::find()->select('id')->where(['is_audit' => 1])->column();
        $this->setAttributes($param);
        $query = LogHandover::find();
        $query->where(['is_del_audit' => 0]);
        $query->andWhere(['is_audit' => 1]);
        $query->andWhere(['not in', 'batch_id', $batchIdArr]);
        $query->andFilterWhere(['like', 'content_audit', trim($this->content)]);
        $query->andFilterWhere(['date' => $this->date]);
        $query->andFilterWhere(['classes' => $this->classes]);
        $query->andFilterWhere(['section' => $this->section]);
        $query->orderBy(['id' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }

}
