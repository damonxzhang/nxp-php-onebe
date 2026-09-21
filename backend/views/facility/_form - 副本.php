<?php

use backend\models\MachineVendor;
use yii\helpers\Html;
use backend\widgets\LayuiActiveForm;
use backend\models\UserInfo;
use yii\helpers\ArrayHelper;
use backend\widgets\SearchMachineModel;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model backend\models\Facility */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = LayuiActiveForm::begin(); ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true,'placeholder' => '请输入设备名称']) ?>
    <?= $form->field($model, 'number')->textInput(['maxlength' => true,'placeholder' => '请输入机器编号']) ?>
    <div class="form-group field-facility-cba_pass_date required">
        <div class="layui-form-item">
            <label for="tel" class="layui-form-label">设备型号:</label>
            <div class="layui-input-inline" style="width: 800px;">
                <?= SearchMachineModel::widget([
                    'name'  => Html::getInputName($model, 'model'),
                    'url'   => Url::to('/machine-model/search-by-name'),
                    'value' => $model->model
                ])?>
            </div>
            <div class="layui-col-md4" style="color:red"><div class="help-block"></div></div>
        </div>
    </div>
    <?= $form->field($model, 'process')->dropDownList(ArrayHelper::merge(['' => '请选择工序'],\backend\models\Facility::$processList)) ?>
    <?= $form->field($model, 'area')->dropDownList(ArrayHelper::merge(['' => '请选择区域'], UserInfo::$areaArr)) ?>
    <?= $form->field($model, 'serial_no')->textInput(['maxlength' => true,'placeholder' => '请输入serial_no']) ?>
    <?= $form->field($model, 'vendor_id')->dropDownList(ArrayHelper::merge(['' => '请选择VENDOR'],MachineVendor::VendorArr())) ?>
    <?= $form->field($model, 'ASSET_NO')->textInput(['maxlength' => true,'placeholder' => 'ASSET_NO']) ?>
    <?= $form->field($model, 'BUY_OFF_DATE')->textInput(['maxlength' => true,'id' => 'buyOffDate','placeholder' => '请选择时间']) ?>
    <?= $form->field($model, 'CBA_pass_date')->textInput(['maxlength' => true,'id' => 'CBA_pass_date','placeholder' => '请选择时间']) ?>
    <div class="form-group field-facility-cba_pass_date required">
        <div class="layui-form-item">
            <label for="tel" class="layui-form-label">负责人:</label>
            <div class="layui-input-inline" style="width: 800px;">
                <?= \backend\widgets\SearchUser::widget([
                    'name' => Html::getInputName($model, 'head_user'),
                    'url' => \yii\helpers\Url::to('/user-info/search-by-name'),
                    'value' => $model->head_user
                ])?>
            </div>
            <div class="layui-col-md4" style="color:red"><div class="help-block"></div></div>
        </div>
    </div>
    <?= $form->field($model, 'status')->dropDownList( $model::$statusList ) ?>
    <?= $form->field($model, 'is_ok')->dropDownList( $model::$selectList) ?>
    <div class="layui-form-item"  style="margin-top: 15px;">
        <label for="" class="layui-form-label">
        </label>
        <?= Html::button('提交', ['class' => 'layui-btn submit']) ?>
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
    
    layui.use('laydate', function() {
        var laydate = layui.laydate; 
        laydate.render({
            elem: '#buyOffDate' //指定元素
        }); 
        laydate.render({
            elem: '#CBA_pass_date' //指定元素
        });
    });
JS;
$this->registerJs($js);