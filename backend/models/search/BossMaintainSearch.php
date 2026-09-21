<?php

namespace backend\models\search;

use backend\models\BossMaintainLeader;
use backend\models\Facility;
use backend\models\UserInfo;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\BossMaintain;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * BossMaintainSearch represents the model behind the search form of `backend\models\BossMaintain`.
 */
class BossMaintainSearch extends BossMaintain
{
    const CREATE_KEY = 1;
    const ACCESS_KEY = 2;
    const DISPOSE_KEY= 3;
    public $filtrate;

    public $start_time;
    public $end_time;
    public $area;
    public $shift;
    public $process;//工序
    public $issue_type;
    public $finishMaintain;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'f_id', 'status', 'finishMaintain', 'shift', 'area', 'head_user', 'feedback_user', 'rejects', 'create_time', 'update_time'], 'integer'],
            [['number', 'location_num', 'things', 'filtrate', 'files', 'start_time', 'end_time', 'process', 'issue_type'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = BossMaintain::find()->alias('m')
            ->leftJoin(['f' => Facility::tableName()], 'm.f_id = f.id')
            ->innerJoin(['u' => UserInfo::tableName()],'m.create_user = u.id');
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'm.area' => $this->area,
        ]);
        $userId = \Yii::$app->user->identity->user_id;

        //我创建的
        if($this->filtrate == self::CREATE_KEY){
            $query->where(['m.create_user' => $userId]);

        }elseif($this->filtrate == self::DISPOSE_KEY){

            $query->andWhere(new Expression('m.feedback_user = '.$userId . ' or m.head_user ='.$userId .' OR (select user_id from nxp_boss_maintain_leader where feedback_user = 1) != null '));

        }
        if(!empty($this->start_time)){
            $start_time = strtotime($this->start_time);
            $query->andFilterWhere(['>','m.create_time',$start_time]);
        }
        if(!empty($this->end_time)){
            $end_time = strtotime($this->end_time);
            $query->andFilterWhere(['<','m.create_time',$end_time]);
        }
        if ($this->finishMaintain){
            $query->andWhere(['!=','m.status' , self::STATUS_MARCH]);
        }

        $query->andFilterWhere(['u.shift'=> $this->shift ]);
        $query->andFilterWhere(['f.process'=> $this->process ]);
        $query->orderBy('id desc');
        return $dataProvider;
    }


    /**
     * 查询柱形图需要的异常信息
     * @param $params
     * @return array|\yii\db\ActiveRecord[]
     */
    public function searchBarInfo($params){
        $query = self::find()->alias('m')
            ->innerJoin(['f' => Facility::tableName()],'m.f_id = f.id');
        $this->load($params);
        if(empty($this->start_time) && empty($this->end_time)){
            return false;
        }
        if(!empty($this->start_time)){
            $start_time = strtotime($this->start_time);
            $query->andFilterWhere(['>','m.create_time',$start_time]);
        }
        if(!empty($this->end_time)){
            $end_time = strtotime($this->end_time);
            $query->andFilterWhere(['<','m.create_time',$end_time]);
        }

        $query->andFilterWhere(['m.area' => $this->area]);
        $query->select('m.id,m.area,m.create_time');
        $query->asArray()->orderBy('create_time');

        $data = $query->all();
        return $data;
    }


    /**
     *  查询折线图
     * @param $params
     * @return array|bool|\yii\db\ActiveRecord[]
     */
    public function searchLineInfo($params){
        $query = self::find();
        $this->load($params);
        if(empty($this->start_time) && empty($this->end_time)){
            return false;
        }

        if(!empty($this->start_time)){
            $start_time = strtotime($this->start_time);
            $query->andFilterWhere(['>','create_time',$start_time]);

        }
        if(!empty($this->end_time)){
            $end_time = strtotime($this->end_time);
            $query->andFilterWhere(['<','create_time',$end_time]);
        }
        $query->andFilterWhere(['area' => $this->area]);
        $query->select('id, create_time');
        $query->asArray()->orderBy('create_time');
        $data = $query->all();
        return $data;
    }

    /**
     * 搜索导出的数据
     * @param $params
     * @return array
     */
    public function exportSearch($params){
        $idList  = ArrayHelper::getValue($params, 'idlist', []);
        $dataProvider = $this->search($params);
        $query = $dataProvider->query;
        if (!empty($idList)) {
            $query->andWhere(['m.id' => $idList]);
        }
        return $dataProvider->getModels();
    }

}
