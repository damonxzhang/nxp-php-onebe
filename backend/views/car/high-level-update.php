<?php

use yii\helpers\Html;
use backend\widgets\LayuiActiveForm;

?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">DPR</a>
        <a href="javascript:;">
          <cite>HIGH LEVEL</cite>
        </a>
      </span>
</div>

<div class="x-body">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="ir-judge-form">
      <?php $form = LayuiActiveForm::begin(); ?>
      <?= $form->field($model, 'status')->dropDownList(  \backend\models\CarHighLevel::$statusList)?>
      <?= $form->field($model, 'type')->dropDownList(  \backend\models\CarHighLevel::$types)?>
      <div class="form-group field-facility-cba_pass_date required">
        <div class="layui-form-item">
            <label for="tel" class="layui-form-label">负责人:</label>
            <div class="layui-input-inline" style="width: 800px;">
                <?= \backend\widgets\SearchUser::widget([
                    'name' => Html::getInputName($model, 'user_id'),
                    'url' => \yii\helpers\Url::to('/user-info/search-by-name'),
                    'value' => $model->user_id
                ])?>
            </div>
            <div class="layui-col-md4" style="color:red"><div class="help-block"></div></div>
        </div>
     </div>
    
      <div class="form-group">
          <?= Html::submitButton('保存', ['class' => 'layui-btn submit']) ?>
      </div>
      <?php LayuiActiveForm::end(); ?>

    </div>

</div>
