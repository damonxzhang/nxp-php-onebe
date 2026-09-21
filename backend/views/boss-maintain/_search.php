<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use backend\models\UserInfo;
use backend\widgets\SearchActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\search\Maintain */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="layui-row">

    <?php $form = SearchActiveForm::begin([
        'method' => 'get',
    ]); ?>
    <div class="layui-input-inline">
        <?= $form->field($model, 'process')->dropDownList(ArrayHelper::merge(['' => '请选择工序'],\backend\models\Facility::$processList)) ?>
    </div>
    <div class="layui-input-inline">
        <?= $form->field($model, 'shift')->dropDownList(ArrayHelper::merge(['' => '请选择班别'],UserInfo::$shiftArr)) ?>
    </div>
    <div class="layui-input-inline">
        <?= $form->field($model, 'area')->dropDownList(ArrayHelper::merge(['' => '请选择区域'],UserInfo::$areaArr)) ?>
    </div>
    <div class="layui-input-inline">
        <?= $form->field($model, 'start_time')->textInput([ 'id' => 'startTime','placeholder' => '请输入开始时间','autocomplete'=>"off"]) ?>
    </div>
    <div class="layui-input-inline">
        <?= $form->field($model, 'end_time')->textInput(['id' => 'endTime','placeholder' => '请输入结束时间','autocomplete'=>"off"]) ?>
    </div>
    <div class="layui-input-inline">
        <?= $form->field($model, 'type')->dropDownList(ArrayHelper::merge(['' => '请选择类型'],\backend\models\BossMaintain::$typeList)) ?>
    </div>

    <?= Html::submitButton('<i class="layui-icon"></i>查询', ['class' => 'layui-btn']) ?>
    <?= Html::a('<i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>刷新','index', ['class' => 'layui-btn']) ?>



    <?php SearchActiveForm::end(); ?>

</div>
