<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use backend\widgets\SearchUser;
use backend\widgets\SearchActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\search\TrainSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="layui-row">

    <?php $form = SearchActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="layui-input-inline" style="width: 150px">

        <?= SearchUser::widget([
            'name' => Html::getInputName($model,'courses'),
            'url' => '/train-courses/search-select',
            'initValueText' => '测试',
            'options' => [
                'placeholder' => '请输入或选择课程',
            ]
        ])?>
    </div>
    <div class="layui-input-inline">

        <?= $form->field($model, 'start_progress')->dropDownList(ArrayHelper::merge(['' => '请选择开始完成率'],$model::$progressArray),[ 'autocomplete'=>"off",'placeholder' => '请输入开始签到率','onkeyup' => "value=value.replace(/[^\d]/g,'')"]) ?>
    </div>
    <div class="layui-input-inline">

        <?= $form->field($model, 'end_progress')->dropDownList(ArrayHelper::merge(['' => '请选择结束完成率'],$model::$progressArray),[ 'autocomplete'=>"off",'placeholder' => '请输入结束签到率','onkeyup' => "value=value.replace(/[^\d]/g,'')"]) ?>
    </div>
    <div class="layui-input-inline">

        <?= $form->field($model, 'shift')->dropDownList(ArrayHelper::merge(['' => '请选择班别'],\backend\models\UserInfo::$shiftArr),[ 'autocomplete'=>"off",'placeholder' => '请输入结束签到率','onkeyup' => "value=value.replace(/[^\d]/g,'')"]) ?>
    </div>

    <?= $form->field($model, 'start_time')->input('text',['id' => "startTime",'autocomplete'=>"off" ,'placeholder' => '请选择开始时间']) ?>

    <?= $form->field($model, 'end_time')->input('text',['id' => "endTime",'autocomplete'=>"off",'placeholder' => '请选择结束时间']) ?>


    <?= Html::submitButton('<i class="layui-icon"></i>查询', ['class' => 'layui-btn']) ?>
    <?= Html::a('<i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>刷新','index', ['class' => 'layui-btn']) ?>

    <?php SearchActiveForm::end(); ?>

</div>

<?php

$js = <<<JS
    layui.use('laydate',function() {
        var laydate = layui.laydate;
            //年选择器
            laydate.render({
                elem: '#startTime' 
            });
            
            //年月选择器
            laydate.render({
                elem: '#endTime' 
            });
    });
 
JS;
$js = $this->registerJs($js);
?>