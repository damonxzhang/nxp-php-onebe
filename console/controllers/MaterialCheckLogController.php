<?php
namespace console\controllers;

use backend\models\ShelFInventoryLog;
use backend\models\ShelfMaterials;
use yii\console\Controller;
header("content-type:text/html;charset=utf-8");

class MaterialCheckLogController extends Controller
{

    public function actionIndex()
    {
        $status = [ShelfMaterials::STATUS_INFO,ShelfMaterials::STATUS_PENDING,ShelfMaterials::STATUS_EXPIRED];
        $materials = ShelfMaterials::find()->where(['fettle' => ShelfMaterials::FETTLE_UP])->andWhere(['not', ['shelf_id' => null]])->andWhere(['in', 'status', $status])->all();
        $value = [];
        foreach ($materials as $material){
            if (empty($value[$material->shelf_id][$material->part_no][$material->status])){
                $value[$material->shelf_id][$material->part_no][$material->status] = 0;
            }
            $value[$material->shelf_id][$material->part_no][$material->status]  += 1;
        }

        foreach ($value as $shelf_id => $item){
            foreach ($item as $par_no => $v){
                $model = new ShelFInventoryLog();
                $model->shelf_id = $shelf_id;
                $model->part_no = $par_no;
                $model->value = json_encode($v);
                $model->created_at = date('Y-m-d H:i:s');
                $model->save();
            }
        }

    }
}