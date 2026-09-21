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
                        <?=Html::input('text','', date('Y-m-d',$model->create_time),['class' => 'layui-input ','readonly' => 'readonly'])?>
                    </div>
                </div>
            </div>
            <div class="form-group field-maintain-process">
                <div class="layui-form-item">
                    <label for="tel" class="layui-form-label">创建人员:</label>
                    <div class="layui-input-inline" style="width: 800px;">
                        <?=Html::input('text','',(new UserInfo())->getUserName($model->userInfo->id),['class' => 'layui-input ','readonly' => 'readonly'])?>
                    </div>
                </div>
            </div>
            <div class="form-group field-maintain-process">
                <div class="layui-form-item">
                    <label for="tel" class="layui-form-label">班别:</label>
                    <div class="layui-input-inline" style="width: 800px;">
                        <?=Html::input('text','',UserInfo::$shiftArr[$model->userInfo->shift],['class' => 'layui-input ','readonly' => 'readonly'])?>
                    </div>
                </div>
            </div>
            <!-- <div class="form-group field-maintain-process">
                <div class="layui-form-item">
                    <label for="tel" class="layui-form-label">主管带班:</label>
                    <div class="layui-input-inline" style="width: 800px;">
                        <?=Html::input('text','',(new UserInfo())->getUserName($model->head_user),['class' => 'layui-input ','readonly' => 'readonly'])?>
                    </div>
                </div>
            </div> -->

            <div class="form-group field-maintain-process">
                <div class="layui-form-item">
                    <label for="tel" class="layui-form-label">设备编号:</label>
                    <div class="layui-input-inline" style="width: 800px;">
                        <?=Html::input('text','',$model->facility ? $model->facility->number : '',['class' => 'layui-input ','readonly' => 'readonly'])?>
                    </div>
                </div>
            </div>

            <div class="form-group field-maintain-process">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">区域:</label>
                <div class="layui-input-inline" style="width: 800px;"> 
                    <?=Html::input('text','',(!empty($model->area)? UserInfo::$areaArr[$model->area]:""),['class' => 'layui-input ','readonly' => 'readonly'])?>
                </div>
            </div>
        </div>

        <div class="form-group field-maintain-process">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">工序:</label>
                <div class="layui-input-inline" style="width: 800px;">  
                    <?=Html::input('text','',!empty($model->process)?(\backend\models\Facility::$processList[$model->process]):(!empty($model->facility->process)?(\backend\models\Facility::$processList[$model->facility->process]):""),['class' => 'layui-input ','readonly' => 'readonly'])?>
                </div>
            </div>
        </div>

            


            <div class="form-group field-maintain-process">
                <div class="layui-form-item">
                    <label for="tel" class="layui-form-label">问题事项:</label>
                    <div class="layui-input-inline" style="width: 800px;">
                        <?=Html::textarea('things', $model->things ,['class' => 'layui-textarea ','readonly' => 'readonly'])?>
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
            <!-- <div class=" things" >
                <div class="layui-form-item">
                    <label for="tel" class="layui-form-label">次品数量:</label>
                    <div class="layui-input-inline" style="width: 800px;">
                        <?=Html::input('text','',$model->rejects,['class' => 'layui-input ','readonly' => 'readonly'])?>
                    </div>

                </div>
            </div> -->


        </div>
    </div>
<?php

$js = <<<JS
 
JS;
$this->registerJs($js);