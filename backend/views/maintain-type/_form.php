<?php

use yii\helpers\Html;
use backend\widgets\LayuiActiveForm;
use yii\helpers\ArrayHelper;
use backend\models\MaintainType;
use backend\models\UserInfo;

/* @var $this yii\web\View */
/* @var $model backend\models\MaintainType */
/* @var $form yii\widgets\ActiveForm */
?>
    <?php $form = LayuiActiveForm::begin(); ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'process')->dropDownList(ArrayHelper::merge(['' => '请选择工序'],MaintainType::$processList)) ?>
    <?= $form->field($model, 'area')->dropDownList(ArrayHelper::merge(['' => '请选择区域'], UserInfo::$areaArr)) ?>
    <div class="layui-form-item"  style="margin-top: 50px;">
        <label for="" class="layui-form-label">
        </label>
        <?= Html::button('保存', ['class' => 'layui-btn submit']) ?>
    </div>
    <?php LayuiActiveForm::end(); ?>

<?php
$js = <<<JS
    $('.submit').click(function(){
        let form = $(this).parents('form').serialize();
        $.ajax({
            data:form,
            type:'post',
            dataType:'json',
            success:function(res) {
                if(res.status == 200){
                    window.location.href='index';
                }else{
                    layer.msg(res.msg);
                }
            }
        })
        
    });
     
JS;

$this->registerJs($js);
?>