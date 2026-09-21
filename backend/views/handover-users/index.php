<?php

use yii\helpers\Html;
use backend\widgets\LayuiGridView as GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\HandoverUsersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="x-nav">
   <span class="layui-breadcrumb">
       <a href="javascript:;">首页</a>
       <a href="javascript:;">充电仓交班人员管理</a>
       <a href="javascript:;"><cite>人员列表</cite></a>
   </span>
</div>
<div class=" x-body handover-users-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <button class="layui-btn "  onclick="x_admin_show('新增交接人员','<?= \yii\helpers\Url::to(['create' ]) ?>',700,500)"><i class="layui-icon">&#xe642;</i>新增交接人员</button>

    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'user_id',
                'options' => ['style' => 'width:90%'],
                'value' => function($row){
                    return (new \backend\models\UserInfo())->getUserName($row->user_id);
                }
            ],
            [
                'class' => 'backend\grids\LayuiActionColumn',
                'template' => '{delete}',
                'buttons' => [
                    'delete' => function(){
                        return Html::button('删除',['class' => 'layui-btn layui-btn-danger delete']);
                    }
                ],

            ],
        ],
    ]); ?>

</div>
<?php
$js = <<<JS
    $('.delete').click(function(){
        var id = $(this).parents('tr').data('key');
        layer.confirm('是否确认删除？',function(){
            
            $.post('delete',{id:id},function(res) {
              if(res.status == 200){
                  layer.msg(res.msg, {icon: 1,time: 1500},function(){
                      window.location.reload();
                  });
              }else{
                   layer.msg(res.msg, {icon: 1,time: 2000})
              }
            },'json');
        }); 
       
    });
JS;
$this->registerJs($js);
?>