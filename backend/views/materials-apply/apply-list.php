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
          <cite>申请列表</cite>
        </a>
      </span>
</div>

<div class="x-body">
    <p>
        <?= Html::a('新增','javascript:;',['class' => 'layui-btn popup-page','data-popup-url' => 'create','data-popup-text' => '新增'])?>
    </p>
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
            'machine_id',
            [
                'class' =>'backend\grids\LayuiActionColumn',
                'template' => '{allot-materials}{withdraw}',
                'buttons' => [
                    'allot-materials' => function($url,$row){
                        return Html::a('领取','javascript:;',[
                            'class' => 'layui-btn layui-btn-xs allot-materials',
                            'data-url' => $url
                        ]);
                    },
                    'withdraw' => function($url,$row){
                        return Html::a('撤回','javascript:;',[
                            'class' => 'layui-btn layui-btn-xs layui-bg-black withdraw',
                            'data-url' => $url
                        ]);
                    },
                ]
            ]
        ]
    ])?>
</div>
<?php
$js = <<<JS
    $('.allot-materials').click(function(){
        let url = $(this).data('url');
        $.get(url,function(res){
            if(res.status == 200){
                window.location.href= '/materials-apply/get-materials?id='+id;
            }else{
                layer.msg(res.msg);
            }
        },'json')
    });
    
    $('.withdraw').click(function(){
        let url = $(this).data('url');
        $.get(url,function(res){
            if(res.status == 200){
                window.location.reload();
            }else{
                layer.msg(res.msg);
            }
        },'json')
    });

JS;
$this->registerJs($js);
?>