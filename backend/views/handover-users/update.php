<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\HandoverUsers */

$this->title = 'Update Handover Users: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Handover Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="handover-users-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
