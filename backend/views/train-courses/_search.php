<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\search\TrainCoursesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="reain-courses-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'author') ?>

    <?= $form->field($model, 'coach') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'type') ?>

    <?php // echo $form->field($model, 'object') ?>

    <?php // echo $form->field($model, 'effect') ?>

    <?php // echo $form->field($model, 'comment') ?>

    <?php // echo $form->field($model, 'ppt') ?>

    <?php // echo $form->field($model, 'video') ?>

    <?php // echo $form->field($model, 'create_time') ?>

    <?php // echo $form->field($model, 'update_time') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
