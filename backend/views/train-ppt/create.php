<?php

use yii\helpers\Html;
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">课件列表</a>
        <a href="javascript:;">
          <cite>创建课件</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
