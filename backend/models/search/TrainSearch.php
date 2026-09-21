<?php

namespace backend\models\search;

use backend\models\TrainCourses;
use backend\models\TrainUser;
use backend\models\UserInfo;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Train;
use yii\db\Expression;

/**
 * TrainSearch represents the model behind the search form of `backend\models\Train`.
 */
class TrainSearch extends Train
{

    static public $progressArray = [
        9   => 9,
        19  => 19,
        29  => 29,
        39  => 39,
        49  => 49,
        59  => 59,
        69  => 69,
        79  => 79,
        89  => 89,
        99  => 99,
    ];
    public $partake;

    public $myCreate;
    public $shift;//班别
    public $start_progress;
    public $end_progress;
    public $complete;
    public $coursesName;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['courses', 'partake','start_progress','end_progress'], 'integer'],
            [['end_time','start_time','complete','lecturer','myCreate','shift'], 'safe'],
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
        $query = Train::find()->alias('t');


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

        if(!empty($this->complete)){
            $query->where(new Expression('progress = 100 '));//默认查询完成率低于100%
        }else{
            $query->where(['<', 'progress',100]);//默认查询完成率低于100
        }
        if(!empty($this->partake)){
            $trainIds = TrainUser::find()->where(['user_id' => \Yii::$app->user->identity->user_id])->select('train_id')->column();

            $query->andWhere(['id' =>$trainIds]);
        }
        if(!empty($this->myCreate)){
            $query->andWhere(['create_user' => \Yii::$app->user->identity->user_id]);
        }

        $query->andFilterWhere(['courses' => $this->courses]);

        !empty($this->start_time) &&  $this->start_time = strtotime($this->start_time);
        !empty($this->end_time)   &&  $this->end_time = strtotime($this->end_time);
        // grids filtering conditions
        $query->andFilterWhere([
            'courses' => $this->courses,
        ]);
        if($this->shift){
            $query->andWhere(new Expression('(select count(*) from '.TrainUser::tableName().' as tu inner join '.UserInfo::tableName().' u on tu.user_id = u.id where tu.train_id = t.id and u.shift = '.$this->shift.') > 0'));
        }
        $query->andFilterWhere(['>','start_time',$this->start_time]);
        $query->andFilterWhere(['<','start_time',$this->end_time]);
        $query->andFilterWhere(['>=','progress',$this->start_progress]);
        $query->andFilterWhere(['<=','progress',$this->end_progress]);
        $query->orderBy('end_date,progress');
        return $dataProvider;
    }


    /**
     * 查询导出的内容
     * @param $type
     * @param $ids
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getExportData($type,$ids){
        if(in_array($type,[self::EXPORT_TYPE_YES,self::EXPORT_TYPE_NO])){
            $data = $this->getTrainList($type,$ids);
        }elseif(in_array($type,[self::EXPORT_TYPE_USER_YES,self::EXPORT_TYPE_USER_NO])){
            $data = $this->getTrainUserList($type,$ids);
        }else{
            $data = $this->getTrainNotUserList($ids);
        }
        return $data;
    }

    /**
     * 导出培训
     * @param $type
     * @param $ids
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getTrainList($type,$ids){

        $query = self::find()->alias('t')->select('t.*,u.chinese_name,c.name');
        $query->innerJoin(['c' => TrainCourses::tableName()],'c.id = t.courses');
        $query->innerJoin(['u' => UserInfo::tableName()],'t.create_user = u.id');
        if($type == self::EXPORT_TYPE_YES){
            $query->where(['t.progress' => 100]);
        }else{
            $query->where(['<','t.progress' , 100]);
        }
        $query->andFilterWhere(['t.id' => $ids]);
        return $query->asArray()->all();

    }

    /**
     * 查询导出的培训学员
     * @param $type
     * @param $ids
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getTrainUserList($type,$ids){
        $query = self::find()->alias('t')->select('t.id as tid,t.lecturer,us.chinese_name as create_user_name,c.name,tu.*,u.chinese_name,u.shift');
        $query->innerJoin(['c' => TrainCourses::tableName()],'c.id = t.courses');
        $query->innerJoin(['tu' => TrainUser::tableName()],'tu.train_id = t.id');
        $query->innerJoin(['u' => UserInfo::tableName()],'u.id = tu.user_id');
        $query->innerJoin(['us' => UserInfo::tableName()],'us.id = t.create_user');
        if($type == self::EXPORT_TYPE_USER_YES){
            $query->where(['t.progress' => 100]);
        }else{
            $query->where(['<','t.progress' , 100]);
        }
        $query->andFilterWhere(['t.id' => $ids]);
        return $query->asArray()->all();
    }
    /**
     * 查询导出的未签到学员
     * @param $type
     * @param $ids
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getTrainNotUserList($ids){
        $query = self::find()->alias('t')->select('t.id as tid,t.lecturer,us.chinese_name as create_user_name,c.name,tu.*,u.chinese_name,u.shift');
        $query->innerJoin(['c' => TrainCourses::tableName()],'c.id = t.courses');
        $query->innerJoin(['tu' => TrainUser::tableName()],'tu.train_id = t.id');
        $query->innerJoin(['u' => UserInfo::tableName()],'u.id = tu.user_id');
        $query->innerJoin(['us' => UserInfo::tableName()],'us.id = t.create_user');
        $query->where(['tu.sign_in' => TrainUser::SIGN_IN_NO]);
        $query->andFilterWhere(['t.id' => $ids]);
        return $query->asArray()->all();
    }

}
