<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\ExternalSystemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">外部系统 </a>
        <a href="javascript:;">
          <cite>系统列表</cite>
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
        'pager' => ['options' => ['class' => 'pagination']],
        'columns' => [
            [
                'label' => 'ID',
                'value' => function($row){
                    return $row->id;
                }
            ],
            [
                'attribute' => 'name',

            ],
            [
                'attribute' => 'status',
                'label' => '状态',
                'value' => function($row){
                    return $row::$statusArr[$row->status];
                },

            ],
            [
                'attribute' => 'create_time',
                'value' => function($row){
                    return date('Y-m-d H:i:s',$row->create_time);
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{authority}{update} ',
                'buttons' => [
                     'authority' => function($url, $model, $key){
                         return  Html::a('权限列表' , \yii\helpers\Url::to(['external-authority/index','pid' => $model->id]), ['class' => 'layui-btn layui-btn-normal layui-btn-xs' ]) ;
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
<?php $this->beginBlock('footer')?>
<script>
    layui.use('layer', function(){
        var layer = layui.layer;

        $('.delete_system').click(function () {
            var url = $(this).data('url');
            layer.confirm('是否确认删除?', function(index){
                $.post(url,function (res) {
                    if(res.status == 200){
                        location.reload();
                    }else{
                        layer.msg(res.msg);
                    }
                },'json')
            });
        });
    });



</script>
<?php $this->endBlock()?>
