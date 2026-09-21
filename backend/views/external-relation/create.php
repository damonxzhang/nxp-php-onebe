<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\ExternalRelation */

$this->title = 'Create External Relation';
$this->params['breadcrumbs'][] = ['label' => 'External Relations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="external-relation-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="external-relation-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= \backend\widgets\FormSelects::widget([
            'selectId' => 'sectionId',
            'connectUser' => $connectUser,
            'selectText' => '用户',
            'selectName' => 'handover_id'
        ]); ?>



        <?= $form->field($model, 'status')->dropDownList($model::$statusArr) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
