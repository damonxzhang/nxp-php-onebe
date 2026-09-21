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
<style>
    .disabled-input{
        border:0px !important;
    }
</style>
<div class="x-body">
    <div class="maintain-form">
        <div class="form-group field-maintain-process">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">编号:</label>
                <div class="layui-input-inline" style="width: 800px;">
                    <?=Html::input('text','',$model->number,['class' => 'layui-input','readonly' => 'readonly'])?>
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
                    <?=Html::input('text','material_number',$model->material_number,['class' => 'layui-input ','readonly' => 'readonly'])?>
                </div>
            </div>
        </div>

        <div class="form-group field-maintain-process">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">异常类型:</label>
                <div class="layui-input-inline" style="width: 800px;">
                    <?=Html::input('text','',( ArrayHelper::getValue(MaintainType::getTypeList(),$model->things,'')),['class' => 'layui-input ','readonly' => 'readonly'])?>
                </div>
            </div>
        </div>
        <div class="form-group field-maintain-process">
            <label for="desc" class="layui-form-label">问题描述:</label>
            <div class="layui-input-block">
                <?=Html::textarea('description', $model->description ,['class' => 'layui-textarea ','readonly' => 'readonly', 'style'=>"width: 800px;height: 100px;"])?>
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

        <div class="form-group field-maintain-process">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">需反馈人员:</label>
                <div class="layui-input-inline" style="width: 800px;">
                    <?=Html::input('text','',$facility['chinese_name'],['class' => 'layui-input ','readonly' => 'readonly'])?>
                </div>
                </div>
            </div>
        </div>
        <div class="form-group field-maintain-process">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">其他需反馈人员:</label>
                <div class="layui-input-inline" style="width: 800px;">
                    <input type="text" id="maintain-process" class="layui-input feedback-user" value="<?= !empty($model->other_feedback_user)? (new UserInfo())->getUserName($model->other_feedback_user):''   ?>" disabled maxlength="255">
                </div>
            </div>
        </div>
        <div class="form-group field-maintain-process">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">问题类型:</label>
                <div class="layui-input-inline" style="width: 800px;">
                    <?=Html::input('text','',Maintain::$problem[$model->type],['class' => 'layui-input ','readonly' => 'readonly'])?>
                </div>
            </div>
        </div>
        <div class="field-maintain-other_things other_things" style="<?= $model->type == Maintain::OTHER_PROBLEM_TYPE ?'':'display: none' ?>">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">其他类型:</label>
                <div class="layui-input-inline" style="width: 800px;">
                    <?=Html::input('text','',$model->other_type,['class' => 'layui-input ','readonly' => 'readonly'])?>
                </div>

            </div>
        </div>
        <div class="field-maintain-other_things other_things" >
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">次品数量:</label>
                <div class="layui-input-inline" >
                    <?=Html::input('text','',$model->rejects,['class' => 'layui-input disabled-input','readonly' => 'readonly'])?>

                </div>
                <?=Html::a('填写','javascript:;',['class' => 'layui-btn','onclick' =>'x_admin_show("次品数量","/maintain/rejects?id='.$model->id.'")' ])?>
            </div>
        </div>
    <?php $form = LayuiActiveForm::begin(); ?>


    <?= $form->field($model, 'access_status')->dropDownList( Maintain::$accessStatusList,['lay-filter'=>"things"]) ?>

        <div class="layui-form-item"  style="margin-top: 50px;">
            <label for="" class="layui-form-label">
            </label>
            <?= Html::button('保存', ['class' => 'layui-btn submit']) ?>
        </div>

        <?php LayuiActiveForm::end(); ?>

    </div>
</div>
<?php
$accessStatusName = Html::getInputName($model,'access_status');
$js = <<<JS
 
    $('.submit').click(function(){
        let form = $(this).parents('form').serialize(); 
        $.post('', form ,function(res) {
            if(res.status == 200){
                 layer.msg('成功', {icon: 1,time: 1500}, function () {
                    var index = parent.layer.getFrameIndex(window.name);
                    parent.layer.close(index);
                });
                //window.location.href='index';
            }else{
                layer.msg(res.msg);
            }
            
        },'json')
    });
JS;
$this->registerJs($js);