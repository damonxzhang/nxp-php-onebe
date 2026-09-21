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
        <?= Html::a('新增', ['defect-create'], ['class' => 'layui-btn']) ?>
    </p>
    <?= LayuiGridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'defect',
            'weight',
            [
                'attribute' => 'status',
                'value' => function($row){
                    return \backend\models\CarDefect::$statusList[$row->status];
                }
            ],
            [
                'attribute' => 'type',
                'value' => function($row){
                    return \backend\models\CarDefect::$types[$row->type];
                }
            ],
            [
                'class' => '\backend\grids\LayuiActionColumn',
                'template' => '{defect-update}',
                'buttons' => [
                    'defect-update' => function($url, $model, $key){
                        return  Html::a('编辑' , \yii\helpers\Url::to(['car/defect-update','id' => $model->id]), ['class' => 'layui-btn layui-btn-normal layui-btn-xs' ]) ;
                    },
                ]
            ],
        ],
    ]); ?>


</div>
<?php
$js = <<<JS
    $('.alter').click(function(){
        var id = $(this).parents('tr').data('key'); 
        $.ajax({    
            url:'defect-update',
            data:{id:id},
            dataType:'json',
            type:'post',
            success:function(res) {
                if(res.status == 200){
                    window.location.reload();
                }else{
                    layer.msg(res.msg);
                }
            }
        });  
    });
JS;
$this->registerJs($js);

?>