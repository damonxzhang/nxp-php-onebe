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
                        <?=Html::input('text','', $userInfo->chinese_name,['class' => 'layui-input ','readonly' => 'readonly'])?>
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
                        <?=Html::input('text','',!empty($facility['number']) ? $facility['number']:'',['class' => 'layui-input ','readonly' => 'readonly'])?>
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
                    <label for="tel" class="layui-form-label">问题事项:</label>
                    <div class="layui-input-inline" style="width: 800px;">
                        <?=Html::input('text','',ArrayHelper::getValue($typeList,$maintainModel->things),['class' => 'layui-input ','readonly' => 'readonly'])?>
                    </div>
                </div>
            </div>

            <div class=" things" style="<?= $maintainModel->things == \backend\models\MaintainType::OTHER_TYPE ?'':'display: none'?>">
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
            <div class="form-group field-maintainfeedback-create_time">
                <div class="layui-form-item">
                    <label for="tel" class="layui-form-label">反馈时间:</label>
                    <div class="layui-input-inline" style="width: 800px;">
                        <?=Html::input('text','',date('Y-m-d',$model->create_time),['class' => 'layui-input ','readonly' => 'readonly'])?>
                    </div>
                    <div class="layui-col-md4" style="color:red"><div class="help-block"></div></div>
                </div>
            </div>

            <div class="form-group field-maintainfeedback-create_time">
                <div class="layui-form-item">
                    <label for="tel" class="layui-form-label">反馈类型:</label>
                    <div class="layui-input-inline" style="width: 800px;">
                        <?=Html::input('text','',Maintain::$problem[$model->feedback_type] , ['class' => 'layui-input ','readonly' => 'readonly'])?>
                    </div>
                    <div class="layui-col-md4" style="color:red"><div class="help-block"></div></div>
                </div>
            </div>
            <div class="field-maintain-other_things feedback_type" style="<?= $model->feedback_type == \backend\models\MaintainType::OTHER_TYPE ?'':'display: none' ?>">
                <div class="layui-form-item">
                    <label for="tel" class="layui-form-label">其他类型:</label>
                    <div class="layui-input-inline" style="width: 800px;">
                        <?= Html::input('text', 'other_feedback' ,$model->other_feedback,['class' => 'layui-input','readonly' => 'readonly'])?>
                    </div>

                </div>
            </div>

            <div class="field-maintain-other_things feedback_type">
                <div class="layui-form-item">
                    <label for="tel" class="layui-form-label">行动内容补充说明:</label>
                    <div class="layui-input-inline" style="width: 800px;">
                        <?= Html::textarea('supplement',$model->supplement,['class' => 'layui-textarea content','readonly' => 'readonly']) ?>
                    </div>
                </div>
            </div>
            <div class="field-maintain-other_things feedback_type">
                <div class="layui-form-item">
                    <label for="tel" class="layui-form-label">解决状态:</label>
                    <div class="layui-input-inline" style="width: 800px;">
                        <?= Html::input('text','solve_status',\backend\models\MaintainFeedback::$solveStatusList[$model->solve_status],['class' => 'layui-input','readonly' => 'readonly'])?>
                    </div>
                </div>
            </div>

            <div class="field-maintain-other_things feedback_type">
                <div class="layui-form-item">
                    <label for="tel" class="layui-form-label">预计完成时间:</label>
                    <div class="layui-input-inline" style="width: 800px;">
                        <?= Html::input('text', 'predict_date' , date('Y-m-d',$model->predict_date),['class' => 'layui-input','readonly' => 'readonly'])?>
                    </div>
                </div>
            </div>
            <div class="field-maintain-other_things feedback_type">
                <div class="layui-form-item">
                    <label for="tel" class="layui-form-label">实际完成时间:</label>
                    <div class="layui-input-inline" style="width: 800px;">
                        <?= Html::input('text','predict_date',!empty($model->complete_date) ? date('Y-m-d',$model->complete_date):'',['class' => 'layui-input','readonly' => 'readonly'])?>
                    </div>
                </div>
            </div>

            <div class=" things" >
                <div class="layui-form-item">
                    <label for="tel" class="layui-form-label">次品数量:</label>
                    <div class="layui-input-inline" style="width: 800px;">
                        <?=Html::input('text','',$maintainModel->rejects,['class' => 'layui-input ','readonly' => 'readonly'])?>
                    </div>

                </div>
            </div>
        </div>
    </div>
