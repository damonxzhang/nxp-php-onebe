<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\widgets\SearchActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\search\TrainSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="layui-row">

    <?php $form = SearchActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="layui-input-inline">
        <?= $form->field($model, 'courses' )->dropDownList(['' => '请输入课程名称'],['class' => 'coursesName','xm-select' => 'coursesName','xm-select-radio' =>'','xm-select-search' => '/train-courses/search-select']) ?>

    </div>


    <?= $form->field($model, 'start_time')->input('text',['id' => "startTime",'autocomplete'=>"off" ,'placeholder' => '请选择开始时间']) ?>
    <?= $form->field($model, 'end_time')->input('text',['id' => "endTime",'autocomplete'=>"off",'placeholder' => '请选择结束时间']) ?>



    <?= Html::submitButton('<i class="layui-icon"></i>查询', ['class' => 'layui-btn']) ?>
    <?= Html::a('<i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>刷新','index', ['class' => 'layui-btn']) ?>

    <?php SearchActiveForm::end(); ?>

</div>

<?php
$js = <<<JS
    layui.use('laydate',function() {
        var laydate = layui.laydate;
            //年选择器
            laydate.render({
                elem: '#startTime' 
            });
            
            //年月选择器
            laydate.render({
                elem: '#endTime' 
            });
    });
JS;
$js = $this->registerJs($js);
?>