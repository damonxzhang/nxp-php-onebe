<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\TrainVideo */

?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">视频列表</a>
        <a href="javascript:;">
          <cite>更新视频</cite>
        </a>
      </span>
</div>
<div class="x-body">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
