<?php

use yii\helpers\Html;
use backend\widgets\LayuiGridView;
use backend\models\UserInfo;
use backend\models\Maintain;
use backend\models\MaintainType;
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
        <p>
            <?= Html::a('新增', ['create','boss' => 1], ['class' => 'layui-btn ']) ?>
            <?= Html::a('我发起的', ['boss-index',$filtrate => $searchModel::CREATE_KEY], ['class' => 'layui-btn layui-btn-normal']) ?>
            <?= Html::a('<i class="layui-icon">&#xe62f;</i>导出', 'javascript:;',['class' => 'layui-btn layui-bg-black']) ?>
        </p>
        <?= LayuiGridView::widget([
            'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'backend\grids\LayuiCheckBoxColumn'],

                'number',
                [
                    'attribute' => 'things',
                    'value' => function($row) use ($typeList){
                        if($row->things == MaintainType::OTHER_TYPE ){
                            return $row->other_things;
                        }else{

                            return $typeList[$row->things];
                        }
                    }
                ],
                [
                    'label' => '物料批号',
                    'value' => function($row){
                        return '';
                    }
                ],
                'process',
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
                    'attribute' => 'feedback_date',
                    'value' => function($row){
                        if(!empty($row->feedback_date)){
                            return date('Y-m-d',$row->feedback_date);
                        }
                    }
                ],
                //'feedback_date',

                [
                    'class' =>'backend\grids\LayuiActionColumn',
                    'template' => '{view} {access} {feedback}{rejects}',
                    'buttons' =>[
                        'access' => function($url,$row,$params){
                            return Html::a('审核','javascript:;',['class' => 'layui-btn layui-btn-xs verify-leader','data-url' => $url,'data-id' => $row->id]);
                        },
                        'feedback' => function($url,$row,$params){
                            return Html::a('反馈','javascript:;',['class' => 'layui-btn layui-btn-xs verify-user','data-url' => $url,'data-id' => $row->id]);
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
$js = <<<JS
    $('.verify-leader').click(function() {
        let url = $(this).data('url');
        let id = $(this).data('id'); 
        $.get('verify-access-user',{id:id},function(res) {
            if(res.status == 200){
                x_admin_show('审核',url);
               // window.location.href=url;
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
                x_admin_show('反馈',url);
                //window.location.href=url;
            }else{
                layer.msg(res.msg);
            }
        },'json');
        
    });

JS;
$this->registerJs($js);