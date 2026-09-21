<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\search\BossFeedback */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="boss-feedback-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'm_id') ?>

    <?= $form->field($model, 'feedback_user') ?>

    <?= $form->field($model, 'type') ?>

    <?= $form->field($model, 'action') ?>

    <?php // echo $form->field($model, 'other_action') ?>

    <?php // echo $form->field($model, 'solve_status') ?>

    <?php // echo $form->field($model, 'predict_date') ?>

    <?php // echo $form->field($model, 'complete_date') ?>

    <?php // echo $form->field($model, 'sendMail') ?>

    <?php // echo $form->field($model, 'create_time') ?>

    <?php // echo $form->field($model, 'update_time') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
