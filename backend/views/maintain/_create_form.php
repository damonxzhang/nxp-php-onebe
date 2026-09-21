<?php

use yii\helpers\Html;
use backend\models\Maintain;
use yii\helpers\ArrayHelper;
use backend\models\UserInfo;
use backend\models\MaintainType;
use backend\widgets\LayuiActiveForm;
use backend\widgets\SearchMachineType;

/* @var $this yii\web\View */
/* @var $model backend\models\Maintain */
/* @var $form yii\widgets\ActiveForm */
?>

    <div class="maintain-form">

    <?php $form = LayuiActiveForm::begin(); ?>

        <?= $form->field($model, 'number')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'happen_date')->textInput(['value' => date('Y-m-d'),'readonly' => 'readonly']) ?>

        <?= $form->field($model, 'operation_user')->textInput(['value' => (new UserInfo())->getUserName($userInfo->id),'disabled' => '']) ?>

        <?= $form->field($model, 'shift')->textInput(['value' => UserInfo::$shiftArr[$userInfo->shift],'disabled' => '']) ?>

        <?= $form->field($model, 'director_user')->textInput(['value' => (new UserInfo())->getUserName($userInfo->leader_uid),'disabled' => '']) ?>

        <?= $form->field($model, 'type')->dropDownList(ArrayHelper::merge(['' => '请选择问题类型'],Maintain::$problem),['lay-filter'=>"type"]) ?>

        <div class="field-maintain-other_things other_things" style="<?= $model->type == Maintain::OTHER_PROBLEM_TYPE ?'':'display: none' ?>">
        <div class="layui-form-item">
            <label for="tel" class="layui-form-label">其他问题:</label>
            <div class="layui-input-inline" style="width: 800px;">
                <?= Html::input('text',Html::getInputName($model,'other_type'),$model->other_type,['class' => 'layui-input'])?>
            </div>
        </div>
    </div>

        <?= Html::hiddenInput(Html::getInputName($model,'f_id'), $model->f_id,['class' => 'f_id' ])?>
        <div class="form-group field-maintain-process">
        <div class="layui-form-item">
            <label for="tel" class="layui-form-label">设备编号:</label>
            <div class="layui-input-inline" style="width: 800px;">
                <input type="text" id="facility-number" class="layui-input" placeholder="请输入设备编号" value="<?= !empty($facility) ?$facility['number']:'' ?>" maxlength="255">
            </div>
            <span style="color: red">*如手动填写，请以回车按钮结束</span>
        </div>
    </div>
    <span id="material" >
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>问题物料号:
            </label>
            <div class="layui-input-inline " >
                <input type="text" name="material_number[0]"  autocomplete="off" class="layui-input" >
            </div>
            <div class="layui-input-inline" >
                <a href="javascript:;" class="layui-btn layui-btn-warm add-material" data-type="1" >增加</a>
            </div>
        </div>
    </span>
        <?= SearchMachineType::widget([
            'name' => 'SearchMachine',
            'columnName' => 'things',
            'text' => '异常类型',
            'required' => true,
            'value' => !empty($model->things)?$model->things:'',
            'options' => [
                'multiple' => false,
                'style' => ['width'=> '800px'],
            ],
        ]) ?>
        <?= $form->field($model, 'description')->textarea(['placeholder' => '问题描述','class' => 'layui-textarea content' ]) ?>

        <div class="field-maintain-other_things things" style="<?= $model->things == MaintainType::OTHER_TYPE ?'':'display: none'  ?>">
        <div class="layui-form-item">
            <label for="tel" class="layui-form-label">其他问题事项:</label>
            <div class="layui-input-inline" style="width: 800px;">
                <?= Html::input('text',Html::getInputName($model,'other_things'),$model->other_things,['class' => 'layui-input'])?>
            </div>

        </div>
    </div>

    <div class="form-group field-maintain-process">
        <div class="layui-form-item">
            <label for="tel" class="layui-form-label">需反馈人员:</label>
            <div class="layui-input-inline" style="width: 800px;">
                <input type="text" id="maintain-process" placeholder="" class="layui-input feedback-user"  value="<?= !empty($facility) ?$facility['chinese_name']:'' ?>" disabled maxlength="255">
            </div>
        </div>
    </div>
    <div class="form-group field-maintain-process">
        <div class="layui-form-item">
            <label for="tel" class="layui-form-label">其他需反馈人员:</label>
            <div class="layui-input-inline" style="width: 800px;">
                <?= \backend\widgets\SearchUser::widget([
                    'name' =>  Html::getInputName($model,'other_feedback_user'),
                    'url' => '/user-info/search-by-name',
                    'placeholder' => '请输入或选择用户',
                    'options' => ['id' => 'feedback-user' ],
                    'value' => !empty($model->other_feedback_user)?$model->other_feedback_user:''
                ])?>
            </div>
        </div>
    </div>

        <?=\backend\widgets\UploadImg::widget([
            'name' => Html::getInputName($model,'files'),
            'value' => $model->files,
            'options' => ['class' => 'layui-upload' ,'style' => 'margin-left: 80px;']
        ])?>

        <div class="layui-form-item"  style="margin-top: 50px;">
        <label for="" class="layui-form-label">
        </label>
            <?= Html::button('保存', ['class' => 'layui-btn submit']) ?>
    </div>

        <?php LayuiActiveForm::end(); ?>

</div>
<?php
$type = Maintain::OTHER_PROBLEM_TYPE;
$things = MaintainType::OTHER_TYPE;
$js = <<<JS

    var rowNum = 0;
    //物料
    function getInputHtml(num){
        var html = '<label class="layui-form-label">';
        html += '问题物料号:' +
            '</label>' +
            '<div class="layui-input-inline" >' +
            '<input type="text" name="material_number['+num+']"  autocomplete="off" class="layui-input" >' +
            '</div>' ;
        return html;
    }

    $(document).on('click','.add-material',function(){
        rowNum +=1;
        var html = ' <div class="layui-form-item">';
        html += getInputHtml(rowNum);
        html += '<div class="layui-input-inline" >'+
                '<a href="javascript:;" class="layui-btn layui-btn-danger delete-row" data-type="1" >删除</a>'+
                '</div></dib>';
        $('#material').append(html);
    });
    $(document).on('click','.delete-row',function(){
        $(this).parent().parent().remove();
    });
        
    function showOrhide(val,id,className){
         if(val == id){
                $('.'+className).show();
            }else{
                $('.'+className).hide();
            } 
    }

    layui.use('form', function(){
        var form = layui.form;
        
        form.on('select(type)', function(data){ 
            showOrhide(data.value,"{$type}",'other_things')
        }); 
        form.on('select(things)', function(data){
            showOrhide(data.value,"{$things}",'things')
        }); 
    }); 
    $(document).on('keyup','#facility-number',function(event){
         if(event.keyCode==13 || event.keyCode == undefined){
            let number = $(this).val();
            $.post('/facility/get-facility',{number:number},function(res){
                if(res.status == 200){
                    $('.feedback-user').val(res.data.chinese_name);
                    $('.f_id').val(res.data.id);
                }else{
                    layer.msg(res.msg);
                }
            },'json')　  
        }
    }); 
    
    $('.submit').click(function(){
    	 if ($("#maintain-process").val() == ''){
            layer.msg('请输入设备编号并按回车键');
            return false;
        }
        if ($("#feedback-user").val() == ''){
            layer.msg('请选择其他需反馈人员');
            return false;
        }
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
