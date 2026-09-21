<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\ExternalAuthority */

?>
<div class="x-nav">
    <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">外部系统</a>
        <a href="<?= \yii\helpers\Url::to(['external-system/index'])?>">系统列表</a>
        <a href="<?= \yii\helpers\Url::to(['index','pid' => Yii::$app->request->get('pid')])?>">权限列表</a>
        <a href="javascript:;">
            <cite>创建外部权限</cite>
        </a>
    </span>
</div>
<div class="x-body layui-anim layui-anim-up">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
