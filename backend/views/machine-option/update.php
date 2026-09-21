<?php

use backend\widgets\LayuiActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\MachineOption */

?>
    <div class="x-nav">
  <span class="layui-breadcrumb">
  </span>
    </div>
    <div class="x-body">
        <?php $form = LayuiActiveForm::begin(); ?>
        <?= $form->field($model, 'position')->textInput(['maxlength' => true,'placeholder' => '请输入位置编号']) ?>
        <?= $form->field($model, 'status')->dropDownList( $model::$statusList ) ?>
        <?= $form->field($model, 'standard')->textarea(['style' => 'width: 800px;height: 200px']) ?>
        <div class="layui-form-item"  style="margin-top: 20px;">
            <label for="" class="layui-form-label">
            </label>
            <?= Html::button('保存', ['class' => 'layui-btn submit']) ?>
        </div>
        <?php LayuiActiveForm::end(); ?>
    </div>
<?php
$js = <<<JS
    $('.submit').click(function(){
        let form = $(this).parents('form').serialize();
        $.ajax({
            data:form,
            type:'post',
            dataType:'json',
            success:function(res) {
                layer.closeAll();
                if(res.status == 200){
                    layer.msg(res.msg, {icon: 1,time: 1500}, function () {
                        var index = parent.layer.getFrameIndex(window.name);
                        parent.layer.close(index);
                        parent.location.reload();
                    })
                }else{
                    layer.msg(res.msg);
                }
            }
        })
    });
JS;
$this->registerJs($js);