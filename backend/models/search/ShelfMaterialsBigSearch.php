<?php

namespace backend\models\search;

use backend\models\Shelf;
use backend\models\ShelfMaterialsBig;
use yii\data\ActiveDataProvider;

class ShelfMaterialsBigSearch extends ShelfMaterialsBig
{

    public $status;
    public $fettle;
    public $shelf_number;
    public $expire_time;
    public $cont_no;
    public $scrapped;
    public $user_id;
    public $obtained;
    public $entry_id;
    public $part_no;
    public $lot_no;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [[
                'status',
                'shelf_number',
                'fettle',
                'cont_no',
                'part_no',
                'lot_no',				
                'expire_time',
                'scrapped',
                'user_id',
                'entry_id',
                'obtained',
            ], 'safe'],
        ];
    }
    
    /**
     * ????
     * @param $param
     * @return ActiveDataProvider
     */
    public function search($param)
    {
        $this->setAttributes($param);
        $shelfId = '';
        if ($this->shelf_number) {
            $shelfModel = Shelf::findOne(['shelf_number' => trim($this->shelf_number)]);
            if ($shelfModel) {
                $shelfId = $shelfModel->id;
            }
        }
        $query = ShelfMaterialsBig::find();
        // $query->andFilterWhere([
        //     'status'   => $this->status,
        //     'user_id'  => $this->user_id,
        //     'entry_id' => $this->entry_id,
        //     'cont_no'  => $this->cont_no,
        //     'part_no'  => $this->part_no,
        //     'lot_no'   => $this->lot_no,
        //     'shelf_id' => $shelfId,
        // ]);
    
        if ($this->scrapped) {
            $query->where(['status' => ShelfMaterialsBig::STATUS_SCRAPPED]);
        }
        $query->andFilterWhere(['<=', 'expire_time', $this->expire_time]);
        $query->orderBy(['update_time' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }

}