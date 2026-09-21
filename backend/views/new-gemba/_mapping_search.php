<?php

use yii\helpers\Html;
use backend\widgets\SearchActiveForm;
use backend\models\NewGemba;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model backend\models\search\NewGembaSearch */
/* @var $form yii\widgets\ActiveForm */
?>


<div class="layui-row">

    <?php $form = SearchActiveForm::begin([

        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'number')->input('text',[ 'placeholder'=>"请输入编号" ]) ?>
    <?= $form->field($model, 'title')->input('text',[ 'placeholder'=>"请输入标题" ]) ?>
    <?= $form->field($model, 'year')->input('text',[ 'placeholder'=>"请输入年" ]) ?>
    <div class="layui-input-inline">
        <?= $form->field($model, 'type')->dropDownList(ArrayHelper::merge(['' => '请选择类型'],NewGemba::$typeList) ) ?>
    </div>
    <div class="layui-input-inline">
        <?= $form->field($model, 'shift')->dropDownList(ArrayHelper::merge(['' => '请选择班别'],\backend\models\UserInfo::$shiftArr)) ?>
    </div>
    <div class="layui-input-inline">
        <?= $form->field($model, 'area')->dropDownList(ArrayHelper::merge(['' => '请选择区域'],\backend\models\UserInfo::$areaArr)) ?>
    </div>

    <div class="layui-input-inline">
        <?= $form->field($model, 'auto_complete')->dropDownList(ArrayHelper::merge(['' => '是否自主完成'],NewGemba::$autoCompleteList)) ?>
    </div>
    <?= $form->field($model, 'start_date')->input('text',[ 'placeholder'=>"请输入开始时间",'id' => 'startDate']) ?>
    <?= $form->field($model, 'end_date')->input('text',[ 'placeholder'=>"请输入结束时间",'id' => 'endDate']) ?>

    <?= Html::submitButton('<i class="layui-icon"></i>查询', ['class' => 'layui-btn']) ?>

    <?= Html::a('<i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>刷新','mapping-index', ['class' => 'layui-btn']) ?>

    <?php SearchActiveForm::end(); ?>

</div>
<?php
$js =<<<JS
    layui.use('laydate',function() {
        var laydate = layui.laydate;
            //年选择器
            laydate.render({
                elem: '#startDate' 
            });
            
            //年月选择器
            laydate.render({
                elem: '#endDate' 
            });
    });

JS;
$this->registerJs($js);
?>