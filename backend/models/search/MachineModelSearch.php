<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\MachineModel;

use backend\models\MachineModelVendor;
use backend\models\MachineVendor;
use yii\db\Query;


class MachineModelSearch extends MachineModel
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['name', 'createdt', 'updatedt'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
  //      $this->setAttributes($params);
  //      $query = MachineModel::find();
  //      $query->andFilterWhere(['like', 'name', trim($this->name)]);
  //      $query->orderBy(['id'=>SORT_DESC]);
  //      $dataProvider = new ActiveDataProvider(['query' => $query]);
  //      return $dataProvider;
  
	    $this->setAttributes($params);
        $query = new Query();
        $query->from(MachineModel::tableName() . ' mm');
        $query->select('mm.id,mm.name,mm.status,mm.createdt,mv.name as vendor_name');
        $query->leftJoin(MachineModelVendor::tableName() . ' mmv', 'mmv.model_id = mm.id');
        $query->leftJoin(MachineVendor::tableName() . ' mv', 'mmv.vendor_id = mv.id');
        $query->andFilterWhere(['like', 'mm.name', trim($this->name)]);
        $query->orderBy(['mm.id'=>SORT_DESC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
  
    }
}
