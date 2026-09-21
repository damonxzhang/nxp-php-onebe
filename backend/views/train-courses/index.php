<?php

use yii\helpers\Html;
use backend\widgets\LayuiGridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\TrainCoursesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Reain Courses';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">培训课程</a>
        <a href="javascript:;">
          <cite>课程列表</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <div class="layui-row">
            <?= Html::a('创建', ['create'], ['class' => 'layui-btn']) ?>
            <?= Html::a('课程类型', ['train-courses-type/index'], ['class' => 'layui-btn']) ?>
    </div>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= LayuiGridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
//            'id',
            [
                'attribute' => 'coach',
                'value' => function($row){
                    return (new \backend\models\UserInfo())->getUserName($row->coach);
                }
            ],
            'name',
            [
                'attribute' => 'type',
                'value' => function($row) use ($coursesType){
                    return  $coursesType[$row->type];
                }
            ], 
            [
                'attribute' => 'create_time',
                'value' => function($row){
                    return date('Y-m-d H:i:s',$row->create_time);
                }
            ],
            ['class' => 'backend\grids\LayuiActionColumn'],
        ],
    ]); ?>


</div>
