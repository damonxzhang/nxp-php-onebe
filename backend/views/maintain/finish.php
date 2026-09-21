<?php

use yii\helpers\ArrayHelper;
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
           <?= Html::a('<i class="layui-icon">&#xe62f;</i>导出', 'javascript:;', ['class' => 'layui-btn layui-bg-black export-excel']) ?>
           <?= Html::a('<i class="layui-icon">&#xe62f;</i>导出反馈', 'javascript:;', ['class' => 'layui-btn layui-bg-black export-feedback-excel']) ?>
        </p>
        <?= LayuiGridView::widget([
            'dataProvider' => $dataProvider,
            'rowOptions' => function($row){
                if($row->boss == Maintain::BOSS_YES){
                    return ['style' => 'color: red;'];
                }else{
                    return [];
                }
            },
            'columns' => [
                ['class' => 'backend\grids\LayuiCheckBoxColumn'],
                'number',
                [
                    'attribute' => 'things',
                    'value' => function($row) use ($typeList){
                        if($row->things == \backend\models\MaintainType::OTHER_TYPE){
                            return $row->other_things;
                        }else{
                            return ArrayHelper::getValue($typeList, $row->things,'');
                        }
                    }
                ],
                [
                    'label' => '物料批号',
                    'headerOptions' => ['width' => '20%'],
                    'value' => function($row){
                       return strtoupper($row->material_number);
                    }
                ],
                [
                    'attribute' => 'status',
                    'value' => function($row){
                        return $row::$statusList[$row->status];
                    }
                ],
                [
                    'label' => '工序',
                    'value' => function($row){
                        return !empty($row->facility->process)? \backend\models\Facility::$processList[$row->facility->process]:'';
                    }
                ],
                [
                    'attribute' => 'create_user',
                    'value' => function($row){
                        return $row->userInfo->chinese_name;
                    }
                ],
                [
                    'attribute' => 'shift',
                    'value' => function($row){
                        return UserInfo::$shiftArr[$row->userInfo->shift];
                    }
                ],
                [
                    'label' => '带班主管',
                    'value' => function($row){
                        return (new UserInfo())->getUserName($row->userInfo->leader_uid);
                    }
                ],
                [
                    'label' => '负责人',
                    'value' => function($row){
                        if(!empty($row->facility)){
                            return  (new UserInfo())->getUserName($row->facility->head_user);
                        }
                        return '';
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

                [
                    'class' =>'backend\grids\LayuiActionColumn',
                    'template' => '{view}{feedback-list}{rejects}',
                    'buttons' => [
                        'feedback-list' => function($url,$row,$params){
                            return Html::a('反馈列表',['/maintain-feedback/index','id' => $row->id],['class' => 'layui-btn layui-btn-xs ' ]);
                        },
                        'rejects' => function($url,$row,$params){
                            return Html::a('次品','javascript:;',['class' => 'layui-btn layui-btn-xs','onclick' =>'x_admin_show("次品数量","'.$url.'")' ]);
                        },
                    ]

                ]
            ],
        ]); ?>


    </div>

<?php
$exportUrl = \yii\helpers\Url::to(array_merge([$this->context->id . '/export-finish-excel'],Yii::$app->request->queryParams));
$exportfeedbackUrl = \yii\helpers\Url::to(array_merge([$this->context->id . '/export-finish-feedback-excel'],Yii::$app->request->queryParams));
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