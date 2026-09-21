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
                        <?=Html::input('text','',strtoupper($model->material_number),['class' => 'layui-input ','readonly' => 'readonly'])?>
                    </div>
                </div>
            </div>

           <div class="form-group field-maintain-process">
                <div class="layui-form-item">
                    <label for="tel" class="layui-form-label">异常类型:</label>
                    <div class="layui-input-inline" style="width: 800px;">
                        <?=Html::input('text','',ArrayHelper::getValue($typeList, $model->things,''),['class' => 'layui-input ','readonly' => 'readonly'])?>
                    </div>
                </div>
            </div>
            <div class="form-group field-maintain-process">
                <label for="desc" class="layui-form-label">问题描述:</label>
                <div class="layui-input-block">
                    <?=Html::textarea('description', $model->description ,['class' => 'layui-textarea ','readonly' => 'readonly', 'style'=>"width: 800px;height: 100px;"])?>
                </div>
            </div>

            <div class=" things" style="<?= $model->things == \backend\models\MaintainType::OTHER_TYPE ?'':'display: none'?>">
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
                'disabled' => true,
                'options' => ['class' => 'layui-upload','style' => 'margin-left: 110px;']
            ])?>
            <div class="form-group field-maintain-process">
                <div class="layui-form-item">
                    <label for="tel" class="layui-form-label">需反馈人员:</label>
                    <div class="layui-input-inline" style="width: 800px;">
                        <input type="text" id="maintain-process" class="layui-input feedback-user" value="<?= !empty($model->feedback_user)? (new UserInfo())->getUserName($model->feedback_user):''   ?>" disabled maxlength="255">
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
            <div class=" things" >
                <div class="layui-form-item">
                    <label for="tel" class="layui-form-label">次品数量:</label>
                    <div class="layui-input-inline" style="width: 800px;">
                        <?=Html::input('text','',$model->rejects,['class' => 'layui-input ','readonly' => 'readonly'])?>
                    </div>

                </div>
            </div>
            <div class="form-group field-maintain-process">
                <label for="desc" class="layui-form-label"></label>
                <div class="layui-input-block">
                   <?= Html::a('反馈列表', ['/maintain-feedback/index','id' => $model->id],['class' => 'layui-btn layui-btn-normal' ]) ?>
                </div>
            </div>
            
        </div>
    </div>
<?php

$js = <<<JS
 
JS;
$this->registerJs($js);