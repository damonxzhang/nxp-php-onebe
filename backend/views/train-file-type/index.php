<?php

use yii\helpers\Html;
use backend\widgets\LayuiGridView;
use backend\models\TrainFileType;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\TrainFileType */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">培训考核</a>
        <a href="javascript:;">
          <cite>培训文件分类</cite>
        </a>
      </span>
</div>
<div class="x-body">

    <p>
        <?= Html::a('新增', ['create'], ['class' => 'layui-btn']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= LayuiGridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'name',
            [
                'attribute' => 'type',
                'value' => function($row){
                    return TrainFileType::$types[$row->type];
                }
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function($row){
                    $text =  TrainFileType::$statusArr[$row->status];
                    $btn = '';
                    if($row->status == TrainFileType::STATUS_NO){
                        $btn = ' layui-btn-danger';
                    }
                    return Html::a($text,'javascript:;',['class' => 'layui-btn update-status '.$btn,'data-id' => $row->id]) ;
                }
            ],
            [
                'class' => 'backend\grids\LayuiActionColumn',
                'template' => '{update} ',
            ],
        ],
    ]); ?>


</div>
<?php
$js = <<<JS
    $('.update-status').click(function(){
        var id = $(this).data('id');
        console.log(id);
        $.get('update-status',{id:id},function(res) {
            if(res.status == 200){
                window.location.reload();
            }else{
                layer.msg(res.msg);
            }
        },'json');
    });

JS;

$this->registerJs($js);
?>