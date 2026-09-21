<?php

namespace backend\models\search;

use backend\models\MachineModel;
use backend\models\UserInfo;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Facility;
use yii\db\Query;

/**
 * FacilitySearch represents the model behind the search form of `backend\models\Facility`.
 */
class FacilitySearch extends Facility
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'create_user', 'area', 'process','shift', 'head_user', 'status', 'vendor_id', 'is_ok', 'create_time', 'update_time', 'delete_time'], 'integer'],
            [['name', 'number', 'model', 'serial_no', 'ASSET_NO', 'BUY_OFF_DATE', 'CBA_pass_date', 'file'], 'safe'],
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
        $this->setAttributes($params);
        $query = new Query();
        $query->select(['f.*','u.chinese_name']);
        $query->from(Facility::tableName() . ' f');
        $query->leftJoin(UserInfo::tableName() . ' u', 'f.create_user = u.id');
        $query->andFilterWhere(['like', 'f.name', trim($this->name)]);
        $query->andFilterWhere(['like', 'f.number', trim($this->number)]);
        $query->andFilterWhere(['=', 'f.status', trim($this->status)]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }
    
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchnew($params)
    {
        // print_r(empty($params));die;
        // $modelList = MachineModel::find()->select('id')
        //     ->where(['like','id' , $params['model']])
        //     ->asArray()
        //     ->all();


        $this->setAttributes($params);
        $query = new Query();
        $query->select(['f.*','u.chinese_name']);
        $query->from(Facility::tableName() . ' f');
        $query->leftJoin(UserInfo::tableName() . ' u', 'f.head_user = u.id');
        $query->andFilterWhere(['like', 'f.name', trim($this->name)]);
        $query->andFilterWhere(['like', 'f.number', trim($this->number)]);
        $query->andFilterWhere(['=', 'f.status', trim($this->status)]);
        $query->andFilterWhere(['=', 'f.area', trim($this->area)]);
        $query->andFilterWhere(['=', 'f.process', trim($this->process)]);
        if(!empty($params)){
            
            $modelList = MachineModel::find()->select('id')
            ->where(['like','name' , $params['model']])
            ->asArray()
            ->all();
            $data = [];
            foreach($modelList as $value){
                $data[] = $value['id'];
            }
            // print_r($modelList);die;
            $query->andFilterWhere(['in', 'f.model', $data]);
        }
        
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }

    /**
     * 查询设备级管理员信息
     * @param $number
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findFacilityUserInfo($number){

        return self::find()->alias('f')
                ->select('f.id,u.chinese_name,f.area,u.leader_uid')
                ->innerJoin(['u' => UserInfo::tableName()],'f.head_user = u.id')
                ->where(['f.number' => $number])
                ->asArray()
                ->one();
    }

}
