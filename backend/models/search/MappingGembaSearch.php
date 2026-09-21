<?php

namespace backend\models\search;

use backend\models\Judge;
use backend\models\Team;
use backend\models\UserInfo;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\NewGembaMapping;
use yii\db\Expression;
use yii\helpers\Html;

/**
 * NewGembaSearch represents the model behind the search form of `backend\models\NewGemba`.
 */
class MappingGembaSearch extends NewGembaMapping
{

    /**
     * 筛选角色
     */
    const ASSESS   = 1;//评审员
    const WORK     = 2;//实施人
    const CONFIRM  = 3;//待我确认
    const THIS     = 4;//我参与的
    const BE_RELATED_TO = 5;//与我相关
    const LEADER   = 6;//主管审核

    public $relevant;//我参与页面筛选条件
    public $start_date;
    public $end_date;
    public $team;
    public $shift;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type', 'norm', 'steps', 'week', 'area', 'shift', 'number', 'relevant', 'create_user', 'auto_complete', 'pm_visible', 'assess_user', 'assess_status', 'assess_time', 'work_status', 'work_user', 'start_time', 'end_time', 'status', 'confirm_time' ], 'integer'],
            [['year','title', 'content', 'files', 'start_date', 'end_date', 'participant', 'work_advice', 'confirm_content'], 'safe'],
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
        $query = NewGembaMapping::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        $query->alias('g');

        $query->where([
            'g.draft' => self::DRAFT_NO,
        ]);
        // grid filtering conditions
        $query->andFilterWhere([
            'g.type' => $this->type,
            'g.status' => $this->status,
            'g.create_user' => $this->create_user,
            'g.auto_complete' => $this->auto_complete,
            'g.steps' => $this->steps,
            'g.work_user' => $this->work_user,
            'g.year' => $this->year,
        ]);

        if(!empty($this->start_date) && !is_int($this->start_date)){
            $start_date = strtotime($this->start_date);
            $query->andFilterWhere([
                '>','g.create_time',$start_date
            ]);
        }
        if(!empty($this->end_date) && !is_int($this->end_date)){
            $end_date = strtotime($this->end_date);
            $query->andWhere([
                '<', 'g.create_time',$end_date
            ]);
        }
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->innerJoin(['u' => UserInfo::tableName()],'g.create_user = u.id');

        if (!empty($this->relevant) || !empty($this->area) || !empty($this->shift)) {
            $userInfo = \Yii::$app->user->identity->userInfo;

            if(!empty($this->relevant)){
                $orWhere = 'u.id = '.$userInfo->id. ' or g.work_user ='.$userInfo->id;

                if(Judge::isJudge(Judge::TYPE_GEMBA )){
                    $orWhere .= ' or g.area='.$userInfo->area ;
                }
                $query->andWhere( new Expression($orWhere));

            }elseif(!empty($this->area) || !empty($this->shift)){
                $query->andFilterWhere(['u.area' => $this->area]);
                $query->andFilterWhere(['u.shift' => $this->shift]);
            }
        }

        $query->andFilterWhere(['like', 'g.title', $this->title])
            ->andFilterWhere(['like', 'g.number', $this->number])
            ->orderBy('g.number desc');
        return $dataProvider;
    }

    /**
     * 查询导出excel数据
     * @param $query
     * @param $id
     * @param $history
     * @return array
     */
    public function searchExcelData($query,$ids,$history){
        $dataProvider = $this->search($query);
        $dataProvider->query->leftJoin(['t' => Team::tableName()],'g.team = t.id')
        ->select('g.*,g.shift,u.chinese_name,u.department_number,u.pid,u.area,t.teamName')
        ->orderBy('id desc')
        ->asArray();
        if(!empty($ids)){
            $dataProvider->query->andWhere(['g.id' => $ids]);
        }
        if($history){
            $dataProvider->query->andWhere(['g.status' => NewGemba::STATUS_COMPLETE]);
        }
        return $dataProvider->query->all();
    }

   
    /**
     * 相关搜索
     * @param $params
     * @return ActiveDataProvider
     */
    public function searchCorrelation($params){
        $query = NewGembaMapping::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $query->where([
            'draft' => self::DRAFT_NO,
            self::tableName().'.status'=> self::STATUS_PERFORM
        ]);

        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        //待我审批
        if($this->relevant == self::ASSESS && Judge::isJudge(Judge::TYPE_GEMBA)){
            $userInfo = \Yii::$app->user->identity->userInfo;
            $query->andWhere(['steps' => self::CREATE_STEPS])
                ->innerJoin(['u' => UserInfo::tableName()],self::tableName().'.create_user = u.id')
                ->andWhere( [self::tableName().'.area' => $userInfo->area] )
                ->andWhere(['auto_complete' => self::AUTO_COMPLETE_NO]);
        //待我实施
        }elseif($this->relevant == self::WORK){
            $query->andWhere(['work_user' => \Yii::$app->user->identity->user_id])
                ->andWhere(new Expression('steps = '.self::ASSESS_STEPS.' or steps ='.self::WORK_STEPS));
        //待我确认
        }elseif($this->relevant == self::CONFIRM){
            $user_id = \Yii::$app->user->identity->user_id;
            $query->innerJoin(['u' => UserInfo::tableName()],self::tableName().'.create_user = u.id')
                ->andWhere(['create_user' => $user_id])
                ->andWhere(['steps' => self::CONFIRM_STEPS]);
        //主管确认
        }elseif($this->relevant == self::LEADER){
            $user_id = \Yii::$app->user->identity->user_id;
            $query->innerJoin(['u' => UserInfo::tableName()],self::tableName().'.create_user = u.id')
                ->andWhere(['u.leader_uid' => $user_id])
                ->andWhere(['steps' => self::LEADER_STEPS]);
        }else{
            //如果是评估人默认显示待我审批
            if(Judge::isJudge(Judge::TYPE_GEMBA)){
                $userInfo = \Yii::$app->user->identity->userInfo;
                $query->andWhere(['steps' => self::CREATE_STEPS])
                    ->innerJoin(['u' => UserInfo::tableName()],self::tableName().'.create_user = u.id')
                    ->andWhere( [self::tableName().'.area'  => $userInfo->area] )
                    ->andWhere(['auto_complete' => self::AUTO_COMPLETE_NO]);
                //反之默认为空
            }else{
                $query->andWhere(['id' => null]);
            }
        }
        $query->orderBy(self::tableName().'.number desc');
        return $dataProvider;
    }

    public function searchBraChartTypeByArea($params,$select){
        $query = self::find()->select($select);
        $this->load($params);
        if(!$this->validate()){
            return [];
        }
        if($this->start_date){
            $query->andWhere(['>=','create_time',strtotime($this->start_date)]);
        }
        if($this->end_date){
            $query->andWhere(['<=','create_time',strtotime($this->end_date)]);
        }
        $query->andFilterWhere(['area' => $this->area])->asArray()->orderBy('create_time');
        $query->andFilterWhere(['type' => $this->type])->asArray()->orderBy('create_time');
        return $query->all();
    }

}
