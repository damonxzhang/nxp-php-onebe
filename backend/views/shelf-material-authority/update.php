<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\ShelfMaterialAuthority */

$this->title = 'Update Shelf Material Authority: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Shelf Material Authorities', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="shelf-material-authority-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
