<?php

use yii\helpers\Html;
use backend\widgets\LayuiActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ExternalAuthority */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="external-authority-form">

    <?php $form =  LayuiActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList($model::$statusArr) ?>


    <div class="layui-form-item"  style="margin-top: 50px;">
        <label for="" class="layui-form-label">
        </label>
        <?= Html::submitButton('保存', ['class' => 'layui-btn','lay-filter'=>"add",'lay-submit'=>""]) ?>
    </div>


    <?php LayuiActiveForm::end(); ?>

</div>
