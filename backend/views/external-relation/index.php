<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\ExternalRelationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">日志管理</a>
        <a href="javascript:;">系统列表</a>
        <a href="javascript:;">
          <cite>权限列表</cite>
        </a>
      </span>
</div>
<div class="external-system-index x-body">


    <p>
        <?= Html::a('创建', ['create'], ['class' => 'layui-btn']) ?>

    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= \backend\widgets\LayuiGridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'system_id',
            'authority_id',
            'user_id',
            'create_time:datetime',
            //'update_time:datetime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
