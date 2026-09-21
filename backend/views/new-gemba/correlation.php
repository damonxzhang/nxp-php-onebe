<?php

use yii\helpers\Html;
use backend\widgets\LayuiGridView;
use backend\models\search\NewGembaSearch;
use backend\models\NewGemba;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\NewGembaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
    <div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">NXP WAY</a>
        <a href="javascript:;">
          <cite>Gemba记录</cite>
        </a>
      </span>
    </div>
    <div class="x-body">
        <xblock>
            <?= Html::a('审核人审核',  ['correlation',Html::getInputName($searchModel,'relevant') => NewGembaSearch::ASSESS], ['class' => 'layui-btn']) ?>
            <?= Html::a('主管审核',  ['correlation',Html::getInputName($searchModel,'relevant') => NewGembaSearch::LEADER], ['class' => 'layui-btn']) ?>
            <?= Html::a('待我实施',    ['correlation',Html::getInputName($searchModel,'relevant') => NewGembaSearch::WORK], ['class' => 'layui-btn']) ?>
            <?= Html::a('待我确认',    ['correlation',Html::getInputName($searchModel,'relevant') => NewGembaSearch::CONFIRM], ['class' => 'layui-btn']) ?>
        </xblock>

        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <?= LayuiGridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                [ 'class' => 'backend\grids\LayuiCheckBoxColumn'],
                'number',
                [
                    'attribute' => 'create_user',
                    'value' => function($row){
                        $names = (new \backend\models\UserInfo())->getUserName($row->create_user);
                        if(!empty($names)){
                            return $names;
                        }
                        return '';
                    }
                ],
                [
                    'attribute' => 'participant',
                    'value' => function($row){
                        $names = (new \backend\models\UserInfo())->getUserNameByIdArray($row->participant);
                        if(!empty($names)){
                            return implode(',',$names);
                        }
                        return '';
                    }
                ],
                'title',
                'content',
                [
                    'attribute' => 'type',
                    'value' => function($row){
                        return NewGemba::$typeList[$row->type];
                    }
                ],
                [
                    'attribute' => 'steps',
                    'value' => function($row){
                        return NewGemba::$stepsList[$row->steps];
                    }
                ],
                [
                    'attribute' => 'status',
                    'value' => function($row){
                        return NewGemba::$statusList[$row->status];
                    }
                ],

                [
                    'class' =>'backend\grids\LayuiActionColumn',
                    'template' => '{view}{update}',
                    'buttons' => [
                        'update' => function($url,$row){
                            return Html::a('编辑','javascript:;',[
                                'class' => 'layui-btn layui-btn-xs layui-btn-normal update-btn',
                                'data-id' => $row->id
                            ]);
                        }
                    ]
                ]
            ],
        ]); ?>


    </div>
<?php
$url = urlencode(Yii::$app->request->hostInfo.Yii::$app->request->url);

$js =<<<JS
    $('.update-btn').click(function() {
        var id = $(this).data('id');
        $.ajax({
            url:'befor-update',
            type:'post',
            dataType:'json',
            data:{id:id,redirect_url:"{$url}"},
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
   
        window.location.href='exprot-excel?idlist='+idlist;
      
    }); 

JS;
$this->registerJs($js);

?>