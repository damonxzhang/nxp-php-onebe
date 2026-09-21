<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\MaintainFeedback */

$this->title = 'Update MaintainApi Feedback: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'MaintainApi Feedbacks', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="maintain-feedback-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
