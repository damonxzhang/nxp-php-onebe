<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\MaintainType */

?>
<div class="x-nav">
  <span class="layui-breadcrumb">
    <a href="javascript:;">首页</a>
    <a href="javascript:;">异常类型</a>
    <a href="javascript:;">
      <cite>编辑类型</cite>
    </a>
  </span>
</div>
<div class="x-body">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>