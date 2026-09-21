<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\HandoverUsers */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="handover-users-form">

    <?php $form = ActiveForm::begin(); ?>

    <select xm-select="select-principal" name="<?= Html::getInputName($model,'user_id') ?>" xm-select-search='/user-info/search-by-name'>
        <?php
        $head_users =  [];
        if(!empty($head_users)):
            foreach ($head_users as $key => $val):
                ?>
                <option value="<?=$key?>" selected><?=$val?> </option>
            <?php endforeach; endif;?>
    </select>
    <div class="form-group" style="margin: 1rem;">
        <?= Html::button('保存', ['class' => 'layui-btn  submit']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
<?php
$js = <<<JS
    $('.submit').click(function(){
        var form = $(this).parents('form').serialize();
        $.ajax({
            data:form,
            dataType:'json',
            type:'post',
            success:function(res) {
                if(res.status == 200){
                    layer.msg(res.msg, {icon: 1,time: 1500}, function () {
                        var index = parent.layer.getFrameIndex(window.name);
                        parent.layer.close(index);
                        parent.location.reload();
                    })
                }else{
                    layer.msg(res.msg, {icon: 1,time: 2000})
                }
            }
        });
        
    });
  
JS;
$this->registerJs($js);
?>