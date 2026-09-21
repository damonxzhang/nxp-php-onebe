<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use backend\widgets\LayuiActiveForm;
use backend\models\TrainFileType;

/* @var $this yii\web\View */
/* @var $model backend\models\TrainFileType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="layui-form">

    <?php $form = LayuiActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->dropDownList(ArrayHelper::merge(['' => '请选择类型'],TrainFileType::$types)) ?>

    <?= $form->field($model, 'status')->dropDownList(TrainFileType::$statusArr) ?>

    <div class="layui-form-item"  style="margin-top: 50px;">
        <label for="" class="layui-form-label">
        </label>
        <?= Html::button('保存', ['class' => 'layui-btn submit-btn',' lay-submit' => '','lay-filter'=>"add"]) ?>
    </div>
    <?php LayuiActiveForm::end(); ?>

</div>
<?php
$js = <<<JS
    layui.use('layer', function(){
        var layer = layui.layer;
 
        $('.submit-btn').click(function() {
            var form = $('form').serialize();
            layer.load(3);
            $.post('',form,function(res) { 
                layer.closeAll();
                if(res.status == 200){
                    location.href="/train-file-type/index"
                }else{ 
                     layer.msg(res.msg);
                }
            },'json');
        });
    }); 
JS;
$this->registerJs($js);
?>