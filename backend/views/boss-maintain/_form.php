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

        <?= $form->field($model, 'number')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'create_time')->textInput(['value' => date('Y-m-d H:i:s'),'readonly' => '']) ?>

        <?= $form->field($model, 'create_user')->textInput(['value' => (new UserInfo())->getUserName($userInfo->id),'disabled' => '']) ?>

        <?= $form->field($model, 'shift')->textInput(['value' => UserInfo::$shiftArr[$userInfo->shift],'disabled' => '']) ?>

        <?= Html::hiddenInput(Html::getInputName($model,'f_id'), $model->f_id,['class' => 'f_id' ])?>
        <div class="form-group field-maintain-process">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">设备编号:</label>
                <div class="layui-input-inline" style="width: 800px;">
                    <?=Html::hiddenInput(Html::getInputName($model,'feedback_user'))?>
                    <input type="text" id="facility-number" class="layui-input" placeholder="请输入设备编号" value="<?= !empty($facility) ?$facility['number']:'' ?>" maxlength="255">
                </div>
                <span style="color: red">*如手动填写，请以回车按钮结束</span>
            </div>
        </div>

        <?= $form->field($model, 'head_user',['required' => true])->dropDownList(ArrayHelper::merge(['' => '请选择主管带班'],UserInfo::getHeaderUserList())) ?>
       
        <?= $form->field($model, 'location_num')->textInput( ['class' => 'layui-input','placeholder' => '请输入位置编号  ']) ?>

        <?= $form->field($model, 'things')->textarea( ['class' => 'layui-textarea']) ?>

        <div class="form-group field-maintain-process">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">设备反馈人员:</label>
                <div class="layui-input-inline" style="width: 800px;">
                    <input type="text" id="maintain-process" placeholder="" class="layui-input feedback-user"  value="<?= !empty($facility) ?$facility['chinese_name']:'' ?>" disabled maxlength="255">
                </div>
            </div>
        </div>

        <div class="form-group field-maintain-process">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">生产反馈人员:</label>
                <div class="layui-input-inline" style="width: 800px;">
                    <input type="text" id="maintain-process" placeholder="" class="layui-input leader-name"  value="<?= !empty($facility) ?$facility['chinese_name']:'' ?>" disabled maxlength="255">
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
            <?= Html::button('提交', ['class' => 'layui-btn submit']) ?>
        </div>

        <?php LayuiActiveForm::end(); ?>

    </div>
<?php
$type = Maintain::OTHER_PROBLEM_TYPE;
$things = MaintainType::OTHER_TYPE;
$js = <<<JS

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
            
            if(number){
                $.post('get-auto-input',{number:number},function(res){
                if(res.status == 200){
                    console.log(res);
                    $('.feedback-user').val(res.data.chinese_name+','+res.data.leaderName);
                    $('.leader-name').val(res.data.leaderNames);
                    $('.head_user').val(res.data.leader_uid);
                    $('.f_id').val(res.data.id);
                }else{
                    layer.msg(res.msg);
                }
            },'json')　  
            }else{
                $('.feedback-user').val('');
                $('.leader-name').val('');
                $('.head_user').val('');
                $('.f_id').val(''); 
            }
            
        }
    }); 
    
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