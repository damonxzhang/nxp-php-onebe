<?php

use yii\helpers\Html;
use backend\models\search\IrSearch;
use backend\widgets\LayuiGridView;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\TrainSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


?>
    <div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">IR</a>
        <a href="javascript:;">
          <cite>IR列表</cite>
        </a>
      </span>
    </div>

    <div class="x-body">
        <p>
            <?= Html::a('主管审批',  ['correlation',Html::getInputName($model,'relevant') => IrSearch::DIRECTOR], ['class' => 'layui-btn ']) ?>
            <?= Html::a('评估人审批',['correlation',Html::getInputName($model,'relevant') => IrSearch::ASSESS], ['class' => 'layui-btn ']) ?>
            <?= Html::a('负责人意见',['correlation',Html::getInputName($model,'relevant') => IrSearch::HEAD], ['class' => 'layui-btn ']) ?>
            <?= Html::a('实施人意见',['correlation',Html::getInputName($model,'relevant') => IrSearch::WORK], ['class' => 'layui-btn ']) ?>
            <?= Html::a('结果确认',['correlation',Html::getInputName($model,'relevant') => IrSearch::CINFIRM], ['class' => 'layui-btn ']) ?>
            <?= Html::a('奖励发放',['correlation',Html::getInputName($model,'relevant') => IrSearch::PRIZE], ['class' => 'layui-btn ']) ?>
            <?= Html::a('申诉审核',['correlation',Html::getInputName($model,'relevant') => IrSearch::APPEAL], ['class' => 'layui-btn ']) ?>
        </p>
        <?= LayuiGridView::widget([
            'dataProvider' => $data ,
            'columns' => [
                [ 'class' => 'backend\grids\LayuiCheckBoxColumn'],
                'id',
                'subject',
                [
                    'attribute' => 'create_user',
                    'value' =>function($row){
                        return (new \backend\models\UserInfo())->getUserName($row->create_user);
                    }
                ],
//            [
//                'attribute' => 'advice_type',
//                'value' => function($row){
//                    $types = explode(',',$row->advice_type);
//                    $type = '';
//                    foreach ($types as $v){
//                        $type .= ','.$row::$adviceTypes[$v];
//                    }
//                    return trim($type,',');
//                }
//            ],
                [
                    'attribute' => 'status',
                    'value' => function($row){

                        return  \backend\models\Ir::$statusArr[$row->status] ;
                    }
                ],
                [
                    'attribute' => 'steps',
                    'value' => function($row){
                        return \backend\models\Ir::$stepsArr[$row->steps] ;
                    }
                ],
                [
                    'label' => '实施人',
                    'value' =>function($row){
                        $user = \backend\models\IrUser::findMasterUserId($row->id,\backend\models\IrUser::WORK_TYPE);
                        if(!empty($user)){

                            return (new \backend\models\UserInfo())->getUserName($user->user_id);
                        }
                    }
                ],
                [
                    'attribute' => 'create_time',
                    'value' => function($row){
                        return date('Y-m-d',$row->create_time);
                    }
                ],
                [
                    'attribute' => 'end_time',
                    'value' => function($row){
                        if($row->end_time){

                            return date('Y-m-d',$row->end_time);
                        }
                    }
                ],
                [
                    'class' =>'backend\grids\LayuiActionColumn',
                    'template' => '{view}{update}',
                    'buttons' => [
                        'update' => function($url,$row){
                            return Html::a('编辑','javascript:;',[
                                'class' => 'layui-btn layui-btn-xs layui-btn-normal update-btn',
                                'data-id' => $row->id
                            ]);
                        }
                    ]
                ]

            ],
        ]); ?>

    </div>
<?php
$url = urlencode(Yii::$app->request->hostInfo.Yii::$app->request->url);
$js = <<<JS
    
    $('.update-btn').click(function() {
        var id = $(this).data('id');
        $.ajax({
            url:'befor-update',
            type:'post',
            dataType:'json',
            data:{id:id,redirect_url:"{$url}"},
            success:function(res) {
                if(res.status == 200){
                    window.location.href=res.data.url;
                }else{
                    layer.msg(res.msg);
                }
            }
        });
    });
    $('.export_excel').click(function(){ 
        var idlist   = [];
        $('.layui-form-checked').each(function () {
            if (typeof($(this).data('id')) !="undefined" && $(this).data('id')!=0){
                idlist.push($(this).data('id'));
            }
        }); 
        
        window.location.href='exprot-excel?idlist='+idlist;
      
    }); 
JS;
$this->registerJs($js);
?>