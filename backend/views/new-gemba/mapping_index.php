<?php

use yii\helpers\Html;
use backend\widgets\LayuiGridView;
use yii\helpers\Url;
use backend\models\NewGemba;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\NewGembaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">NXP WAY</a>
        <a href="javascript:;">
          <cite>Gemba镜像记录</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <?= $this->render('_mapping_search', ['model' => $searchModel]); ?>
    <xblock>
        <button class="layui-btn layui-bg-black export_excel" data-query='<?=json_encode(Yii::$app->request->queryParams)?>'><i class="layui-icon">&#xe62f;</i>导出</button>
    </xblock>

    <?= LayuiGridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [ 'class' => 'backend\grids\LayuiCheckBoxColumn'],
            'number',
            'year',
            [
                'attribute' => 'create_user',
                'value' => function($row){
                    $names = (new \backend\models\UserInfo())->getUserName($row->create_user);
                    if(!empty($names)){
                        return $names;
                    }
                    return '';
                }
            ],
            [
                'attribute' => 'participant',
                'value' => function($row){
                    $names = (new \backend\models\UserInfo())->getUserNameByIdArray($row->participant);
                    if(!empty($names)){
                        return implode(',',$names);
                    }
                    return '';
                }
            ],
            [
                'attribute'=>'title',
                'value'=>function ($row) { return $row->title; },
                'format'=>'raw',
                'label'=>'Desc',
                'contentOptions' => ['style'=>'max-width:200px;'],
            ],
            [
                'attribute'=>'content',
                'value'=>function ($row) { return $row->content; },
                'format'=>'raw',
                'label'=>'Desc',
                'contentOptions' => ['style'=>'max-width:300px;'],
            ],
            [
                'attribute' => 'type',
                'value' => function($row){
                    return NewGemba::$typeList[$row->type];
                }
            ],
            [
                'attribute' => 'steps',
                'value' => function($row){
                    return NewGemba::$stepsList[$row->steps];
                }
            ],
            [
                'class' =>'backend\grids\LayuiActionColumn',
                'template' => '{mapping_view}',
                'buttons' => [
                    'mapping_view' => function($url,$row){
                        return Html::a('详情','javascript:;',[
                            'class' => 'layui-btn layui-btn-xs layui-btn-normal mapping-view-btn',
                            'data-id' => $row->id
                        ]);
                    },
                ]
            ]
        ],
    ]); ?>


</div>
<?php

$js =<<<JS

    $('.mapping-view-btn').click(function() {
        var id = $(this).data('id');
        window.location.href='mapping-view?id='+id;
      
    });
    $('.export_excel').click(function(){ 
        let idlist   = [];
        $('.layui-form-checked').each(function () {
            if (typeof($(this).data('id')) !="undefined" && $(this).data('id')!=0){
                idlist.push($(this).data('id'));
            }
        }); 
        let query = $(this).data('query');
        query = JSON.stringify(query);
        window.location.href='exprot-excel?idlist='+idlist+'&query='+query+'&type=2';
    }); 
    
JS;
$this->registerJs($js);

?>