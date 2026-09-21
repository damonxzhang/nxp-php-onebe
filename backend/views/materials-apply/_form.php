<?php

use yii\helpers\Html;
use backend\models\Maintain;
use yii\helpers\ArrayHelper;
use backend\models\UserInfo;
use backend\models\MaintainType;
use backend\widgets\LayuiActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Maintain */
/* @var $form yii\widgets\ActiveForm */
?>

    <div class="maintain-form">

        <?php $form = LayuiActiveForm::begin(); ?>

        <?= $form->field($model, 'part_no')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'amount')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'device_number')->textInput(['maxlength' => true]) ?>

        <div class="layui-form-item"  style="margin-top: 50px;">
            <label for="" class="layui-form-label">
            </label>
            <?=Html::button('保存',['class' => 'layui-btn','id' => 'submit'])?>
        </div>

        <?php LayuiActiveForm::end(); ?>

    </div>
<?php
$type = Maintain::OTHER_PROBLEM_TYPE;
$things = MaintainType::OTHER_TYPE;
$js = <<<JS
 
    $('.submit').click(function(){
        let form = $(this).parents('form').serialize();
        $.post('',form,function(res) {
            if(res.status == 200){
                window.location.href='index';
            }else{
                layer.msg(res.msg);
            }
            
        },'json')
    });
JS;
$this->registerJs($js);