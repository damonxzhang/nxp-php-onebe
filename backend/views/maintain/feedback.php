<?php

use yii\helpers\Html;
use backend\models\Maintain;
use yii\helpers\ArrayHelper;
use backend\models\UserInfo;
use backend\widgets\LayuiActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Maintain */
/* @var $form yii\widgets\ActiveForm */
?>
    <div class="x-body">
        <div class="maintain-form">
            <div class="form-group field-maintain-process">
                <div class="layui-form-item">
                    <label for="tel" class="layui-form-label">编号:</label>
                    <div class="layui-input-inline" style="width: 800px;">
                        <?=Html::input('text','',$model->number,['class' => 'layui-input ','readonly' => 'readonly'])?>
                    </div>
                </div>
            </div>
            <div class="form-group field-maintain-process">
                <div class="layui-form-item">
                    <label for="tel" class="layui-form-label">发生时间:</label>
                    <div class="layui-input-inline" style="width: 800px;">
                        <?=Html::input('text','', date('Y-m-d',$model->happen_date),['class' => 'layui-input ','readonly' => 'readonly'])?>
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
                        <?=Html::input('text','',$model->material_number,['class' => 'layui-input ','readonly' => 'readonly'])?>
                    </div>
                </div>
            </div>

            <div class="form-group field-maintain-process">
                <div class="layui-form-item">
                    <label for="tel" class="layui-form-label">问题事项:</label>
                    <div class="layui-input-inline" style="width: 800px;">
                        <?=Html::input('text','',(\backend\models\MaintainType::getTypeList())[$model->things],['class' => 'layui-input ','readonly' => 'readonly'])?>
                    </div>
                </div>
            </div>

            <div class="field-maintain-other_things things" style="<?= $model->things == \backend\models\MaintainType::OTHER_TYPE ?'':'display: none'?>">
                <div class="layui-form-item">
                    <label for="tel" class="layui-form-label">其他问题事项:</label>
                    <div class="layui-input-inline" style="width: 800px;">
                        <?=Html::input('text','',$model->other_things,['class' => 'layui-input ','readonly' => 'readonly'])?>
                    </div>

                </div>
            </div>

            <?=\backend\widgets\UploadImg::widget([
                'name' => Html::getInputName($model,'files'),
                'value' => $model->files,
                'disabled' => true
            ])?>

        <?php $form = LayuiActiveForm::begin(); ?>
            <div class="form-group field-maintain-process">
                <div class="layui-form-item">
                    <label for="tel" class="layui-form-label">需反馈人员:</label>
                    <div class="layui-input-inline" style="width: 800px;">
                        <input type="text" id="maintain-process" class="layui-input feedback-user" value="<?= !empty($facility) ?$facility['chinese_name']:'' ?>" disabled maxlength="255">
                    </div>
                </div>
            </div>

            <?= $form->field($model,'feedback_date')->textInput(['value' => date('Y-m-d'),'readonly' => 'readonly'])?>

            <?= $form->field($model,'feedback_type')->dropDownList(ArrayHelper::merge(['' => '请选择反馈类型'],Maintain::$problem),['lay-filter'=>"feedback_type" ])?>

            <div class="field-maintain-other_things feedback_type" style="<?= $model->feedback_type == \backend\models\MaintainType::OTHER_TYPE ?'':'display: none' ?>">
                <div class="layui-form-item">
                    <label for="tel" class="layui-form-label">其他类型:</label>
                    <div class="layui-input-inline" style="width: 800px;">
                        <?= Html::input('text',Html::getInputName($model,'other_feedback'),$model->other_feedback,['class' => 'layui-input'])?>
                    </div>

                </div>
            </div>

            <?= $form->field($model,'action')->dropDownList(ArrayHelper::merge(['' => '请选择行动内容'],Maintain::$actionList),['lay-filter'=>"action" ])?>

            <div class="field-maintain-other_things action" style="<?= $model->action == Maintain::OTHER_ACTION ?'':'display: none' ?>">
                <div class="layui-form-item">
                    <label for="tel" class="layui-form-label">其他行动:</label>
                    <div class="layui-input-inline" style="width: 800px;">
                        <?= Html::input('text',Html::getInputName($model,'other_action'),$model->other_action,['class' => 'layui-input'])?>
                    </div>
                </div>
            </div>

            <?= $form->field($model,'predict_date')->textInput(['id' => 'predict_date'])?>

            <?= $form->field($model,'complete_date')->textInput(['id' => 'complete_date'])?>

            <?= $form->field($model,'solve_status')->dropDownList(ArrayHelper::merge(['' => '请选择解决状态'],Maintain::$solveStatusList),['lay-filter' => 'solve_status'])?>

            <?= $form->field($model,'rejects')->textInput()?>

            <div class="layui-form-item"  style="margin-top: 50px;">
            <label for="" class="layui-form-label">
            </label>
            <?= Html::button('保存', ['class' => 'layui-btn submit' ]) ?>
        </div>

        <?php LayuiActiveForm::end(); ?>
    </div>
<?php
$accessStatusName = Html::getInputName($model,'access_status');
$type = Maintain::OTHER_PROBLEM_TYPE;
$action = Maintain::OTHER_ACTION;
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
            $('#maintainservice-solve_status').find('option').each(function(){
               if($(this).val() == 1){ 
                   $(this).attr('selected',true);
                   form.render('select'); 
               }
            }); 
        }
    
        //重置input
        function closeInput(){
            $('#maintainservice-solve_status').find('option:selected').each(function(){ 
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
    }); 
 
    $('.submit').click(function(){
        let form = $(this).parents('form').serialize();
        form += '&{$accessStatusName}='+$(this).data('status'); 
        $.post('', form ,function(res) {
            if(res.status == 200){
                layer.msg('成功', {icon: 1,time: 1500}, function () {
                    var index = parent.layer.getFrameIndex(window.name);
                    parent.layer.close(index);
                });
               // window.location.href='index';
            }else{
                layer.msg(res.msg);
            }
            
        },'json');
    });

JS;
$this->registerJs($js);