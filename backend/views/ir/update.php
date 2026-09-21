<?php
use yii\helpers\Html;
?>
<?=Html::cssFile('@css/ir.css'); ?>
<div class="x-nav">
  <span class="layui-breadcrumb">
    <a href="javascript:;">首页</a>
    <a href="javascript:;">IR</a>
    <a href="javascript:;">
      <cite>IR编辑</cite>
    </a>
  </span>
</div>
<?= $this->render($view,array_merge(['model' =>$model ],$params) ); ?>
