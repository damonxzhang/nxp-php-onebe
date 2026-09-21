<?php

use yii\helpers\Html;
use backend\widgets\LayuiActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\MaintainAreaHead */

?>
<div class="x-nav">
  <span class="layui-breadcrumb">
    <a href="javascript:;">首页</a>
    <a href="javascript:;">设备异常</a>
    <a href="javascript:;">
      <cite>新增区域负责人</cite>
    </a>
  </span>
</div>
<div class="x-body">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="maintain-area-head-form">

        <?php $form = LayuiActiveForm::begin(); ?>
        <div class="form-group field-maintainareahead-status">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">负责人:</label>
                <div class="layui-input-inline" style="width: 800px;">  <?=\backend\widgets\SearchUser::widget([
                        'name' => Html::getInputName($model, 'user_id'),
                        'url' => \yii\helpers\Url::to('/user-info/search-by-name'),
                        'value' => $model->user_id,
                    ]); ?>
                </div>
            </div>
        </div>

        <?= $form->field($model,'area')->dropDownList(\yii\helpers\ArrayHelper::merge(['' => '请选择区域'],\backend\models\UserInfo::$areaArr));?>


        <?= $form->field($model, 'status')->dropDownList(\backend\models\MaintainAreaHead::$statusList) ?>

        <div class="form-group">
            <label for="tel" class="layui-form-label"></label>
            <?= Html::button('提交', ['class' => 'layui-btn','id' => 'submit']) ?>
        </div>

        <?php LayuiActiveForm::end(); ?>

    </div>

</div>
<?php

?>