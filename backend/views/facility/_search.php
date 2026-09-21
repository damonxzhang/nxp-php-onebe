<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\search\FacilitySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="facility-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'number') ?>

    <?= $form->field($model, 'create_user') ?>

    <?= $form->field($model, 'model') ?>

    <?php // echo $form->field($model, 'area') ?>

    <?php // echo $form->field($model, 'shift') ?>

    <?php // echo $form->field($model, 'serial_no') ?>

    <?php // echo $form->field($model, 'VENDOR') ?>

    <?php // echo $form->field($model, 'ASSET NO') ?>

    <?php // echo $form->field($model, 'BUY-OFF DATE') ?>

    <?php // echo $form->field($model, 'CBA_pass_date') ?>

    <?php // echo $form->field($model, 'head_user') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'is_ok') ?>

    <?php // echo $form->field($model, 'file') ?>

    <?php // echo $form->field($model, 'create_date') ?>

    <?php // echo $form->field($model, 'update_date') ?>

    <?php // echo $form->field($model, 'delete_date') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
