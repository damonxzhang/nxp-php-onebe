<?php

use backend\grids\LayuiActionColumn;
use backend\widgets\LayuiGridView;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\MaintainFeedbackSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'MaintainApi Feedbacks';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">异常反馈</a>
        <a href="javascript:;">
          <cite>异常反馈列表</cite>
        </a>
      </span>
</div>

<div class="x-body">

    <?= LayuiGridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'backend\grids\LayuiCheckBoxColumn'],
            'id',
            [
                'label' => '异常编号',
                'value' => function($row){
                    return $row->maintain->number;
                }
            ],
            [
                'attribute'=>  'feedback_type',
                'value' => function($row){
                    if ($row->feedback_type == \backend\models\Maintain::OTHER_PROBLEM_TYPE){

                        return $row->other_feedback;
                    }else{

                        return \backend\models\Maintain::$problem[$row->feedback_type];
                    }
                }
            ],
            [
                'attribute'=>  'feedback_user',
                'value' => function($row){
                    return (new \backend\models\UserInfo())->getUserName($row->feedback_user);
                }
            ],
            [
                'attribute'=>  'solve_status',
                'value' => function($row){
                    return  \backend\models\MaintainFeedback::$solveStatusList[$row->solve_status];
                }
            ],
            'supplement',
            [
                'attribute'=>  'predict_date',
                'value' => function($row){
                    return date('Y-m-d',$row->predict_date);
                }
            ],
            [
                'attribute'=>  'complete_date',
                'value' => function($row){
                    return !empty($row->complete_date)? date('Y-m-d',$row->predict_date) :'';
                }
            ],
            [
                'class' => 'backend\grids\LayuiActionColumn',
                'template' => '{view}'
            ],
        ],
    ]); ?>


</div>
