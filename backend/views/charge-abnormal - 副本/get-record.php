<?php

use yii\helpers\Html;
use backend\widgets\LayuiGridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\TrainSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


?>
    <div class="x-body">


        <?= LayuiGridView::widget([
            'dataProvider' => $data ,
            'columns' => [
                [
                    'attribute' => 'user_id',
                    'value' => function($row){
                        return (new \backend\models\UserInfo())->getUserName($row->user_id);
                    }
                ],
                'warehouse_number',
                [
                    'attribute' => 'type',
                    'value' => function($row){
                        return \backend\models\ChargingLog::$typeArr[$row->type];
                    }
                ],
                [
                    'label' => '创建时间',
                    'value' => function($row){
                        return date('Y-m-d H:i:s',$row->created_at);
                    }
                ],
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