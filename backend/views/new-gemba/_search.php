<?php
use yii\helpers\Url;
use yii\helpers\Html;
use backend\widgets\SearchActiveForm;
use backend\models\NewGemba;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model backend\models\search\NewGembaSearch */
/* @var $form yii\widgets\ActiveForm */
?>


<div class="layui-row">

    <?php $form = SearchActiveForm::begin([

        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'number')->input('text',[ 'placeholder'=>"请输入编号" ,'autocomplete'=>"off"]) ?>
    <?= $form->field($model, 'title')->input('text',[ 'placeholder'=>"请输入标题" ,'autocomplete'=>"off"]) ?>
    <?= $form->field($model, 'year')->input('text',[ 'placeholder'=>"请输入年" ,'autocomplete'=>"off"]) ?>
    <div class="layui-input-inline">
        <?= $form->field($model, 'type')->dropDownList(ArrayHelper::merge(['' => '请选择类型'],NewGemba::$typeList) ) ?>
    </div>
    <div class="layui-input-inline">
        <?= $form->field($model, 'steps')->dropDownList(ArrayHelper::merge(['' => '请选择步骤'],NewGemba::$stepsList) ) ?>
    </div>
    <div class="layui-input-inline">
        <?= $form->field($model, 'shift')->dropDownList(ArrayHelper::merge(['' => '请选择班别'],\backend\models\UserInfo::$shiftArr)) ?>
    </div>
    <div class="layui-input-inline">
        <?= $form->field($model, 'area')->dropDownList(ArrayHelper::merge(['' => '请选择区域'],\backend\models\UserInfo::$areaArr)) ?>
    </div>

    <div class="layui-input-inline">
        <?= $form->field($model, 'auto_complete')->dropDownList(ArrayHelper::merge(['' => '是否自主完成'],NewGemba::$autoCompleteList)) ?>
    </div>
    <?= $form->field($model, 'start_date')->input('text',[ 'placeholder'=>"请输入开始时间",'id' => 'startDate','autocomplete'=>"off"]) ?>
    <?= $form->field($model, 'end_date')->input('text',[ 'placeholder'=>"请输入结束时间",'id' => 'endDate','autocomplete'=>"off"]) ?>

    <?= Html::submitButton('<i class="layui-icon"></i>查询', ['class' => 'layui-btn']) ?>

    <?= Html::a('<i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>刷新','index', ['class' => 'layui-btn']) ?>
    <?= $form->field($model, 'keywords')->input('text',[ 'placeholder'=>"请输入关键词" ,'minlength'=>"2",'maxlength'=>"4" ]) ?>
    <?= Html::submitButton('<i class="layui-icon"></i>关键词搜索', ['class' => 'layui-btn']) ?>
    <a class="layui-btn layui-bg-blue" href="<?=  Url::to([$this->context->id . '/showdata','params' => Yii::$app->request->queryParams]) ?>">关键词展示</a>
    <?php SearchActiveForm::end(); ?>

</div>
<?php
$js =<<<JS
    layui.use('laydate',function() {
        var laydate = layui.laydate;
            //年选择器
            laydate.render({
                elem: '#startDate' 
            });
            
            //年月选择器
            laydate.render({
                elem: '#endDate' 
            });
    });

JS;
$this->registerJs($js);
?>