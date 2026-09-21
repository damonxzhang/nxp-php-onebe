<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\ExternalAuthoritySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">外部系统</a>
        <a href="<?= \yii\helpers\Url::to(['external-system/index'])?>">系统列表</a>
        <a href="">
          <cite>权限列表</cite>
        </a>
      </span>
</div>
<div class="external-system-index x-body">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('创建', ['create','pid' => $pid], ['class' => 'layui-btn']) ?>

    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= \backend\widgets\LayuiGridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'system_id',
            'name',
            [
                'attribute' => 'status',
                'label' => '状态',
                'value' => function($row){
                    return $row::$statusArr[$row->status];
                },

            ],
            'create_time:datetime',
            [
                'label' => '创建时间',
                'value' => function($row){
                    return date('Y-m-d H:i:s',$row->create_time);
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{authority}{update} ',
                'buttons' => [
                    'authority' => function($url, $model, $key){
                        return  Html::a('用户编辑' , \yii\helpers\Url::to(['external-relation/update','pid' => $model->id]), ['class' => 'layui-btn layui-btn-normal layui-btn-xs delete_system' ]) ;
                    },
                    'update' => function($url, $model, $key){
                        return  Html::a('编辑',  $url , ['class' => 'layui-btn layui-btn-xs']) ;
                    },
                    'delete' => function($url, $model, $key){
                        return  Html::button('删除' , ['class' => 'layui-btn layui-btn-danger layui-btn-xs delete_system','data-url' => $url]) ;
                    }
                ],
            ],
        ],
    ]); ?>


</div>
