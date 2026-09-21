<?php

use yii\helpers\Html;
use backend\widgets\LayuiGridView;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\TrainCoursesTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Reain Courses Types';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">培训课程</a>
        <a href="javascript:;">课程列表</a>
        <a href="javascript:;">
          <cite>课程分类列表</cite>
        </a>
      </span>
</div>
<div class="x-body">

    <div class="layui-row">
        <?= Html::a('创建', ['create'], ['class' => 'layui-btn']) ?>
    </div>


    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= LayuiGridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
//            'id',
            'name',
            [
                'label' => '状态',
                'value' => function($row){
                    return $row::$status[$row->status];
                }
            ],
            [
                'attribute' => 'create_time',
                'value' => function($row){
                    return date('Y-m-d H:i:s',$row->create_time);
                }
            ],
            [
                'class' => 'backend\grids\LayuiActionColumn',
                'template' => '{update}'
            ],
        ],
    ]); ?>


</div>
