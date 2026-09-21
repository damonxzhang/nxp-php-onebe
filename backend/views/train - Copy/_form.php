<?php

use yii\helpers\Html;
use backend\widgets\LayuiActiveForm;
use yii\helpers\ArrayHelper;
use backend\models\UserInfo;

/* @var $this yii\web\View */
/* @var $model backend\models\Train */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="reain-ppt-form">

    <?php $form = LayuiActiveForm::begin(['id' => 'form']); ?>

    <?= $form->field($model, 'courses',['required' => true])->dropDownList(ArrayHelper::map($coursesList,'id','name'),['class' => 'layui-col-xs6','xm-select'=>'courses','id' => 'coursesSelect','lay-filte'=>"coursesSelect", 'xm-select-search'=>'', 'xm-select-radio'=>""] ) ?>

    <?= \backend\widgets\FormSelects::widget([
        'selectId' => 'UserId',
        'connectUser' => UserInfo::UserDropDownList($section['id']),
        'selectText' => '学员',
        'section' => $section,
        'value' => $model->user_ids,
        'required' => true,
        'selectName' => Html::getInputName($model,'user_ids'),
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
    ]); ?>
    <?php $director = ArrayHelper::map( (new UserInfo())->getConnectUserList(UserInfo::$leaderArr),'id','chinese_name')?>
    <?= $form->field($model, 'director',['required' => true])->dropDownList(ArrayHelper::merge(['' => '请选择主管'] ,$director),[ 'class'=>"layui-input",'xm-select' => 'director', 'placeholder'=>"请选择时间"] )?>

    <?= $form->field($model, 'start_time',['required' => true])->textInput([ 'class'=>"layui-input ",'id'=>"startTime", 'placeholder'=>"请选择时间"]) ?>

    <?= $form->field($model, 'end_time')->textInput(['value' => 16]) ?>

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
    
    layui.formSelects.on('courses', function(name,obj,data,select) {
            var con = $('.content');
            if(select === true){
 
                con.val({$contents}[data.value]);
            }else{
                con.val(''); 
            } 
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