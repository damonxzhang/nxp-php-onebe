<?php
use yii\helpers\Html;
use yii\helpers\Url;
use backend\widgets\LayuiGridView;

?>

    <div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">COMPOUND货架</a>
        <a href="javascript:;">
          <cite>申请物料列表</cite>
        </a>
      </span>
    </div>

    <div class="x-body">
        <?=LayuiGridView::widget([
            'dataProvider' => $dataProvider ,
            'columns' => [
                'id',
                [
                    'attribute' => 'user_id',
                    'value' => function($row){
                        return (new \backend\models\UserInfo())->getUserName($row->user_id);
                    }
                ],
                'part_no',
                [
                    'attribute' => 'create_time',
                    'value' => function($row){
                        return date('Y-m-d H:i:s',$row->create_time);
                    }
                ],
                'amount',
                'device_number',
                [
                    'class' =>'backend\grids\LayuiActionColumn',
                    'template' => '{complete}{callback}',
                    'buttons' => [
                        'complete' => function($url,$row){
                            return Html::a('完成','javascript:;',[
                                'class' => 'layui-btn complete',
                                'data-id' => $row->id,
                                'data-url' => $url
                            ]);
                        },
                        'callback' => function($url,$row){
                            return Html::a('撤回','javascript:;',[
                                'class' => 'layui-btn layui-bg-black  callback',
                                'data-id' => $row->id,
                                'data-url' => $url
                            ]);
                        },
                    ]
                ]
            ]
        ])?>
    </div>
<?php
$url = Url::to([$this->context->id. '/allot-materials']);
$redirectUrl = Url::to('/materials-apply/get-materials');
$js = <<<JS
    function required(url,id){ 
        $.post(url,{id:id},function(res){
        if(res.status == 200){
            window.location.reload();
        }else{
            layer.msg(res.msg);
        }
        },'json')
    }
    $('.complete').click(function(){
        let id = $(this).data('id');
        let url = $(this).data('url');
        required(url,id);
    });

    $('.callback').click(function(){
        let id = $(this).data('id');
        let url = $(this).data('url');
        required(url,id);
    });
JS;
$this->registerJs($js);
?>