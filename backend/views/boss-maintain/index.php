<?php

use yii\helpers\Html;
use backend\widgets\LayuiGridView;
use backend\models\UserInfo;
use backend\models\Maintain;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\Maintain */
/* @var $dataProvider yii\data\ActiveDataProvider */
$filtrate = Html::getInputName($searchModel,'filtrate')
?>
    <div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">设备异常</a>
        <a href="javascript:;">
          <cite>异常列表</cite>
        </a>
      </span>
    </div>

    <div class="x-body">
        <?= $this->render('_search',['model' => $searchModel])?>

        <p>
            <!-- <?= 
            Html::a('新增', ['create'], ['class' => 'layui-btn ']) 
            ?> -->
            <?= Html::a('已完成', ['index','BossMaintainSearch[finishMaintain]' => 1] , ['class' => 'layui-btn layui-btn-normal']) ?>
            <?= Html::hiddenInput(Html::getInputName($searchModel,'finishMaintain'),$searchModel->finishMaintain)?>
            <?= Html::a('我发起的', ['index',$filtrate => $searchModel::CREATE_KEY], ['class' => 'layui-btn ']) ?>
            <?= Html::a('待我反馈', ['index',$filtrate => $searchModel::DISPOSE_KEY], ['class' => 'layui-btn layui-btn-normal']) ?>
            <!-- <?= Html::a('<i class="layui-icon">&#xe62f;</i>导出', 'javascript:;', ['class' => 'layui-btn layui-bg-black export-excel']) ?> -->
            <!-- <?= Html::a('<i class="layui-icon">&#xe62f;</i>导出反馈', 'javascript:;', ['class' => 'layui-btn layui-bg-black export-feedback-excel']) ?> -->
            <button class="layui-btn layui-bg-black export_excel" data-query='<?=json_encode(Yii::$app->request->queryParams)?>'><i class="layui-icon">&#xe62f;</i>导出</button>

        </p>

        <?= LayuiGridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                ['class' => 'backend\grids\LayuiCheckBoxColumn'],
                'id',
                [
                    'label'=>'问题描述',
                    'attribute' => 'things',
                    'value' => function($row) {
                        return $row->things;
                    }
                ],
                // [
                //     'label' => '工序',
                //     'value' => function($row){
                //         return !empty($row->facility->process)? \backend\models\Facility::$processList[$row->facility->process]:'';
                //     }
                // ],
                [
                    'label' => '工序',
                    'value' => function($row){
                        return  !empty($row->process)? \backend\models\Facility::$processList[$row->process]:(!empty($row->facility->process)? \backend\models\Facility::$processList[$row->facility->process]:'');
                    }
                ],
                [
                    'label' => '机台号',
                    'value' => function($row){
                        if(!empty($row->facility)){
                            return $row->facility->number;
                        }
                        return '';
                    }
                ],
                // [
                //     'label' => '主反馈人',
                //     'value' => function($row){
                //         if(!empty($row->facility)){
                //             return  (new UserInfo())->getUserName($row->facility->head_user);
                //         }
                //         return '';
                //     }
                // ],
                // [
                //     'attribute' => 'shift',
                //     'value' => function($row){
                //         return UserInfo::$shiftArr[$row->userInfo->shift];
                //     }
                // ],
                [
                    'label' => '主反馈人',
                    'value' => function($row){
                        return (new UserInfo())->getUserName($row->feedback_user);
                    }
                ],
                // [
                //     'label' => '区域',
                //     'value' => function($row){
                //         return  !empty($row->facility)? UserInfo::$areaArr[$row->facility->area]:'';
                //     }
                // ],
                [
                    'label' => '区域',
                    'value' => function($row){
                        return  !empty($row->area)? UserInfo::$areaArr[$row->area]:'';
                    }
                ],
                [
                    'label' => '状态',
                    'value' => function($row){
                        return   \backend\models\BossMaintain::$statusList[$row->status];
                    }
                ],
                [
                    'label' => '类型',
                    'value' => function($row){

                        if(!is_null($row->type))
                        {
                            
                            return   \backend\models\BossMaintain::$typeList[$row->type];
                        }
                        return '';
                        
                    }
                ],
                [
                    'attribute' => 'create_time',
                    'value' => function($row){
                        return  date('Y-m-d H:i:s',$row->create_time);
                    }
                ],
                [
                    'class' =>'backend\grids\LayuiActionColumn',
                    'template' => '{view}{feedback}{feedback-list}',
                    'buttons' =>[
                        'feedback' => function($url,$row,$params){
                            return Html::a('反馈','javascript:;',['class' => 'layui-btn layui-btn-xs verify-user','data-url' => \yii\helpers\Url::to(['/boss-feedback/create','id' => $row->id]),'data-id' => $row->id]);
                        },
                        'feedback-list' => function($url,$row,$params){
                            return Html::a('反馈列表',['/boss-feedback/index','id' => $row->id],['class' => 'layui-btn layui-btn-xs' ]);
                        },
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
        $.get('check-feedback-user',{id:id},function(res) {
            if(res.status == 200){
                window.location.href=url;
            }else{
                layer.msg(res.msg);
            }
        },'json');
        
    });
    // $('.export-excel').click(function(){
    //      var idlist   = [];
    //     $('.layui-form-checked').each(function () {
    //         if (typeof($(this).data('id')) !="undefined" && $(this).data('id')!=0){
    //             idlist.push($(this).data('id'));
    //         }
    //     });
    //     layui.use(['jquery', 'layer'], function() {
    //         var $ = layui.jquery;
    //         $.ajax({
    //             type: 'GET',
    //             data: {idlist: idlist},
    //             dataType: 'json',
    //             url: '{$exportUrl}',
    //             success: function(res) {
    //               //  LAY_EXCEL.exportExcel(res.data, res.msg, 'xlsx');
    //             }
    //         });
    //     });
    // });
    $('.export_excel').click(function(){ 
        let idlist   = [];
        $('.layui-form-checked').each(function () {
            if (typeof($(this).data('id')) !="undefined" && $(this).data('id')!=0){
                idlist.push($(this).data('id'));
            }
        }); 
        let query = $(this).data('query');
        query = JSON.stringify(query);
        window.location.href='export-excel?idlist='+idlist+'&query='+query;
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