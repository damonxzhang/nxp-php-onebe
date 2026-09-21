<?php
namespace  backend\models\search;

use backend\models\BillShelf;
use yii\data\ActiveDataProvider;
use backend\models\ShelfMaterials;


class BillShelfSearch extends BillShelf {

    public $status;
    public $shelf_number;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['status', 'shelf_number'], 'safe'],
        ];
    }

    /**
     * 数据查询
     * @param $param
     * @return ActiveDataProvider
     */
    public function search($param)
    {
        $this->setAttributes($param);
        $query = BillShelf::find();
        $query->andFilterWhere(['status' => $this->status]);
        $query->andFilterWhere(['like', 'shelf_number', trim($this->shelf_number)]);
        $query->orderBy(['id'=>SORT_ASC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }

}