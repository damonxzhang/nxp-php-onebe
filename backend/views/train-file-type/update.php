<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\TrainFileType */

$this->title = 'Update Train File Type: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Train File Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">培训考核</a>
        <a href="javascript:;">培训文件分类</a>
        <a href="javascript:;">
          <cite>培训文件分类编辑</cite>
        </a>
      </span>
</div>
<div class="x-body">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
