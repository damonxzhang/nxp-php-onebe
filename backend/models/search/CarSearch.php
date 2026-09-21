<?php

namespace backend\models\search;

use backend\models\Base;
use backend\models\Car;
use backend\models\UserInfo;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class CarSearch extends Car
{
    public $title;
    public $status;
    public $start_time;
    public $end_time;
  
    public $associate;
    public $area;
    public $matter_number;
    public $device_number;
    /**
     * @return array
     */
    public function rules()
    {
        return [
            [
                [
                    'id',
                    'station',
                    'date',
                    'shift',
                    'device_model',
                    'batch_number',
                    'package',
                    'machine_number',
                    'create_id',
                    'defective_items',
                    'inspector',
                    'storage_location',
                    'description',
                    'create_time',
                    'status',
                    'operator',
                    'number',
                    'second_status',
                    'result',
                    'responsibility',
                    'dpr',
                    'type',
                    'trace_no',
                    'files_mapping'
                ], 'safe'
            ]
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
     * 查询日志
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $this->setAttributes($params);
        $query = Car::find();
        $query->andWhere([ 'type' => $params['type']]);
        $query->andFilterWhere(['like', 'trace_no', trim($this->trace_no)]);
        $query->andFilterWhere(['shift' => $this->shift]);
        $query->andFilterWhere(['like', 'machine_number', trim($this->machine_number)]);
        if($this->status){
            $query->andFilterWhere(['status' => $this->status]); 
        }
        $query->andWhere(['approval_status' => Car::APPROVAL_STATUS_GOING]);
        $query->orderBy(['id' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query'     => $query
        ]);
        return $dataProvider;
    }

     /**
     * 查询日志
     * @param $params
     * @return ActiveDataProvider
     */
    public function searchHistory($params)
    {
        $this->setAttributes($params);
        $query = Car::find();
        $query->andWhere([ 'type' => $params['type']]);
        $query->andFilterWhere(['like', 'trace_no', trim($this->trace_no)]);
        $query->andFilterWhere(['shift' => $this->shift]);
        $query->andFilterWhere(['like', 'machine_number', trim($this->machine_number)]);
        $startTime = ArrayHelper::getValue($params, 'start_time', '');
        $endTime   = ArrayHelper::getValue($params, 'end_time', '');
       
        $query->andFilterWhere(['>=', 'create_time', $startTime]);
        $query->andFilterWhere(['<=', 'create_time', $endTime]);
        $query->andWhere(['approval_status' => Car::APPROVAL_STATUS_PASS]);
        $query->orderBy(['id' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query'     => $query
        ]);
        return $dataProvider;
    }

}