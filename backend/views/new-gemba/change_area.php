<?php

use yii\helpers\Html;
use backend\models\UserInfo;
use yii\helpers\ArrayHelper;
use backend\widgets\LayuiActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\NewGemba */

?>
<div class="x-body">
    <?php $form = LayuiActiveForm::begin()?>

        <div class="layui-form-item">
            <label for="nickname" class="layui-form-label">
                <span class="x-red">*</span>区域:
            </label>
            <div class="layui-input-inline" style="width: 300px;">
                <?=Html::dropDownList('area','', UserInfo::$areaGembaArr)?>
            </div>
        </div>
        <div class="layui-form-item">
            <label for="nickname" class="layui-form-label">
                <span class="x-red">*</span>内容:
            </label>
            <div class="layui-input-inline" style="width: 300px;">
                <?=Html::textarea('content','',['rows' => '7', 'style'=>"width: 100%;"])?>
            </div>
        </div>
        <div class="layui-form-item"  style="margin-top: 50px;">
            <label for="" class="layui-form-label">
            </label>
            <a class="layui-btn submit" >
                提交
            </a>
        </div>

        <div class="layui-form-item" style="color: red;">
            <label for="" class="layui-form-label"></label>
            *请先与更换的区域审核人沟通
        </div>
    <?php LayuiActiveForm::end()?>
</div>
<?php

$js =<<<JS
    $('.submit').click(function(){
        let form = $(this).parents('form').serialize();
        $.post('',form,function(res) {
            if(res.status == 200){
                 layer.msg(res.msg, {icon: 1,time: 1500}, function () {
                    var index = parent.layer.getFrameIndex(window.name);
                    parent.layer.close(index);
                    parent.location.href='index';
                })
            }else{
                layer.msg(res.msg);
            }
        },'json')
        
    });
JS;
$this->registerJs($js);

?>