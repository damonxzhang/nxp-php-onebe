<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\IrJudgeSpecial */

?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">IR评审员</a>
        <a href="javascript:;">
          <cite>新增特殊评估人</cite>
        </a>
      </span>
</div>

<div class="x-body">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
