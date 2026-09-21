<?php

use yii\helpers\Html;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;
use backend\models\UserInfo;
use backend\widgets\SearchUser;
use backend\widgets\LayuiActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Train */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="reain-ppt-form">

    <?php $form = LayuiActiveForm::begin(['id' => 'form']); ?>

    <?= $form->field($model, 'courses',['required' => true])->widget(Select2::classname(), [
    'options' => ['placeholder' => '请输入标题名称 ...','lay-ignore' => true],
    'data' => $coursesList,
]) ?>

    <?= \backend\widgets\SelectSearch::widget([
        'name' => 'UserId',
        'text' => '学员',
        'columnName' => Html::getInputName($model,'user_ids'),
        'value' => $model->user_ids,
        'required' => true,
        'selectWidth' => '800px',
        'section' => $section,
        'options' => [
            'multiple' => true,
        ],
        'addSelectList' => [
            'team' => [
                'text' =>'请选择gemba小组',
                'data'=> ArrayHelper::map( \backend\models\Team::getAllByArray(),'id','teamName')
            ],

            'gemba_role' => [
                'text' =>'请选择Gemba权限',
                'data'=> \backend\models\Gemba::$showGeambaRole,
            ],
        ],
    ])?>
    <?php $director = ArrayHelper::map( (new UserInfo())->getConnectUserList(UserInfo::$leaderArr),'id','chinese_name')?>

    <div class="form-group field-train-director required">
        <div class="layui-form-item">
            <label for="tel" class="layui-form-label">
                <span class="x-red">*</span>
                <label class="control-label" for="train-director">讲师</label>:
            </label>
            <div class="layui-input-inline" style="width: 800px;">
                <?= SearchUser::widget([
                    'name' => Html::getInputName($model,'lecturer'),
                    'localSearch' => true,
                    'value' => $model->lecturer,
                    'options' => [
                        'multiple' => true,
                        'class' => 'director',
                        'width' => '600px',
                        'placeholder' => '请输入讲师名称'
                    ],
                    'pluginOptions' => [
                        'closeOnSelect' => false,
                    ]
                ])?>
            </div>
    </div>
    <div class="form-group field-train-director required">
        <div class="layui-form-item">
            <label for="tel" class="layui-form-label">
                <span class="x-red">*</span>
                <label class="control-label" for="train-director">主管</label>:
            </label>
            <div class="layui-input-inline" style="width: 800px;">
                <?= SearchUser::widget([
                    'name' => Html::getInputName($model,'director'),
                    'localSearch' => true,
                    'value' => $model->director,
                    'options' => [
                        'class' => 'director',
                        'multiple' => true,
                        'placeholder' => '请输入主管名称'
                    ],
                    'pluginOptions' => [
                        'closeOnSelect' => false,
                    ]
                ])?>
            </div>
    </div>
    <?= $form->field($model, 'start_time',['required' => true])->textInput([ 'class'=>"layui-input ",'id'=>"startTime", 'placeholder'=>"请选择时间"]) ?>

    <?= $form->field($model, 'end_time')->textInput(['value' => 8]) ?>

    <?= $form->field($model, 'content')->textarea(['class' => 'layui-textarea content' ]) ?>

    <div class="layui-form-item"  style="margin-top: 50px;">
        <label for="" class="layui-form-label">
        </label>
        <?= Html::button('保存', ['class' => 'layui-btn submit']) ?>
    </div>

    <?php LayuiActiveForm::end(); ?>

</div>

<?php
$contents = json_encode(ArrayHelper::map($coursesList,'id','comment'));
$js = <<<JS

   
 
    layui.use('laydate', function(){
        var laydate = layui.laydate;
        laydate.render({
            elem: '#startTime'
        });
    });
     
    $('.submit').click(function() {
        var data = $(this).parents('form').serialize();
        layer.load(3);
        $.post('',data,function(res) {
            layer.closeAll();
            if(res.status == 200){
                location.href='index'
            }else{
                layer.msg(res.msg);   
            }
        },'json');
    });
JS;

$this->registerJs($js);

?>