<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Maintain */

?>
<div class="x-nav">
  <span class="layui-breadcrumb">
    <a href="javascript:;">首页</a>
    <a href="javascript:;">设备异常</a>
    <a href="javascript:;">
      <cite>添加异常</cite>
    </a>
  </span>
</div>
<div class="x-body">
    <?= $this->render('_create_form', [
        'model' => $model,
        'userInfo' => $userInfo
    ]) ?>

</div>
