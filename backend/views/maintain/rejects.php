<?php

use yii\helpers\Html;
use backend\widgets\LayuiActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\Maintain */

?>
<div class="x-body">
    <div class="maintain-form">

        <?php $form = LayuiActiveForm::begin(); ?>
            <?= $form->field($model, 'rejects')->textInput(['maxlength' => true, 'placeholder'=>"请输入次品数量"]) ?>
            <div class="layui-form-item"  style="margin-top: 50px;">
                <label for="" class="layui-form-label">
                </label>
                <?= Html::button('保存', ['class' => 'layui-btn submit']) ?>
            </div>

        <?php LayuiActiveForm::end(); ?>

</div>
</div>
<?php
$js = <<<JS
 
    
    $('.submit').click(function(){
        let form = $(this).parents('form').serialize();
        $.post('',form,function(res) {
            if(res.status == 200){
                var index = parent.layer.getFrameIndex(window.name);
                parent.layer.close(index);
                parent.location.reload();
            }else{
                layer.msg(res.msg);
            }
            
        },'json')
    });
JS;
$this->registerJs($js);