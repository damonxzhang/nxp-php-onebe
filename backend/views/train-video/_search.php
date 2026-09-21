<?php

use yii\helpers\Html;
use backend\widgets\SearchActiveForm;
use yii\helpers\ArrayHelper;
use backend\models\TrainFileType;
/* @var $this yii\web\View */
/* @var $model backend\models\search\TrainVideoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="layui-row">

    <?php $form = SearchActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'name')->input('text',['placeholder' => '请输入视频名称']) ?>
    <div class="layui-input-inline">

    <?= $form->field($model, 'type')->dropDownList(ArrayHelper::merge(['' => '请选择类型'],TrainFileType::getTypes(TrainFileType::TYPE_VIDEO))) ?>

    </div>
    <?php // echo $form->field($model, 'create_time') ?>

    <?= Html::submitButton('<i class="layui-icon"></i>查询', ['class' => 'layui-btn']) ?>
    <?= Html::a('<i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>刷新','index', ['class' => 'layui-btn']) ?>


    <?php SearchActiveForm::end(); ?>

</div>
