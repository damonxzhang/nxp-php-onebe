<?php

use yii\helpers\Html;
use backend\models\UserInfo;
use backend\widgets\LayuiGridView;
use yii\helpers\ArrayHelper;
use backend\models\MaintainType;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\MaintainTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'MaintainApi Types';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="x-nav">
  <span class="layui-breadcrumb">
    <a href="javascript:;">首页</a>
    <a href="javascript:;">异常类型</a>
    <a href="javascript:;">
      <cite>异常类型列表</cite>
    </a>
  </span>
</div>
<div class="x-body">


    <p>
        <?= Html::a('新增', ['create'], ['class' => 'layui-btn ']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= LayuiGridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'name',
            [
                'attribute' => 'process',
                'value' =>function($row){
                    return ArrayHelper::getValue(MaintainType::$processList,$row->process,'');
                }
            ],
            [
                'attribute' => 'area',
                'value' =>function($row){
                    return ArrayHelper::getValue(UserInfo::$areaArr,$row->area,'');
                }
            ],
            [
                'attribute' => 'create_user',
                'value' =>function($row){
                    return (new UserInfo())->getUserName($row->create_user);
                }
            ],
            [
                'attribute' => 'create_time',
                'value' =>function($row){
                    return date('Y-m-d',$row->create_time);
                }
            ],

            [
                'class' => 'backend\grids\LayuiActionColumn',
                'template' => ' {update} {delete}',
                'buttons' =>[
                    'delete' => function($url,$row,$params){
                        return Html::a('删除','javascript:;',['class' => 'layui-btn layui-btn-xs layui-btn-danger delete','data-url' => $url ]);
                    },
                ]
            ],
        ],
    ]); ?>

</div>
<?php
$js = <<<JS
    $('.delete').click(function(){
        let url = $(this).data('url');
        layer.confirm('是否确认删除？',function() {
            $.ajax({
                url:url, 
                type:'post',
                dataType:'json',
                success:function(res) {
                    if(res.status == 200){
                        window.location.reload();
                    }else{
                        layer.msg(res.msg);
                    }
                }
            })
        });
       
        
    });
     
JS;

$this->registerJs($js);
?>