<?php
namespace backend\models\search;

use backend\models\GridApply;
use backend\models\GridMaterials;
use yii\data\ActiveDataProvider;

class MaterialsApplySearch extends GridApply {


    public function search($request){

        $query = self::find()->where([
            'user_id' => \Yii::$app->user->identity->user_id,
            'status' => self::STATUS_UNRECEIVE
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        $this->load($request);
        if(!$this->validate()){
            return $dataProvider;
        }
        return $dataProvider;
    }

    /**
     * 未二次确认
     * @param $request
     */
    public function searchTwoConfirm(){
        $query = self::find()->alias('ma');

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        $query->innerJoin(['m' => GridMaterials::tableName()],'ma.id = m.apply_id');
        $query->andWhere(['m.status' => GridMaterials::STATUS_USING,'m.fettle' => GridMaterials::FETTLE_UP]);
        return $dataProvider;
    }

}