<?php

use yii\helpers\Html;
use backend\models\Maintain;
use yii\helpers\ArrayHelper;
use backend\models\UserInfo;
use backend\widgets\LayuiActiveForm;
use backend\models\MaintainType;
/* @var $this yii\web\View */
/* @var $model backend\models\Maintain */
/* @var $form yii\widgets\ActiveForm */
?>

    <div class="maintain-form">
        <div class="form-group field-maintain-process">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">编号:</label>
                <div class="layui-input-inline" style="width: 800px;">
                    <?=Html::input('text','',$maintainModel->number,['class' => 'layui-input ','readonly' => 'readonly'])?>
                </div>
            </div>
        </div>
        <div class="form-group field-maintain-process">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">发生时间:</label>
                <div class="layui-input-inline" style="width: 800px;">
                    <?=Html::input('text','', date('Y-m-d',$maintainModel->happen_date),['class' => 'layui-input ','readonly' => 'readonly'])?>
                </div>
            </div>
        </div>
        <div class="form-group field-maintain-process">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">操作人员:</label>
                <div class="layui-input-inline" style="width: 800px;">
                    <?=Html::input('text','',(new UserInfo())->getUserName($userInfo->id),['class' => 'layui-input ','readonly' => 'readonly'])?>
                </div>
            </div>
        </div>
        <div class="form-group field-maintain-process">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">班别:</label>
                <div class="layui-input-inline" style="width: 800px;">
                    <?=Html::input('text','',UserInfo::$shiftArr[$userInfo->shift],['class' => 'layui-input ','readonly' => 'readonly'])?>
                </div>
            </div>
        </div>
        <div class="form-group field-maintain-process">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">主管带班:</label>
                <div class="layui-input-inline" style="width: 800px;">
                    <?=Html::input('text','',(new UserInfo())->getUserName($userInfo->leader_uid),['class' => 'layui-input ','readonly' => 'readonly'])?>
                </div>
            </div>
        </div>

        <div class="form-group field-maintain-process">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">设备编号:</label>
                <div class="layui-input-inline" style="width: 800px;">
                    <?=Html::input('text','',$facility['number'],['class' => 'layui-input ','readonly' => 'readonly'])?>
                </div>
            </div>
        </div>

        <div class="form-group field-maintain-process">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">问题物料号:</label>
                <div class="layui-input-inline" style="width: 800px;">
                    <?=Html::input('text','',$maintainModel->material_number,['class' => 'layui-input ','readonly' => 'readonly'])?>
                </div>
            </div>
        </div>

        <div class="form-group field-maintain-process">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">异常类型:</label>
                <div class="layui-input-inline" style="width: 800px;">
                    <?=Html::input('text','',( ArrayHelper::getValue(MaintainType::getTypeList(),$maintainModel->things,'')),['class' => 'layui-input ','readonly' => 'readonly'])?>
                </div>
            </div>
        </div>
        <div class="form-group field-maintain-process">
            <label for="desc" class="layui-form-label">问题描述:</label>
            <div class="layui-input-block">
                <?=Html::textarea('description', $maintainModel->description ,['class' => 'layui-textarea ','readonly' => 'readonly', 'style'=>"width: 800px;height: 100px;"])?>
            </div>
        </div>

        <div class="field-maintain-other_things things" style="<?= $maintainModel->things == \backend\models\MaintainType::OTHER_TYPE ?'':'display: none'?>">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">其他问题事项:</label>
                <div class="layui-input-inline" style="width: 800px;">
                    <?=Html::input('text','',$maintainModel->other_things,['class' => 'layui-input ','readonly' => 'readonly'])?>
                </div>

            </div>
        </div>

        <?=\backend\widgets\UploadImg::widget([
            'name' => Html::getInputName($maintainModel,'files'),
            'value' => $maintainModel->files,
            'disabled' => true,
            'options' => ['class' => 'layui-upload','style' => 'margin-left: 110px;']
        ])?>

        <div class="form-group field-maintain-process">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">需反馈人员:</label>
                <div class="layui-input-inline" style="width: 800px;">
                    <input type="text" id="maintain-process" class="layui-input feedback-user" value="<?= !empty($maintainModel['feedback_user'])? (new UserInfo())->getUserName($maintainModel['feedback_user']):''   ?>" disabled maxlength="255">
                </div>
            </div>
        </div>
        <div class="form-group field-maintain-process">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">其他需反馈人员:</label>
                <div class="layui-input-inline" style="width: 800px;">
                    <input type="text" id="maintain-process" class="layui-input feedback-user" value="<?= !empty($maintainModel['other_feedback_user'])? (new UserInfo())->getUserName($maintainModel['other_feedback_user']):''   ?>" disabled maxlength="255">
                </div>
            </div>
        </div>
        <?php $form = LayuiActiveForm::begin(); ?>

        <?= $form->field($feedbackModel,'create_time')->textInput(['readonly' => 'readonly'])?>

        <?= $form->field($feedbackModel,'feedback_type')->dropDownList(ArrayHelper::merge(['' => '请选择反馈类型'],Maintain::$problem),['lay-filter'=>"feedback_type" ])?>

        <div class="field-maintain-other_things feedback_type" style="<?= $feedbackModel->feedback_type == Maintain::OTHER_PROBLEM_TYPE ?'':'display: none' ?>">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">其他类型:</label>
                <div class="layui-input-inline" style="width: 800px;">
                    <?= Html::input('text',Html::getInputName($feedbackModel,'other_feedback'),$feedbackModel->other_feedback,['class' => 'layui-input'])?>
                </div>

            </div>
        </div>
        <?= $form->field($feedbackModel,'supplement')->textarea( ['class' => 'layui-textarea ','style'=>"width: 800px;height: 100px;"])?>
       
        <?= $form->field($feedbackModel,'solve_status')->dropDownList(ArrayHelper::merge(['' => '请选择解决状态'],\backend\models\MaintainFeedback::$solveStatusList),['lay-filter' => 'solve_status','id' => 'solve_status'])?>

        <?= $form->field($feedbackModel,'predict_date')->textInput(['id' => 'predict_date'])?>

        <?= $form->field($feedbackModel,'complete_date')->textInput(['id' => 'complete_date'])?>

        <?= $form->field($feedbackModel,'rejects')->textInput()?>

        <div class="layui-form-item"  style="margin-top: 50px;">
            <label for="" class="layui-form-label">
            </label>
            <?= Html::button('保存', ['class' => 'layui-btn submit' ]) ?>
        </div>

        <?php LayuiActiveForm::end(); ?>
    </div>
<?php
$accessStatusName = Html::getInputName($feedbackModel,'access_status');
$type = Maintain::OTHER_PROBLEM_TYPE;
$action = $feedbackModel::OTHER_ACTION;
$js = <<<JS
    function showOrhide(val,id,className){
        if(val == id){
            $('.'+className).show();
        }else{
            $('.'+className).hide();
        } 
    }
 
    layui.use('laydate', function(){
        var laydate = layui.laydate; 
        laydate.render({
            elem: '#predict_date'
        });//执行一个laydate实例
        laydate.render({
            elem: '#complete_date'
        });
    });
 
    layui.use('form', function(){
        var form = layui.form;
        
        //初始input
        function editInput(){
            $('#maintainfeedback-solve_status').find('option').each(function(){
               if($(this).val() == 1){ 
                   $(this).attr('selected',true);
                   form.render('select'); 
                       console.log($(this));
               }
            }); 
        }
    
        //重置input
        function closeInput(){
            $('#maintainfeedback-solve_status').find('option:selected').each(function(){ 
                $(this).attr('selected',false);
                form.render('select');  
            }); 
        }
        
        form.on('select(feedback_type)', function(data){ 
            showOrhide(data.value,"{$type}",'feedback_type')
        }); 
        form.on('select(action)', function(data){
        
            if(data.value == 1 || data.value == 2){
                editInput();
            }else{
                closeInput();
            } 
            showOrhide(data.value,"{$action}",'action')
        });
		 form.on('select(solve_status)', function(data){
            if(data.value == 2){
                $("#predict_date").val("");
                $("#complete_date").val("");
            }else{
                var myDate = new Date;
                var year = myDate.getFullYear(); //获取当前年
                var mon  = myDate.getMonth() + 1; //获取当前月
                var date = myDate.getDate(); //获取当前日
                var dateTime = year + "-" + mon + "-" + date;
                if($('#predict_date').val()==''){
                    $("#predict_date").attr("value",dateTime);
                }
                if($('#complete_date').val()==''){
                   $("#complete_date").attr("value",dateTime);
                }
            }
        });
    }); 
 
    $('.submit').click(function(){
        if($('#solve_status').val() == 2 && $('#predict_date').val()==''){
           layer.msg('预计完成时间不能为空!');
        }else{
            let form = $(this).parents('form').serialize();
            form += '&{$accessStatusName}='+$(this).data('status');
            $.post('', form ,function(res) {
                if(res.status == 200){
                    layer.msg('成功', {icon: 1,time: 1500}, function () {
                        var index = parent.layer.getFrameIndex(window.name);
                        parent.layer.close(index);
                    });
                   // window.location.href='/maintain/index';
                }else{
                    layer.msg(res.msg);
                }
                
            },'json');
        }
    });

JS;
$this->registerJs($js);