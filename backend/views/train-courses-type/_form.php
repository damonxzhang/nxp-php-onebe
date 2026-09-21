<?php

use yii\helpers\Html;
use backend\widgets\LayuiActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\TrainCoursesType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="reain-courses-type-form">

    <?php $form = LayuiActiveForm::begin(['id' => 'form']); ?>

    <?= $form->field($model, 'name',['required' => true])->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status',['required' => true])->dropDownList( $model::$status  ) ?>


    <div class="layui-form-item"  style="margin-top: 50px;">
        <label for="" class="layui-form-label">
        </label>
        <?= Html::submitButton('保存', ['class' => 'layui-btn']) ?>
    </div>

    <?php LayuiActiveForm::end(); ?>

</div>
<?php
$js = <<<JS
    layui.use('layer', function(){
        var layer = layui.layer;
 
        $('.submit').click(function() {
            var form = $('#form').serialize();
            layer.load(3);
            $.post('',form,function(res) { 
                layer.closeAll();
                if(res.status == 200){
                    location.href="/train-video/index"
                }else{
                    
                     layer.msg(res.msg);
                }
            },'json');
        });
    }); 
JS;
$this->registerJs($js);

?>
