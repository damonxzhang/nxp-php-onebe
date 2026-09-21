<?php

use yii\helpers\Html;
use backend\widgets\LayuiActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\Maintain */

?>
<div class="x-nav">
  <span class="layui-breadcrumb">
    <a href="javascript:;">首页</a>
    <a href="javascript:;">设备异常</a>
    <a href="javascript:;">
      <cite>Boss直检</cite>
    </a>
  </span>
</div>
<div class="x-body">

    <?php $form = LayuiActiveForm::begin(); ?>


        <?= $form->field($model,'user_id')->widget( '\backend\widgets\SearchUser',[
            'name' => Html::getInputName($model, 'user_id'),
            'localSearch' => true,
            'options' => [
                'multiple' => false,
            ],
        ])?>
        <?= $form->field($model,'area')->dropDownList(\yii\helpers\ArrayHelper::merge(['' => '请选择区域'],\backend\models\UserInfo::$areaArr));?>
        <div class="layui-form-item"  style="margin-top: 50px;">
            <label for="" class="layui-form-label">
            </label>
            <?= Html::submitButton('保存', ['class' => 'layui-btn submit']) ?>
        </div>
    <?php LayuiActiveForm::end(); ?>


</div>
