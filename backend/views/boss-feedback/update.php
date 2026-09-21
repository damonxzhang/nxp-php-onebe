<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\BossFeedback */

$this->title = 'Update Boss Feedback: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Boss Feedbacks', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="boss-feedback-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
