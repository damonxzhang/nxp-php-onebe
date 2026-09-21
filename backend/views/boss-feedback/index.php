<?php

use backend\models\MaintainFeedback;
use backend\widgets\LayuiGridView;
use yii\helpers\Html;
use backend\models\FileInfo;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\MaintainFeedbackSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'MaintainApi Feedbacks';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">异常反馈</a>
        <a href="javascript:;">
          <cite>异常反馈列表</cite>
        </a>
      </span>
</div>

<div class="x-body">

    <?= LayuiGridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'backend\grids\LayuiCheckBoxColumn'],
            // 'id',
            [
                'label'=>  '所属ID',
                'value' => function($row){
                    return $row->m_id;
                }
            ],
            // [
            //     'label' => '异常编号',
            //     'value' => function($row) use ($maintain){
            //         return $maintain->number;
            //     }
            // ],
            [
                'attribute'=>  'feedback_user',
                'value' => function($row){
                    return (new \backend\models\UserInfo())->getUserName($row->feedback_user);
                }
            ],
           
            [
                'attribute'=>  'action',
                'value' => function($row){
                    if($row->action == MaintainFeedback::OTHER_ACTION ){
                        return $row->other_action;
                    }else{
                        return MaintainFeedback::$actionList[$row->action];
                    }
                }
            ],
            [
                'attribute'=>  'predict_date',
                'value' => function($row){
                    return date('Y-m-d',$row->predict_date);
                }
            ],
            [
                'attribute'=>  'complete_date',
                'value' => function($row){
                    return !empty($row->complete_date)? date('Y-m-d',$row->predict_date) :'';
                }
            ],
            // [
            //     'attribute'=>  'files',
            //     'value' => function($row){
            //         //return !empty($row->complete_date)? date('Y-m-d',$row->predict_date) :'';
            //         // foreach ($row->files as  $val):
            //         //     if(empty($val)) continue;

            //         //     $test='<span class="itemimg"><input type="hidden" name="" value="><img src="" class="layui-upload-img show-img"><i class="delicon layui-icon layui-icon-close-fill image-span"></i></span>';

            //         // endforeach; 
                
            //         return !empty($row->files)? Html::getInputName($row,'files'):'';
            //     }
            // ],
            [
                'attribute' => 'files',
                'format' => 'raw',
                'value' => function($row) {
                    $files=$row->files;
                    if(empty($files)){
                        return "";
                    }
                    if(is_string($files)){
                        $files = explode(',',$files);
                    }
                    $temp=(new FileInfo)->getFileInfoById($files);
                    $temp1="";
                    foreach ($temp as $value){
                        $temp1.=Html::a( Html::img($value["path"], ['height' => '80px']),[$value["path"]]);
                    
                    }
                    return (empty($row->files)) ? '' : $temp1;
//                    Html::img(Yii::$temp, ['height' => '30px'])
                }  
            ],
            
            // [
            //     'class' => 'backend\grids\LayuiActionColumn',
            //     'template' => '{view}'
            // ],
        ],
    ]); ?>


</div>
