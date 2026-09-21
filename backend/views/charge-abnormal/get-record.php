<?php

use yii\helpers\Html;
use backend\widgets\LayuiGridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;



/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\TrainSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


?>
    <div class="x-body">
        <div class="layui-row">
            <form class="layui-form layui-col-md12 x-so">
               
                <div class="layui-input-inline">
                    <?= Html::dropDownList('type', ArrayHelper::getValue($request, 'type'), ArrayHelper::merge(['' => '状态操作类型'], \backend\models\ChargingLog::$typeArr), ['name' => 'status']); ?>
                </div>
                <input class="layui-input" placeholder="开始日" name="start_time" id="start_time" value="<?= !empty($request['start_time']) ? date('Y-m-d H:i:s',strtotime($request['start_time'])) : '' ?>" autocomplete="off">
                <input class="layui-input" placeholder="截止日" name="end_time" id="end_time" value="<?= !empty($request['end_time']) ? date('Y-m-d H:i:s',strtotime($request['end_time'])) : '' ?>" autocomplete="off">
                <button class="layui-btn"  lay-submit="" lay-filter="sreach"><i class="layui-icon">&#xe615;</i>查询</button>
                <button class="layui-btn" type="button" onclick="location.href= '<?= Url::to([$this->context->id . '/index'])?>//'" >
                    <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>刷新
                </button>
            </form>
        </div>

        <?= LayuiGridView::widget([
            'dataProvider' => $data ,
            'columns' => [
                [
                    'attribute' => '操作人',
                    'value' => function($row){
                        return (new \backend\models\UserInfo())->getUserByBadgeCode($row->wbi_code)->chinese_name;
                    }
                ],
                'tank_number',
                'warehouse_number',
                [
                    'attribute' => '操作类型',
                    'value' => function($row){
                        return \backend\models\ChargingLog::$typeArr[$row->type];
                    }
                ],
                [
                    'label' => '操作时间',
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