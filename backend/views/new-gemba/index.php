<?php

use yii\helpers\Html;
use backend\widgets\LayuiGridView;
use yii\helpers\Url;
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
    <?= $this->render('_search', ['model' => $searchModel]); ?>
    <xblock>
        <a href="javascript:;" class="layui-btn add-gemba">新增</a>
        <?= Html::a("与我相关",['index',Html::getInputName($searchModel,'relevant') => \backend\models\search\NewGembaSearch::BE_RELATED_TO],['class' => 'layui-btn layui-btn-normal'])?>
        <?php if(Yii::$app->user->identity->userInfo->gemba_role === \backend\models\Gemba::ADMIN_ROLE):?>
            <a class="layui-btn layui-btn-normal"  href="<?=  Url::to(['gemba-user/index']) ?>"> 用户列表</a>
            <a class="layui-btn layui-btn-warm"  href="<?=  Url::to(['team/index']) ?>"> 小组列表</a>
            <a class="layui-btn layui-btn-green"  href="<?=  Url::to(['new-gemba-participle/keywords']) ?>">关键词</a>
            <a class="layui-btn layui-bg-red"  href="<?=  Url::to(['new-gemba-participle/blockedwords']) ?>">屏蔽词</a>
            <a class="layui-btn layui-bg-blue" href="<?=  Url::to(['new-gemba-participle/export-excel-keywords','params' => Yii::$app->request->queryParams]) ?>"><i class="layui-icon">&#xe62f;</i>导出关键词</a>
        <?php endif;?>
        <?php if(Yii::$app->user->identity->userInfo->gemba_role === \backend\models\Gemba::SHADOW_COACH_ROLE):?>
          <a class="layui-btn layui-btn-green"  href="<?=  Url::to(['new-gemba-participle/keywords']) ?>">关键词</a>
          <a class="layui-btn layui-bg-red"  href="<?=  Url::to(['new-gemba-participle/blockedwords']) ?>">屏蔽词</a>
          <a class="layui-btn layui-bg-blue" href="<?=  Url::to(['new-gemba-participle/export-excel-keywords','params' => Yii::$app->request->queryParams]) ?>"><i class="layui-icon">&#xe62f;</i>导出关键词</a>
        <?php endif;?>
        <button class="layui-btn layui-bg-black export_excel" data-query='<?=json_encode(Yii::$app->request->queryParams)?>'><i class="layui-icon">&#xe62f;</i>导出</button>

    </xblock>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= LayuiGridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [ 'class' => 'backend\grids\LayuiCheckBoxColumn'],
            'number',
            'year',
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
            [
                'attribute'=>'title',
                'value'=>function ($row) { return $row->title; },
                'format'=>'raw',
                'label'=>'标题',
                'contentOptions' => ['style'=>'max-width:200px;'],
            ],
            [
                'attribute'=>'content',
                'value'=>function ($row) { return $row->content; },
                'format'=>'raw',
                'label'=>'描述',
                'contentOptions' => ['style'=>'max-width:300px;'],
            ],
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
                'class' =>'backend\grids\LayuiActionColumn',
                'template' => '{view}{update} {user-confirm}{change-area-info}{delete}',
                'buttons' => [
                    'update' => function($url,$row){
                        return Html::a('编辑','javascript:;',[
                            'class' => 'layui-btn layui-btn-xs layui-btn-normal update-btn',
                            'data-id' => $row->id
                        ]);
                    },
//                    'leader-confirm' => function($url,$row){
//                        if($row->steps == NewGemba::MATERIAL_TYPE && $row->isCreateUserLeader($row->id)){
//                            return Html::a('完成确认','javascript:;',[
//                                'class' => 'layui-btn layui-btn-xs layui-btn-normal layui-btn-warm leader_conform',
//                                'data-url' => $url
//                            ]);
//                        }
//                    },
                    'change-area-info' => function($url,$row){
                        if( (new \backend\models\GembaChangeArea())->isChange($row->id) ){
                            return Html::a('区域修改记录','javascript:;',[
                                'class' => 'layui-btn layui-btn-xs layui-btn-primary',
                                'onclick' => "x_admin_show('区域修改记录', ' ".$url."',500,500)"
                            ]);
                        }
                    },
                    'delete' => function($url,$row){
                        if(Yii::$app->user->identity->user_id == 34 || Yii::$app->user->identity->user_id == 1){
                            return Html::a('删除',$url,['class' => 'layui-btn layui-btn-xs layui-btn-normal']);
                        }
                    }
                ]
            ]
        ],
    ]); ?>


</div>
<?php

$js =<<<JS
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
        let idlist   = [];
        $('.layui-form-checked').each(function () {
            if (typeof($(this).data('id')) !="undefined" && $(this).data('id')!=0){
                idlist.push($(this).data('id'));
            }
        }); 
        let query = $(this).data('query');
        query = JSON.stringify(query);
        if(query == "[]"){
            layer.msg('请选择查询条件,并点击查询按钮后再导出操作!');
        }else if(query == '{"NewGembaSearch":{"relevant":"5"}}'){
            layer.msg('请选择查询条件,并点击查询按钮后再导出操作!');
        }else{ 
            window.location.href='exprot-excel?idlist='+idlist+'&query='+query+'&type=1';
        }
    }); 
    
    $('.add-gemba').click(function(){
        layer.confirm('是否选择提交IR', {btn: [ '继续GEMBA','新建IR'], title: "提示"}, function() {
             window.location.href='create';
        },
        function() {
             window.location.href='/ir/create'
        });
    });

JS;
$this->registerJs($js);

?>