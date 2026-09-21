<?php

namespace backend\models\search;

use backend\models\Facility;
use backend\models\MaintainFeedback;
use backend\models\MaintainType;
use backend\models\service\MaintainService;
use Yii;
use backend\models\UserInfo;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Maintain;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * MaintainApi represents the model behind the search form of `backend\models\MaintainApi`.
 */
class MaintainSearch extends Maintain
{

    const CREATE_KEY = 1;
    const ACCESS_KEY = 2;
    const DISPOSE_KEY= 3;
    public $filtrate;

    public $start_time;
    public $end_time;
    public $area;
    public $shift;
    public $process;
    public $issue_type;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'f_id', 'operation_user', 'happen_date', 'shift', 'director_user', 'things', 'create_user', 'type', 'rejects' , 'access_status', 'access_user', 'access_date', 'feedback_user', 'create_time', 'update_time', 'delete_time'], 'integer'],
            [['filtrate','area','issue_type'],'integer' ],
            [['number', 'process','start_time','end_time', 'files', 'access_advice' ], 'safe'],
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
        $query = Maintain::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->alias('m')
            ->leftJoin(['f' => Facility::tableName()], 'm.f_id = f.id')
            ->innerJoin(['u' => UserInfo::tableName()],'m.create_user = u.id');

        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $userId = Yii::$app->user->identity->user_id;
        //我创建的
        if($this->filtrate == self::CREATE_KEY){
            $query->where(['m.create_user' => $userId]);
        //我审批的
        }elseif($this->filtrate == self::ACCESS_KEY){
            $query->where(['u.leader_uid' => $userId, 'm.access_user' => null]);

        //待我处理
        }elseif($this->filtrate == self::DISPOSE_KEY){
            $query->andWhere(new Expression('m.feedback_user = '.$userId . ' or m.other_feedback_user ='.$userId ))
            ->andWhere(new Expression( 'm.access_user IS NOT NULL'));
        }
        $query->andWhere([ 'm.status' => self::STATUS_MARCH]);
        $query->andFilterWhere([ 'm.type' => $this->type]);
        $query->andFilterWhere(['u.shift'=> $this->shift ]);
        $query->andFilterWhere(['m.area'=> $this->area]);
        $query->andFilterWhere(['f.process'=> $this->process ]);

        if(!empty($this->start_time)){
            $start_time = strtotime($this->start_time);
            $query->andFilterWhere(['>','m.create_time',$start_time]);
        }
        if(!empty($this->end_time)){
            $end_time = strtotime($this->end_time);
            $query->andFilterWhere(['<','m.create_time',$end_time]);
        }
        $query->orderBy('id desc');
        return $dataProvider;
    }
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchBoss($params)
    {
        $query = Maintain::find();

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
        $query->where(['boss' => self::SELECT_YES]);

        //我创建的
        if($this->filtrate == self::CREATE_KEY){

            $query->andWhere(['create_user' => Yii::$app->user->identity->user_id]);
        //我审批的
        }elseif($this->filtrate == self::ACCESS_KEY){
            $query->innerJoin(['u' => UserInfo::tableName()],self::tableName().'.create_user = u.id')
                ->andWhere(['u.leader_uid' => Yii::$app->user->identity->user_id,self::tableName().'.access_user' => null]);

        //待我处理
        }elseif($this->filtrate == self::DISPOSE_KEY){
            $query->innerJoin(['f' => Facility::tableName()],self::tableName().'.f_id = f.id')
                ->andWhere(['f.head_user' => Yii::$app->user->identity->user_id])
                ->andWhere(['IS NOT', self::tableName().'.access_user',NULL]);
        }
        $query->orderBy('id desc');

        return $dataProvider;
    }
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchFinish($params)
    {
        $query = Maintain::find();
        $query->alias('m')
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
        $query->andWhere(['!=','m.status', self::STATUS_MARCH]);
        $query->andFilterWhere([ 'm.type' => $this->type]);
        $query->andFilterWhere([ 'm.area' => $this->area]);
        $query->andFilterWhere([ 'f.shift' => $this->shift]);
        $query->andFilterWhere([ 'f.process' => $this->process]);

        if(!empty($this->start_time)){
            $start_time = strtotime($this->start_time);
            $query->andFilterWhere(['>','m.create_time',$start_time]);
        }
        if(!empty($this->end_time)){
            $end_time = strtotime($this->end_time);
            $query->andFilterWhere(['<','m.create_time',$end_time]);
        }
        $query->orderBy('id desc');

        return $dataProvider;
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

    /**
     * 搜索已完成的导出
     * @param $params
     * @return array
     */
    public function exportFinishSearch($params){
        $idList  = ArrayHelper::getValue($params, 'idlist', []);
        $dataProvider = $this->searchFinish($params);
        $query = $dataProvider->query;
        if (!empty($idList)) {
            $query->andWhere(['m.id' => $idList]);
        }
        return $dataProvider->getModels();
    }
    /**
     * 搜索已完成的导出
     * @param $params
     * @return array
     */
    public function exportFinishData($params){
        $this->load($params);
        $query = Maintain::find();
        $query->alias('m')
            ->leftJoin(['f' => Facility::tableName()], 'm.f_id = f.id')
            ->innerJoin(['u' => UserInfo::tableName()],'m.create_user = u.id');
        $query->andWhere(['!=','m.status', self::STATUS_MARCH]);
        $query->andFilterWhere([ 'm.type' => $this->type]);
        $query->andFilterWhere([ 'm.area' => $this->area]);
        $query->andFilterWhere([ 'f.shift' => $this->shift]);
        $query->andFilterWhere([ 'f.process' => $this->process]);

        if(!empty($this->start_time)){
            $start_time = strtotime($this->start_time);
            $query->andFilterWhere(['>','m.create_time',$start_time]);
        }
        if(!empty($this->end_time)){
            $end_time = strtotime($this->end_time);
            $query->andFilterWhere(['<','m.create_time',$end_time]);
        }
        $idList  = ArrayHelper::getValue($params, 'idlist', []);
        if (!empty($idList)) {
            $query->andWhere(['m.id' => $idList]);
        }
        $query->orderBy('id desc');
        $query->asArray();
        return $query->all();
    }


    public function getDataByRequest($params){
        $query = self::find();
        $this->load($params);
        if(!$this->validate()){
            return false;
        }
        $serviceModel = new MaintainService();

        if(!empty($this->f_id)){

            $data = $this->searchInfoByFacility($query);
            $thingsList =  MaintainType::getTypeList();
            return [$serviceModel->setInfoByFacility($data,$thingsList,'things'),$thingsList];

        }elseif (!empty($this->process)){

            $data = $this->searchInfoByProcess($query);
            if(!empty($data)){
                $fIds =  ArrayHelper::getColumn($data,'f_id') ;
                asort($fIds);
                $facilityList = (new Facility())->getNumberByIds($fIds);
                return [$serviceModel->setInfoByFacility($data,$facilityList,'f_id'),$facilityList];
            }
            return false;
        }elseif(!empty($this->start_time) || !empty($this->end_time) || !empty($this->type)){
            $data = $this->searchReportInfo($query);
            $processList = Facility::$processList;
            return [$serviceModel->setInfoByFacility($data,$processList,'process'),$processList];
        }
    }

    /**
     * 根据设备统计
     * @param $query
     * @return mixed
     */
    public function searchInfoByFacility($query){

        if(!empty($this->start_time)){
            $start_time = strtotime($this->start_time);
            $query->andFilterWhere(['>','create_time',$start_time]);

        }
        if(!empty($this->end_time)){
            $end_time = strtotime($this->end_time);
            $query->andFilterWhere(['<','create_time',$end_time]);
        }
        $query->andFilterWhere([
            'f_id' => $this->f_id,
            'process' => $this->process,
        ]);

        $query->select('things,'.new Expression("count(things) as num"))->groupBy('things');
        $query->asArray()->orderBy('things');
        $data = $query->all();
        return $data;
    }

    /**
     * 根据工序统计
     * @param $query
     * @return mixed
     */
    public function searchInfoByProcess($query){
        $query->alias('m');
        if(!empty($this->start_time)){
            $start_time = strtotime($this->start_time);
            $query->andFilterWhere(['>','m.create_time',$start_time]);
        }
        if(!empty($this->end_time)){
            $end_time = strtotime($this->end_time);
            $query->andFilterWhere(['<','m.create_time',$end_time]);
        }
        if($this->process){
            $query->innerJoin(['f' => Facility::tableName()], 'm.f_id = f.id')
            ->andWhere([ 'f.process' => $this->process]);
        }


        $query->select('m.f_id, '.new Expression("count(f_id) as num"))->groupBy('f_id');
        $query->asArray();
        $data = $query->all();
        return $data;

    }

    /**
     * 查询数据统计异常信息
     * @param $params
     * @return array|\yii\db\ActiveRecord[]
     */
    public function searchReportInfo($query){
        $query->alias('m');
        $query->innerJoin(['f' => Facility::tableName()], 'm.f_id = f.id');

        if(!empty($this->start_time)){
            $start_time = strtotime($this->start_time);
            $query->andFilterWhere(['>','m.create_time',$start_time]);

        }
        if(!empty($this->end_time)){
            $end_time = strtotime($this->end_time);
            $query->andFilterWhere(['<','m.create_time',$end_time]);
        }
        $query->andFilterWhere([
            'm.f_id' => $this->f_id,
            'f.process' => $this->process,
            'f.area' => $this->area,
            'm.type' => $this->type
        ]);

        $query->select('f.process,'.new Expression("count(m.id) as num"))->groupBy('f.process');
        $query->asArray()->orderBy('f.process');

        $data = $query->all();
        return $data;
    }

}
