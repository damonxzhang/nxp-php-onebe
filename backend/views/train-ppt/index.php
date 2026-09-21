<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\TrainPptSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">课程课件</a>
        <a href="javascript:;">
          <cite>课件列表</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <?= $this->render('_search',['model' => $searchModel])?>

    <div class="layui-row">
        <p>
            <?= Html::a('创建', ['create'], ['class' => 'layui-btn']) ?>
        </p>
    </div>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= \backend\widgets\LayuiGridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
//            'id',
            'name',
            [
                'attribute' => 'type',
                'value' => function($row){
                    if(empty($row->type)){
                        return null;
                    }
                    return \backend\models\TrainFileType::getTypeName($row->type);
                }
            ],
            [
                'attribute' => 'create_user',
                'value' => function($row){
                    if(empty($row->create_user)){
                        return null;
                    }
                    return (new \backend\models\UserInfo())->getUserName($row->create_user);
                }
            ],
            [
                'attribute' => 'status',
                'value' => function($row){
                    if(empty($row->status)){
                        return null;
                    }
                    return \backend\models\TrainPpt::$statusArr[$row->status];

                }
            ],
            'comment:ntext',
            [
                'attribute' => 'create_time',
                'value' => function($row){
                    return date('Y-m-d H:i:s',$row->create_time);
                }
            ],
            //'create_time:datetime',
            //'update_time:datetime',

            [
                'class' => 'backend\grids\LayuiActionColumn',
                'template' => '{view} {update} {delete}',
                'buttons'=> [
                    'update' => function($url,$model){
                        if($model->create_user == Yii::$app->user->identity->user_id){
                            return Html::a('<span class="glyphicon glyphicon-trash">更新</span>',$url,['class' => 'layui-btn layui-btn-xs layui-btn-normal' ]);
                        }
                    },
                    'delete' => function($url,$model){
                        if($model->create_user == Yii::$app->user->identity->user_id){
                            return Html::a('<span class="glyphicon glyphicon-trash">删除</span>',$url,['class' => 'layui-btn layui-btn-xs layui-btn-danger','data-pjax'=>0,'data-confirm'=>'您确定要删除此项吗？']);
                        }
                    }
                ]
            ],
        ],
    ]); ?>


</div>
