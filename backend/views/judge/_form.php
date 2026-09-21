<?php

use yii\helpers\Html;
use backend\widgets\LayuiActiveForm;
use backend\models\UserInfo;
use backend\models\Judge;
/* @var $this yii\web\View */
/* @var $model backend\models\IrJudge */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ir-judge-form">

    <?php $form = LayuiActiveForm::begin(); ?>


    <?= $form->field($model, 'type')->dropDownList( Judge::$typeList)?>
    <?= $form->field($model, 'status')->dropDownList( Judge::$statusList)?>


    <?=\backend\widgets\SelectSearch::widget([
        'name' => 'userId' ,
        'text' => '用户',
        'columnName' => Html::getInputName($model,'user_id'),
        'value' => $model->user_id,
        'required' => true,
        'selectWidth' => '800px',
        'options' => [
            'multiple' => false,
        ],
        'pluginOptions' => [
            'closeOnSelect' => true,
        ],
        'selectList' => [
            'area' => [
                'text' =>'请选择区域',
                'data'=> UserInfo::$areaArr,
            ],
        ]

    ])?>


    <div class="form-group">
        <?= Html::button('保存', ['class' => 'layui-btn submit']) ?>
    </div>

    <?php LayuiActiveForm::end(); ?>

</div>
<?php
$js = <<<JS
    $('.submit').click(function() {
        let form = $(this).parents('form').serialize(); 
        $.ajax({
            type:'post',
            dataType:'json',
            data:form,
            success:function(res) {
                if(res.status == 200 ){
                    window.location.href='index';
                }else{
                    layer.msg(res.msg);
                }
            }
        });
    });

JS;
$this->registerJs($js);
?>