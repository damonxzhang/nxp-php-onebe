<?php
namespace  backend\models\search;

use backend\models\BillMaterials;
use yii\data\ActiveDataProvider;
use backend\models\BillShelf;
use yii\db\Query;

class BillMaterialsSearch extends BillMaterials {

    public $status;
    public $materials_number;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['status', 'materials_number'], 'safe'],
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
        // $query = BillMaterials::find();
        // $query->andFilterWhere(['status' => $this->status]);
        // $query->andFilterWhere(['like', 'materials_number', trim($this->materials_number)]);
        // $query->orderBy(['id'=>SORT_ASC]);
        // $dataProvider = new ActiveDataProvider(['query' => $query]);

        $query = new Query();
        $query->select([
            'bm.id',
            'bs.mark',
            'bs.shelf_number',
            'bm.materials_number',
            'bm.create_time',
            'bm.down_time'
        ]);
        $query->from(BillMaterials::tableName() . ' bm');
        $query->leftJoin(BillShelf::tableName() . ' bs', 'bm.shelf_id = bs.id');
        $query->andFilterWhere([
            'bm.status'  => $this->status
        ]);
        $query->andFilterWhere(['like', 'bm.materials_number', trim($this->materials_number)]);
        $query->orderBy(['bm.id' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }

}