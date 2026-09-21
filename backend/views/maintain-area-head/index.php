<?php

use yii\helpers\Html;
use backend\models\UserInfo;
use backend\widgets\LayuiGridView;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\MaintainAreaHeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="x-nav">
  <span class="layui-breadcrumb">
    <a href="javascript:;">首页</a>
    <a href="javascript:;">设备异常</a>
    <a href="javascript:;">
      <cite>区域负责人列表</cite>
    </a>
  </span>
</div>
<div class="x-body">

    <p>
        <?= Html::a('新增区域负责人', 'javascript:;', ['class' => 'layui-btn popup-page','data-popup-url'=> 'create','data-popup-text' => '新增']) ?>
    </p>

    <?= LayuiGridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            [
                'attribute' => 'area',
                'value' => function($row){
                    return UserInfo::$areaArr[$row->area];
                }
            ],
            [
                'attribute' => 'user_id',
                'value' => function($row){
                    return (new UserInfo())->getUserName($row->user_id);
                }
            ],
            [
                'attribute' => 'status',
                'value' => function($row){
                    return $row::$statusList[$row->status];
                }
            ],
            [
                'class' => 'backend\grids\LayuiActionColumn',
                'template' => '{change-status}',
                'buttons' => [
                    'change-status' => function($url,$row){
                        if($row->status == $row::SELECT_YES ){
                            return Html::a('关闭','javascript:;',['data-url' => $url,'class' => 'layui-btn change layui-btn-warm']);

                        }else{
                            return Html::a('开启','javascript:;',['data-url' => $url,'class' => 'layui-btn change']);
                        }

                        
                    }
                ],
            ],
        ],
    ]); ?>
</div>
<?php
$js = <<<JS
    $('.change').click(function(){
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

