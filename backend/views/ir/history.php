<?php

use backend\models\Ir;
use yii\helpers\Html;
use backend\widgets\LayuiGridView;
use backend\models\search\IrSearch;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\TrainSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">历史IR</a>
        <a href="javascript:;">
          <cite>历史IR</cite>
        </a>
      </span>
</div>

<div class="x-body">

    <?php  echo $this->render('_history_search', ['model' => $model]); ?>

    <p>
        <button class="layui-btn layui-bg-black " onclick="export_excel()"><i class="layui-icon">&#xe62f;</i>导出</button>
        <?= Html::a('我发起的',['history',Html::getInputName($model,'this_ir') => 1], ['class' => 'layui-btn ']) ?>
        <?= Html::a('与我有关的',['history',Html::getInputName($model,'relevant') => 1], ['class' => 'layui-btn ']) ?>
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
                'attribute' => 'prize',
                'value' =>function($row){
                    if($row->prize){
                        return \backend\models\Ir::$prizes[$row->prize];
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
                'template' => '{view} {update}',
                'buttons' => [
                    'update' => function($url,$row){
                        if($row->status == Ir::STATUS_DIE && $row->steps <= Ir::CONFIRM_STEPS){

                            return Html::a('编辑','javascript:;',[
                                'class' => 'layui-btn layui-btn-xs layui-btn-normal update-btn',
                                'data-id' => $row->id
                            ]);
                        }

                    }
                ]
            ]

        ],
    ]); ?>

</div>
<?php $this->beginBlock('footer') ?>
<script>
    $('.update-btn').click(function() {
        var id = $(this).data('id');
        $.ajax({
            url:'befor-update',
            type:'post',
            dataType:'json',
            data:{id:id},
            success:function(res) {
                if(res.status == 200){
                    window.location.href=res.data.url;
                }else{
                    layer.msg(res.msg);
                }
            }
        });
    });
    function export_excel(){
        var idlist   = [];
        var fromData  = $('#search_form').serialize();
        $('.layui-form-checked').each(function () {
            if (typeof($(this).data('id')) !="undefined" && $(this).data('id')!=0){
                idlist.push($(this).data('id'));
            }
        });
        window.location.href='exprot-excel?type=history&idlist='+idlist+'&'+fromData;
    }
</script>
<?php $this->endBlock() ?>