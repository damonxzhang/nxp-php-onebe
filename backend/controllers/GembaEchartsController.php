<?php
namespace backend\controllers;

use backend\models\GembaChart;
use backend\models\NewGemba;
use backend\models\search\NewGembaSearch;
use backend\models\UserInfo;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;

class GembaEchartsController extends BaseController{

    /**
     * 根据周别和类型展示柱形图
     * @return string
     */
    public function actionTypeChart(){

        $model = new NewGembaSearch();
        $data = $model->searchBraChartTypeByArea(\Yii::$app->request->queryParams,'id,type,create_time');
        $chartData = [];
        $category =[];
        if(isset($data)){
            $gembaChart = new GembaChart();
            $gembaChart->setChartParamsByW($data,$chartData,$category);
            $chartData = $gembaChart->setShowChartData($chartData,function($val,$key){
                return NewGemba::$typeList[$key];
            },[],'bar','20','周别');
        }

        return $this->render('type-chart', [
            'model' => $model,
            'category' => $category,
            'chartData' => $chartData
        ]);
    }

    /**
     * 根据周别和类型展示柱形图
     * @return string
     */
    public function actionAreaChart(){

        $model = new NewGembaSearch();
        $data = $model->searchBraChartTypeByArea(\Yii::$app->request->queryParams,'id,create_time,area,status');

        $chartData = [];
        $category =['finish' => 2,'perform' => 1];
        if(isset($data)){
            $gembaChart = new GembaChart();
            $gembaChart->setChartParamsByArea($data,$chartData,$category);

            $label = $gembaChart->setPercentLabel($chartData,$category);

            $chartData = $gembaChart->setShowChartData($chartData,function($val,$key) {
                return $key === 2 ? '执行中' : '已完成';
            } ,$label);
        }
        return $this->render('area-chart', [
            'model' => $model,
            'category' => $category,
            'chartData' => $chartData
        ]);
    }

    /**
     * 根据区域区分饼图
     * @return string
     */
    public function actionAreaPieChart(){
        $model = new NewGembaSearch();
        $data = $model->searchBraChartTypeByArea(\Yii::$app->request->queryParams,'id,create_time,area,status');
        $chartData = [];
        if(isset($data)){
            $GembaChart = new GembaChart();
            $chartData = $GembaChart->setPieChartParamsByArea($data, UserInfo::$areaArr);
        }
        return $this->render('area-pie-chart', [
            'model' => $model,
            'chartData' => $chartData
        ]);
    }
    /**
     * 根据区域区分饼图
     * @return string
     */
    public function actionAreaPercentPieChart(){
        $model = new NewGembaSearch();
        $data = $model->searchBraChartTypeByArea(\Yii::$app->request->queryParams,'id,create_time,area,status');
        $chartData = [];
        if(isset($data)){
            $GembaChart = new GembaChart();
            $category =['finish' => 2,'perform' => 1];
            $chartData = $GembaChart->setPieChartParamsByFinish($data,$category);
        }
        return $this->render('area-percent-pie-chart', [
            'model' => $model,
            'chartData' => $chartData
        ]);
    }

}