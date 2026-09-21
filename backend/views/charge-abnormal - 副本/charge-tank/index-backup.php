<?php

use yii\helpers\Html;
use backend\widgets\LayuiGridView;
use backend\models\ChargingWarehouse;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\TrainSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
    <div class="x-body">


        <?= LayuiGridView::widget([
            'dataProvider' => $data ,
            'rowOptions'=>function($row){
                if($row['status'] == 1 ){
                    if ($row['type'] == 1 ) { //判断条件
                        return ['style'=>'color:#d3d24a;'];
                    }elseif ( $row['type'] == 2 ){
                        return ['style'=>'color:blue;'];
                    }elseif ( $row['type'] == 3 ){
                        return ['style'=>'color:red;'];
                    }else {
                        return ['style'=>'color:green;'];
                    }
                }else{
                    return ['style'=>'color:gray;'];
                }
            },
            'columns' => [
                [
                    'attribute' => '设备编号',
                    'value' => function($row){
                        return $row->number;
                    }
                ],
                [
                    'attribute' => '设备类型',
                    'value' => function($row){
                        return ArrayHelper::getValue(ChargingWarehouse::$stypeArr,$row->stype);
                    }
                ],
                [
                    'attribute' => '当前状态',
                    'value' => function($row){
                        return ArrayHelper::getValue(ChargingWarehouse::$statusArr,$row->status);
                    }
                ],
                [
                    'attribute' => '使用状态',
                    'value' => function($row){
                        return ArrayHelper::getValue(ChargingWarehouse::$typeArr,$row->type);
                    }
                ],
                [
                    'attribute' => '领用人',
                    'value' => function($row){
                        return (new \backend\models\UserInfo())->getUserName($row->user_id);
                    }
                ],
                [
                    'class' =>'backend\grids\LayuiActionColumn',
                    'template' => '{update}',
                    'buttons' => [
                        'update' => function($url,$row,$key){
                            return Html::a('编辑',['/charge-tank/update','id' =>$row->id],['class' => 'layui-btn layui-btn-xs layui-btn-normal']);
                        },
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
        if(idlist.length <= 0){
            layer.msg('请选择导出的IR!');
            return false;
        }
        window.location.href='exprot-excel?idlist='+idlist;
      
    }); 
JS;
$this->registerJs($js);
?>