<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Facility */


?>
<div class="x-nav">
  <span class="layui-breadcrumb">
    <a href="javascript:;">首页</a>
    <a href="javascript:;">设备管理</a>
    <a href="javascript:;">
      <cite>编辑设备</cite>
    </a>
  </span>
</div>
<div class="x-body">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
