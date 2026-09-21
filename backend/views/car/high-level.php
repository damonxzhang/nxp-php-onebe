<?php

use yii\helpers\Html;
use backend\widgets\LayuiGridView;
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">DPR</a>
        <a href="javascript:;">
          <cite>HIGH LEVEL</cite>
        </a>
      </span>
</div>

<div class="x-body">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('新增', ['high-level-create'], ['class' => 'layui-btn']) ?>
    </p>
    <?= LayuiGridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            [
                'attribute' =>  'user_id',
                'value' => function($row){
                    return  \backend\models\UserInfo::$areaArr[$row->userInfo->area].'--'. $row->userInfo->chinese_name;
                }
            ],
            [
                'attribute' => 'status',
                'value' => function($row){
                    return \backend\models\CarHighLevel::$statusList[$row->status];
                }
            ],
            [
                'attribute' => 'type',
                'value' => function($row){
                    return \backend\models\CarHighLevel::$types[$row->type];
                }
            ],
            [
                'class' =>'backend\grids\LayuiActionColumn',
                'template' => '{high-level-update}',
                'buttons' => [
                    'high-level-update' => function($url, $model, $key){
                        return  Html::a('编辑' , \yii\helpers\Url::to(['car/high-level-update','id' => $model->id]), ['class' => 'layui-btn layui-btn-normal layui-btn-xs' ]) ;
                    },
                ]
            ]
        ],
    ]); ?>


</div>
