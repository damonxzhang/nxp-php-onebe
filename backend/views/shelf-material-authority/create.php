<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\ShelfMaterialAuthority */

$this->title = 'Create Shelf Material Authority';
$this->params['breadcrumbs'][] = ['label' => 'Shelf Material Authorities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shelf-material-authority-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
