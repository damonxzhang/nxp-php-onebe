<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use backend\models\Train;
use backend\widgets\LayuiGridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\TrainSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">课程培训</a>
        <a href="javascript:;">
          <cite>培训列表</cite>
        </a>
      </span>
</div>

<div class="x-body">
    <?= $this->render('_search',['model' => $searchModel])?>

    <p>
    <div class="layui-input-inline">
        <?= Html::a('创建培训', ['create'], ['class' => 'layui-btn']) ?>

        <?= Html::a('我创建的', ['index?TrainSearch[myCreate]=1' ], ['class' => 'layui-btn layui-btn-normal']) ?>

        <?= Html::a('我参与的', ['index?TrainSearch[partake]=1' ], ['class' => 'layui-btn layui-btn-warm']) ?>

        <?= Html::a('已完成', ['index?TrainSearch[complete]=1'], ['class' => 'layui-btn']) ?>

    </div>
    <div class="layui-input-inline layui-select-title">

        <?=Html::dropDownList('export_type','',ArrayHelper::merge([''=> '请选择导出类型'],Train::$exportTypes),['class' => 'layui-input export_type '])?>
    </div>
    <?= Html::a('<i class="layui-icon"></i>导出', 'javascript:;', ['class' => 'layui-btn layui-bg-black export']) ?>

    </p>

    <?= LayuiGridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [ 'class' => 'backend\grids\LayuiCheckBoxColumn'],
            'id',
            [
                'attribute' => 'courses',
                'value' => 'coursesInfo.name'
            ],
            [
                'label' => '班别',
                'value' => function($row){
                    return $row->getShifts();
                }
            ],
            [
                'attribute' => 'lecturer',
                'value' => function($row){

                    return (new  \backend\models\UserInfo)->getUserName($row->lecturer);
                }
            ],
            [
                'attribute' => 'progress',
                'value' => function($row){
                    $progress = !empty($row->progress)?$row->progress :0;
                    return $progress.'%';
                }
            ],
            [
                'label' => '开始时间',
                'value' => function($row){
                    return date('Y-m-d',$row->start_time);
                }
            ],
            [
                'label' => '结束时间',
                'value' => function($row){
                    return date('Y-m-d',$row->end_date);
                }
            ],



            [
                'class' => 'backend\grids\LayuiActionColumn',
                'template' => '{view} {update} {sign_in} ',
                'buttons' => [
                     'sign_in' => function($url,$row,$key){
                        return Html::a('签到',['/sign-in/sign-in-list','id' =>$row->id],['class' => 'layui-btn layui-btn-xs layui-btn-warm']);
                     },
                    'study' => function($url,$row,$key){
                        return Html::a('学习',$url,['class' => 'layui-btn layui-btn-xs ']);
                     },
                ]
            ],
        ],
    ]); ?>


</div>
<?php
$js =<<<JS

    $('.export').click(function(){
        var idlist   = [];
        $('.layui-form-checked').each(function () {
            if (typeof($(this).data('id')) !="undefined" && $(this).data('id')!=0){
                idlist.push($(this).data('id'));
            }
        });
        var export_type = $('select[name="export_type"]').val();
        if(!export_type){
            layer.msg('请选择导出类型');return;
        }
         $.ajax({
                type: 'POST',
                data: {idlist: idlist,export_type:export_type},
                dataType: 'json',
                url: 'export',
                success: function(res) {
                    if(res.status == 200){
                        
                        LAY_EXCEL.exportExcel(res.data, res.msg, 'xlsx');
                    }else{
                        layer.msg(res.msg);
                    }
                }
            });
    });
    
JS;
$this->registerJs($js);


?>