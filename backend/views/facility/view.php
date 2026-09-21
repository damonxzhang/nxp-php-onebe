<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Facility */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Facilities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="facility-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'number',
            'create_user',
            'model',
            'area',
            'shift',
            'serial_no',
            'VENDOR',
            'ASSET_NO',
            'BUY_OFF_DATE',
            'CBA_pass_date',
            'head_user',
            'status',
            'is_ok',
            'file',
            'create_time',
            'update_time',
            'delete_time',
        ],
    ]) ?>

</div>
