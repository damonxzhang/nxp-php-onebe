<?php

use yii\helpers\Html;
use backend\widgets\LayuiActiveForm;
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">DPR</a>
        <a href="javascript:;">
          <cite>HIGH LEVEL</cite>
        </a>
      </span>
</div>

<div class="x-body">

    <h1><?= Html::encode($this->title) ?></h1>
    <div class="ir-judge-form">
      <?php $form = LayuiActiveForm::begin(); ?>
      <?= $form->field($model, 'defect')->textInput( ['class'  => 'layui-input', 'lay-verify'=>"required",'autocomplete'=>"off" ,'placeholder' => '请输入缺陷项目']) ?>
      <?= $form->field($model, 'weight')->dropDownList(  \backend\models\CarDefect::$statusWeight)?>
      <?= $form->field($model, 'status')->dropDownList(  \backend\models\CarDefect::$statusList)?>
      <?= $form->field($model, 'type')->dropDownList(  \backend\models\CarDefect::$types)?>
      <div class="form-group">
          <?= Html::submitButton('保存', ['class' => 'layui-btn submit']) ?>
      </div>
      <?php LayuiActiveForm::end(); ?>
  </div>
</div>
