<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\TrainFileType */
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">培训考核</a>
        <a href="javascript:;">培训文件分类</a>
        <a href="javascript:;">
          <cite>培训文件分类新增</cite>
        </a>
      </span>
</div>
<div class="x-body">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
