<?php

use yii\helpers\Html;
use backend\widgets\LayuiGridView;
use backend\models\UserInfo;
use backend\models\Maintain;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
    <div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">设备异常</a>
        <a href="javascript:;">
          <cite>异常区域负责人</cite>
        </a>
      </span>
    </div>

    <div class="x-body">

        <p>
            <?= Html::a('新增', ['create'], ['class' => 'layui-btn ']) ?>
        </p>
        <?= LayuiGridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                ['class' => 'backend\grids\LayuiCheckBoxColumn'],
                [
                    'attribute' => 'user_id',
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
                    'class' =>'backend\grids\LayuiActionColumn',
                    'template' => '{delete}',
                    'buttons' =>[
                        'delete' => function($url){
                            return Html::a('删除',$url,['class' => 'layui-btn  layui-btn-danger']);
                        }
                    ]
                ]
            ],
        ]); ?>


    </div>

<?php
$exportUrl = \yii\helpers\Url::to(array_merge([$this->context->id . '/export-excel'],Yii::$app->request->queryParams));
$exportfeedbackUrl = \yii\helpers\Url::to(array_merge([$this->context->id . '/export-feedback-excel'],Yii::$app->request->queryParams));
$js = <<<JS
    $('.verify-leader').click(function() {
        let url = $(this).data('url');
        let id = $(this).data('id'); 
        $.get('verify-access-user',{id:id},function(res) {
            if(res.status == 200){
                window.location.href=url;
            }else{
                layer.msg(res.msg);
            }
        },'json');
        
    });
    $('.verify-user').click(function() {
        let url = $(this).data('url');
        let id = $(this).data('id'); 
        $.get('verify-feedback-user',{id:id},function(res) {
            if(res.status == 200){
                window.location.href=url;
            }else{
                layer.msg(res.msg);
            }
        },'json');
        
    });
    $('.export-excel').click(function(){
         var idlist   = [];
        $('.layui-form-checked').each(function () {
            if (typeof($(this).data('id')) !="undefined" && $(this).data('id')!=0){
                idlist.push($(this).data('id'));
            }
        });
        layui.use(['jquery', 'layer'], function() {
            var $ = layui.jquery;
            $.ajax({
                type: 'GET',
                data: {idlist: idlist},
                dataType: 'json',
                url: '{$exportUrl}',
                success: function(res) {
                    LAY_EXCEL.exportExcel(res.data, res.msg, 'xlsx');
                }
            });
        });
    });
    $('.export-feedback-excel').click(function(){
         var idlist   = [];
        $('.layui-form-checked').each(function () {
            if (typeof($(this).data('id')) !="undefined" && $(this).data('id')!=0){
                idlist.push($(this).data('id'));
            }
        });
        layui.use(['jquery', 'layer'], function() {
            var $ = layui.jquery;
            $.ajax({
                type: 'GET',
                data: {idlist: idlist},
                dataType: 'json',
                url: '{$exportfeedbackUrl}',
                success: function(res) {
                    LAY_EXCEL.exportExcel(res.data, res.msg, 'xlsx');
                }
            });
        });
    });
    
 
JS;
$this->registerJs($js);