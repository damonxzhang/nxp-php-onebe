<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ExternalRelation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="external-relation-form">

    <?php $form = ActiveForm::begin(); ?>



    <?= \backend\widgets\FormSelects::widget([
        'selectId' => 'sectionId',
        'connectUser' => $connectUser,
        'selectText' => '用户',
        'selectName' => 'handover_id',
        'value'  =>  implode($acceptUserArr,','),]); ?>

    <?= $form->field($model, 'status')->dropDownList($model::$statusArr) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
