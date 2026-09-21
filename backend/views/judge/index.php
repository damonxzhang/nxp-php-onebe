<?php

use yii\helpers\Html;
use backend\widgets\LayuiGridView;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\IrJudge */
/* @var $dataProvider yii\data\ActiveDataProvider */


?>
    <div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">IR评审员</a>
        <a href="javascript:;">
          <cite>培训列表</cite>
        </a>
      </span>
    </div>

    <div class="x-body">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a('新增评审员', ['create'], ['class' => 'layui-btn ']) ?>
        </p>

        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <?= LayuiGridView::widget([
            'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
            'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

                'id',
                [
                    'attribute' => 'status',
                    'value' => function($row){
                        return \backend\models\IrJudge::$statusList[$row->status];
                    }
                ],
                [
                    'attribute' =>  'user_id',
                    'value' => function($row){
                        return  \backend\models\UserInfo::$areaArr[$row->userInfo->area].'--'. $row->userInfo->chinese_name;
                    }
                ],


                [
                    'class' => '\backend\grids\LayuiActionColumn',
                    'template' => '{update}',
                    'buttons' => [
                        'update' => function($url,$row){
                            if($row->status == \backend\models\IrJudge::STATUS_OFF){

                                return Html::button('开启', ['class' => 'layui-btn  alter']);

                            }else{

                                return Html::button('关闭', ['class' => 'layui-btn layui-btn-warm alter']);
                            }

                        }
                    ]
                ],
            ],
        ]); ?>


    </div>
<?php
$js = <<<JS
    $('.alter').click(function(){
        var id = $(this).parents('tr').data('key'); 
        layer.confirm('是否确认修改？',function() {
           $.ajax({    
            url:'update-status',
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
       
    });
JS;
$this->registerJs($js);

?>