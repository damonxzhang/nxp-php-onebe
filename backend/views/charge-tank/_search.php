<?php

use yii\helpers\Html;
use backend\models\Team;
use backend\models\IrData;
use backend\models\UserStation;
use backend\widgets\SearchActiveForm;
use yii\helpers\ArrayHelper;
use backend\models\UserInfo;
$team = ArrayHelper::map(Team::getAllByArray(),'id','teamName');
/* @var $this yii\web\View */
/* @var $model backend\models\search\TrainSearch */
/* @var $form yii\widgets\ActiveForm */
?>

    <div class="layui-row">

        <?php $form = SearchActiveForm::begin([

            'method' => 'get',
        ]); ?>

        <?= $form->field($model, 'id')->input('text',['autocomplete'=>"off" ,'placeholder' => '请选输入ID']) ?>

        <div class="layui-input-inline">
            <?= $form->field($model, 'shift' )->dropDownList(ArrayHelper::merge(['' => '请选择班别'] ,UserInfo::$shiftArr)) ?>
        </div>
        <div class="layui-input-inline">
            <?= $form->field($model, 'team' )->dropDownList(ArrayHelper::merge(['' => '请选择gemba小组'] ,Team::getTeamData())) ?>
        </div>
        <div class="layui-input-inline">
            <?= $form->field($model, 'station_id' )->dropDownList(ArrayHelper::merge(['' => '请选择站别'] ,UserStation::getStationList())) ?>
        </div>
        <div class="layui-input-inline">
            <?= $form->field($model, 'steps' )->dropDownList(ArrayHelper::merge(['' => '请选择实施步骤'] ,IrData::$stepsArr)) ?>
        </div>
        <div class="layui-input-inline">
            <?= $form->field($model, 'status')->dropDownList(ArrayHelper::merge(['' => '请选择实施状态'] ,IrData::$statusArr)) ?>
        </div>
        <div class="layui-input-inline">
            <?= $form->field($model, 'area')->dropDownList(ArrayHelper::merge(['' => '请选择区域'] ,UserInfo::$areaArr)) ?>
        </div>
        <div class="layui-input-inline">
            <?= $form->field($model, 'advice_type')->dropDownList(ArrayHelper::merge(['' => '请选择建议类型'] ,IrData::$adviceTypes)) ?>
        </div>

        <?= $form->field($model, 'start_time')->input('text',['id' => "startTime",'autocomplete'=>"off" ,'placeholder' => '请选择开始时间']) ?>
        <?= $form->field($model, 'over_time')->input('text',['id' => "endTime",'autocomplete'=>"off",'placeholder' => '请选择结束时间']) ?>

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