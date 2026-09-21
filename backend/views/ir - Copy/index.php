<?php

use yii\helpers\Html;
use backend\widgets\LayuiGridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\TrainSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


?>
<style>
    .red-class {
        color:red;
    }
</style>
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
    <?php  echo $this->render('_search', ['model' => $model]); ?>

    <p>
        <?= Html::a('新增IR', ['create'], ['class' => 'layui-btn ']) ?>
        <?= Html::a('我发起的',\yii\helpers\Url::to(['index', Html::getInputName($model,'this_ir') => 1 ]) , ['class' => 'layui-btn layui-btn-normal']) ?>
        <button class="layui-btn layui-bg-black export_excel" ><i class="layui-icon">&#xe62f;</i>导出</button>

    </p>
    <?= LayuiGridView::widget([
        'dataProvider' => $data ,
        'rowOptions' => function($row){
            if($row->steps == \backend\models\Ir::HEAD_STEPS && !empty($row->end_time) && $row->end_time  < strtotime(date('Y-m-d',time()))){
                return ['class' => 'red-class'];
            }else{
                return [];
            }
        },
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
                'label' => '是否有图',
                'value' => function($row){
                    if(!empty($row->old_images)){
                        return '是';
                    }else{
                        return '否';
                    }
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

                        return date('Y-m-d',$row->end_time) ;
                    }
                },
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

$js = <<<JS
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