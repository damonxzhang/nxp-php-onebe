<?php
namespace  backend\models\search;

use backend\models\ShelfScattered;
use yii\data\ActiveDataProvider;
use backend\models\ShelfMaterials;


class ShelfScatteredSearch extends ShelfScattered {

    public $status;
    public $part_no;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['status', 'part_no'], 'safe'],
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
        $query = ShelfScattered::find();
        $query->andFilterWhere(['status' => $this->status]);
        $query->andFilterWhere(['like', 'part_no', trim($this->part_no)]);
        $query->orderBy(['id'=>SORT_ASC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }


	
    /**
     * 获取 part_no
     */
    public function getPartNo($shelf_id){
        $query_list = ShelfMaterials::find();
        $query_list->select(['part_no']);
        $query_list->andWhere(['shelf_id'=>$shelf_id]);
        $query_list->andWhere(['fettle'=>1]);
        $query_list->andWhere(['in','status',[1,2,3,4]]);
//        $query_list->groupBy(['shelf_id']);
        $list = $query_list->asArray()->one();

//        print_r($list);
        if(empty($list))
            return '';

        return $list['part_no'];
    }
	
    /**
     * 获取 part_no
     */
    public function getContNo($shelf_id){
        $query_list = ShelfMaterials::find();
        $query_list->select(['cont_no']);
        $query_list->andWhere(['shelf_id'=>$shelf_id]);
        $query_list->andWhere(['fettle'=>1]);
        $query_list->andWhere(['in','status',[1,2,3,4]]);
//        $query_list->groupBy(['shelf_id']);
        $list = $query_list->asArray()->all();

    //    print_r($list);die;
        if(empty($list))
            return '';


        $result = '';
        foreach ($list as $key => $value) {
            # code...
            $result = $result . $value['cont_no'] . '<br>';
        }


        return $result;
    }

}