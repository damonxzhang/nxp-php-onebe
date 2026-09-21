<?php
use backend\models\Ir;
use yii\helpers\Html;
use backend\widgets\LayuiActiveForm;


?>
<form class="layui-form" action="">
    <table class="layui-table">
        <tr style="width: 100%;">
            <th rowspan="4" class="leftTitle">建<br/>议<br/>人<br/>填<br/>写</th>
            <td colspan="8" class="noborderRight">
                <label class="layui-form-label leftLable">主题：</label>
                <div class="layui-input-block rightbox">
                    <input type="text" value="<?=$model->subject?>" disabled required lay-verify="required" autocomplete="off" class="layui-input">
                </div>
            </td>
            <td colspan="4" class="noborderLeft">
                <label class="layui-form-label leftLable01">填表日期：</label>
                <div class="layui-input-block rightbox01">
                    <input type="text" disabled value="<?= date('Y-m-d',$model->create_time)?>" required lay-verify="required" autocomplete="off" class="layui-input">
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="3" class="noborder">
                <label class="layui-form-label leftLable01 ">建议类别：</label>
            </td>
            <td colspan="9" class="noborder">
                <?php foreach ($model::$adviceTypes as  $key => $val):?>
                    <input type="radio" disabled <?= $model->advice_type == $key  ? 'checked': '';?> name="<?= Html::getInputName($model,'advice_type')?>" value="<?=$key?>" title="<?= $val ?>">
                <?php endforeach;?>
            </td>
        </tr>
        <tr>
            <td colspan="12">
                <div id="">目前状况：</div>
                <div class="">
                    <textarea name="" rows="7" disabled style="width: 100%;"><?=$model->old_status?></textarea>
                </div>
                <div id="">建议内容：（请尽量与相关人员讨论后填写）</div>
                <div class="">
                    <textarea name="" rows="7" disabled style="width: 100%;"><?=$model->advice_content?></textarea>
                </div>
                <div class="layui-upload">
                    <button type="button" class="layui-btn" id="test2">图片上传</button>
                    <blockquote class="layui-elem-quote layui-quote-nm" style="margin-top: 10px;">
                        预览图：
                        <div class="layui-upload-list" >
                            <?php

                            if($model->old_images):
                                foreach ($model->old_images as  $val):
                                    if(empty($model->images_path[$val])) continue;
                                    ?>
                                    <span class="itemimg">
                                        <img src="<?=$model->images_path[$val]?>" class="layui-upload-img show-img">
                                    </span>
                                <?php
                                endforeach;
                            endif;
                            ?>
                        </div>
                    </blockquote>
                </div>
            </td>

        </tr>
        <tr>
            <td colspan="2" class="noborderRight">
                <label class="layui-form-label leftLable">姓名：</label>
                <div class="layui-input-block rightbox">
                    <input type="text" disabled value="<?= $model->createUserInfo->chinese_name?>" required lay-verify="required" autocomplete="off" class="layui-input">
                </div>
            </td>
            <td colspan="2" class="noborderRight noborderLeft">
                <label class="layui-form-label leftLable">CID：</label>
                <div class="layui-input-block rightbox">
                    <input type="text" disabled value="<?= $model->createUserInfo->badge_code?>" required lay-verify="required" autocomplete="off" class="layui-input">
                </div>
            </td>

            <td colspan="2" class="noborderRight noborderLeft">

                <td class="boderstyle"><label class="layui-form-label leftLable">gemba小组：</label></td>
                <td  class="boderstyle">
                    <input type="text" name="" disabled required lay-verify="required"  value="<?=\backend\models\Team::getTeamName($model->createUserInfo->team)?>"  autocomplete="off" class="layui-input">
                </td>
            </td>
            <td colspan="2" class="noborderRight noborderLeft">
                <label class="layui-form-label leftLable">班次：</label>
                <div class="layui-input-block rightbox">
                    <input type="text" disabled value="<?=$userInfo::$shiftArr[$model->createUserInfo->shift]?>" required lay-verify="required" autocomplete="off" class="layui-input">
                </div>
            </td>
            <td colspan="2" class="noborderLeft">
                <label class="layui-form-label leftLable">部门：</label>
                <div class="layui-input-block rightbox">
                    <input type="text" disabled value="<?=$userInfo::$sectionArr[$model->createUserInfo->section] ?>" required lay-verify="required" autocomplete="off" class="layui-input">
                </div>
            </td>
        </tr>
    </table>
</form>
<!--主管意见-->
<?php if($model->status != Ir::STATUS_DIE && $model->status != Ir::STATUS_APPEAL ) {?>
<?php $form = LayuiActiveForm::begin([
    'fieldConfig' => [
        'template' => ' {input} ',
    ],
])?>
    <table class="layui-table" width="100%">
        <tr style="width: 100%;">
            <th rowspan="2" class="leftTitle">主<br/>管<br/>意<br/>见</th>
            <td colspan="12">
                <label class="layui-form-label leftLable02">不及格IR评判标准：</label>
                <div class="layui-input-block rightbox02">
                    <select name="<?=Html::getInputName($model,'standard')?>"  lay-filter="deny">
                        <option value="">请选择</option>
                        <?php foreach($model::$standards as $key => $val):?>
                            <option value="<?=$key?>" <?= $key == $model->standard ? 'selected' : ''  ?> ><?=$val?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="4" class="noborderRight">
                <label class="layui-form-label leftLable">初评：</label>
                <div class="layui-input-block rightbox">
                    <input type="text" name="<?=Html::getInputName($model,'director_advice')?>" value="<?=$model->director_advice? : '建议合理，同意'?>"  lay-verify="required" autocomplete="off" class="layui-input director_advice">
                </div>
            </td>
            <td colspan="4" class="noborderLeft noborderRight">
                <label class="layui-form-label leftLable02">直接主管签字：</label>
                <div class="layui-input-block rightbox02">
                    <input type="hidden" name="<?=Html::getInputName($model,'director_user')?>" value="<?=$userInfo->id?>" />
                    <input type="text" disabled value="<?=$userInfo->chinese_name?>" required lay-verify="required" autocomplete="off" class="layui-input">
                </div>
            </td>
            <td colspan="4" class="noborderLeft">
                <label class="layui-form-label leftLable01">评估日期：</label>
                <div class="layui-input-block rightbox01">
                    <input type="text" disabled value="<?= date('Y-m-d')?>" required lay-verify="required" autocomplete="off"
                           class="layui-input">
                </div>
            </td>
        </tr>
    </table>
    <div class="layui-form-item" style="margin-top: 50px;">
        <label for="" class="layui-form-label">
        </label>
        <?= Html::button('保存', ['class' => 'layui-btn submit']) ?>
    </div>
    <?php
    LayuiActiveForm::end();
}else{
?>
<form>
    <table class="layui-table" width="100%">
        <tr style="width: 100%;">
            <th rowspan="2" class="leftTitle">主<br/>管<br/>意<br/>见</th>
            <td colspan="12">
                <label class="layui-form-label leftLable02">不及格IR评判标准：</label>
                <div class="layui-input-block rightbox02">
                    <select disabled lay-filter="deny">
                        <option value="">请选择</option>
                        <?php foreach($model::$standards as $key => $val):?>
                            <option value="<?=$key?>" <?= $key == $model->standard ? 'selected' : ''  ?> ><?=$val?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="4" class="noborderRight">
                <label class="layui-form-label leftLable">初评：</label>
                <div class="layui-input-block rightbox">
                    <input type="text" disabled value="<?=$model->director_advice? : '建议合理，同意'?>"  lay-verify="required" autocomplete="off" class="layui-input director_advice">
                </div>
            </td>
            <td colspan="4" class="noborderLeft noborderRight">
                <label class="layui-form-label leftLable02">直接主管签字：</label>
                <div class="layui-input-block rightbox02">
                    <input type="text" disabled value="<?=$userInfo->chinese_name?>" required lay-verify="required" autocomplete="off" class="layui-input">
                </div>
            </td>
            <td colspan="4" class="noborderLeft">
                <label class="layui-form-label leftLable01">评估日期：</label>
                <div class="layui-input-block rightbox01">
                    <input type="text" disabled value="<?= date('Y-m-d')?>" required lay-verify="required" autocomplete="off"
                           class="layui-input">
                </div>
            </td>
        </tr>
    </table>
</form>
<?php
    echo $this->render('_appeal_form',['model' => $model,'userInfo' => $userInfo,'type' => \backend\models\Appeal::IR_DIRECTOR_TYPE]);
}
?>
<?php
$js = <<<JS
    
    $(function() { 
        $('.appeal-form').show(); 
    });
    layui.use('form', function(){
        var form = layui.form;
        form.on('select(deny)', function(data){   
            if(data.value != false){ 
                var text = data.othis.find('.layui-this').text(); 
                $('.director_advice').val(text);
            }else{ 
                $('.director_advice').val('建议合理，同意');
            }
        });   
    });
    
    $('.submit').click(function() { 
        layer.load(3);
        var form = $(this).parents('form').serialize(); 
        $.ajax({
            type:'post',
            dataType:'json',
            data:form,
            success:function(res) {
                layer.closeAll();
                if(res.status == 200){
                    window.location.href='index';
                }else{
                    layer.msg(res.msg);
                }
            }
        });
    });
    
JS;

$this->registerJs($js);

?>
