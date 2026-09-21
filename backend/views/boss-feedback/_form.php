<?php

use yii\helpers\Html;
use backend\models\Maintain;
use yii\helpers\ArrayHelper;
use backend\models\UserInfo;
use backend\widgets\LayuiActiveForm;
use backend\models\MaintainFeedback;

/* @var $this yii\web\View */
/* @var $model backend\models\Maintain */
/* @var $form yii\widgets\ActiveForm */
?>

    <div class="maintain-form">
        <!-- <div class="form-group field-maintain-process">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">编号:</label>
                <div class="layui-input-inline" style="width: 800px;">
                    <?=Html::input('text','',$maintainModel->number,['class' => 'layui-input ','readonly' => 'readonly'])?>
                </div>
            </div>
        </div> -->
        <div class="form-group field-maintain-process">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">发生时间:</label>
                <div class="layui-input-inline" style="width: 800px;">
                    <?=Html::input('text','', date('Y-m-d',$maintainModel->create_time),['class' => 'layui-input ','readonly' => 'readonly'])?>
                </div>
            </div>
        </div>
        <div class="form-group field-maintain-process">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">创建人员:</label>
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
        <!-- <div class="form-group field-maintain-process">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">主管带班:</label>
                <div class="layui-input-inline" style="width: 800px;">
                    <?=Html::input('text','',(new UserInfo())->getUserName($userInfo->leader_uid),['class' => 'layui-input ','readonly' => 'readonly'])?>
                </div>
            </div>
        </div> -->

        <div class="form-group field-maintain-process">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">设备编号:</label>
                <div class="layui-input-inline" style="width: 800px;">  
                    <?=Html::input('text','',!empty($maintainModel->facility->number)?$maintainModel->facility->number:'',['class' => 'layui-input ','readonly' => 'readonly'])?>
                </div>
            </div>
        </div>

        <div class="form-group field-maintain-process">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">区域:</label>
                <div class="layui-input-inline" style="width: 800px;"> 
                    <?=Html::input('text','',(!empty($maintainModel->area)? UserInfo::$areaArr[$maintainModel->area]:""),['class' => 'layui-input ','readonly' => 'readonly'])?>
                </div>
            </div>
        </div>

        <div class="form-group field-maintain-process">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">工序:</label>
                <div class="layui-input-inline" style="width: 800px;">  
                    <?=Html::input('text','',!empty($maintainModel->process)?(\backend\models\Facility::$processList[$maintainModel->process]):(!empty($maintainModel->facility->process)?(\backend\models\Facility::$processList[$maintainModel->facility->process]):""),['class' => 'layui-input ','readonly' => 'readonly'])?>
                </div>
            </div>
        </div>

        <div class="form-group field-maintain-process">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">问题描述:</label>
                <div class="layui-input-inline" style="width: 800px;">
                    <?=Html::input('text','',$maintainModel->things,['class' => 'layui-input ','readonly' => 'readonly'])?>
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

        <?php $form = LayuiActiveForm::begin(); ?>

        <?= $form->field($feedbackModel,'create_time')->textInput(['readonly' => 'readonly'])?>

        <!-- <?= $form->field($feedbackModel,'action')->dropDownList(ArrayHelper::merge(['' => '请选择行动内容'], MaintainFeedback::$actionList),['lay-filter'=>"action"])?> -->
        <div style="display: none;">
        <?= $form->field($feedbackModel,'action')->textInput(['readonly' => 'readonly','value'=>MaintainFeedback::OTHER_ACTION])?>
        </div>
        <!-- <div class="field-maintain-other_things action" style="<?= $feedbackModel->action == MaintainFeedback::OTHER_ACTION ?'':'display: none' ?>"> -->
        <div class="field-maintain-other_things action">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">改善行动:</label>
                <div class="layui-input-inline" style="width: 800px;">
                    <?= Html::input('text',Html::getInputName($feedbackModel,'other_action'), $feedbackModel->other_action,['class' => 'layui-input'])?>
                </div>
            </div>
        </div>

        <?= $form->field($feedbackModel,'predict_date')->textInput(['id' => 'predict_date'])?>

        <?= $form->field($feedbackModel,'complete_date')->textInput(['id' => 'complete_date'])?>

        <?= $form->field($feedbackModel,'solve_status')->dropDownList(ArrayHelper::merge(['' => '请选择解决状态'], MaintainFeedback::$solveStatusList),['lay-filter' => 'solve_status'])?>

        <!-- <?= $form->field($feedbackModel,'rejects')->textInput()?> -->

        <?=\backend\widgets\MTFindingUploadImg::widget([
            'name' => Html::getInputName($feedbackModel,'files'),
            'value' => $feedbackModel->files,
            'options' => ['class' => 'layui-upload' ,'style' => 'margin-left: 80px;']
        ])?> 

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
$action = MaintainFeedback::OTHER_ACTION;
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
        let solve_status = $('#bossfeedback-solve_status');
        //初始input
        function editInput(){
            solve_status.find('option').each(function(){
               if($(this).val() == 1){ 
                   $(this).attr('selected',true);
                   form.render('select'); 
                       console.log($(this));
               }
            }); 
        }
    
        //重置input
        function closeInput(){
           solve_status.find('option:selected').each(function(){ 
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
                let solveStatus =document.getElementById("bossfeedback-solve_status").value;
                if(solveStatus==1){
                    JsonpSubmint();
                }else{
                    window.location.href='/boss-maintain/index';
                }
                // window.location.href='/boss-maintain/index';
            }else{
                layer.msg(res.msg);
            }
            
        },'json');
    });

    function JsonpSubmint(){
        
        var JSONP = document.createElement("script") ;
        //FF:onload IE:onreadystatechange
        JSONP.onload = JSONP.onreadystatechange = function(){
            //onreadystatechange，仅IE
            if (!this.readyState || this.readyState === "loaded" || this.readyState === "complete") {
                // alert($("#demo").html());
                JSONP.onload = JSONP.onreadystatechange = null//请内存，防止IE memory leaks
                window.location.href='/boss-maintain/index';
            }
        }
        JSONP.type = "text/javascript";
        //console.log("$maintainModel->id")//[nxp_boss_maintain]的id
        JSONP.src = "http://10.193.226.115:8080/boss-finding/submit/feedback?id=$maintainModel->id";
        //在head之后添加js文件
        document.getElementsByTagName("head")[0].appendChild(JSONP);
        
    }

JS;
$this->registerJs($js);