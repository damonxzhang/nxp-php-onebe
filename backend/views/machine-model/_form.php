<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\MachineModel */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="machine-model-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'createby')->textInput() ?>

    <?= $form->field($model, 'createdt')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'updatedt')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
