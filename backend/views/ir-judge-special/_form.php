<?php

use yii\helpers\Html;
use backend\widgets\LayuiActiveForm;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model backend\models\IrJudgeSpecial */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ir-judge-form">
    <?php $form = LayuiActiveForm::begin(); ?>
    <?= $form->field($model, 'status')->dropDownList( \backend\models\IrJudgeSpecial::$statusList)?>
    <?= $form->field($model, 'section_ir')->dropDownList( \backend\models\IrJudgeSpecial::$sectionIrArr)?>
    <div class="form-group field-irjudge-status">
        <div class="layui-form-item">
            <label for="tel" class="layui-form-label">用户:</label>
            <div class="layui-input-inline" style="width: 800px;">
                <?=\backend\widgets\SearchUser::widget([
                    'name' => Html::getInputName($model, 'user_id'),
                    'localSearch' => true,
                    'options' => [
                        'multiple' => false,
                    ],
                ])?>
            <div class="layui-col-md4" style="color:red"><div class="help-block"></div></div>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'layui-btn submit']) ?>
    </div>
    <?php LayuiActiveForm::end(); ?>

</div>
