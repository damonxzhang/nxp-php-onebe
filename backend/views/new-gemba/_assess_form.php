<?php
use backend\widgets\LayuiActiveForm;
use yii\helpers\Html;
use backend\models\NewGembaData;
use backend\models\UserInfo;
$this->registerCssFile('@css/ir.css');
?>
<style>
    .auto-input{
        display: none;
    }
</style>
<form class="layui-form">
    <table class="layui-table">
        <tr style="width: 100%;">
            <th rowspan="6" class="leftTitle">创<br/>建<br/>人<br/>填<br/>写</th>
            <td colspan="7" class="noborderRight">
                <label class="layui-form-label leftLable">主题：</label>
                <div class="layui-input-block rightbox">
                    <input class="layui-input" value="<?=$model->title?>">
                </div>
            </td>

            <td colspan="5" class="noborderLeft">
                <label class="layui-form-label leftLable01">填表日期：</label>
                <div class="layui-input-block rightbox01">
                    <input type="text"  disabled class="layui-input" value="<?= !empty($model->create_time) ? date('Y-m-d',$model->create_time) : date('Y-m-d' )?>" >
                </div>
            </td>
        </tr>
        <tr>
             <td colspan="2" class="noborder">
                <label class="layui-form-label" style="width: 120px;">Gemba类别：</label>
            </td>
            <td colspan="9" class="noborder">
                <?php foreach (\backend\models\NewGemba::$typeList as  $key => $val): ?>
                    <input type="radio"   <?= $model->type == $key  ? 'checked': '';?> disabled value="<?=$key?>" title="<?= $val ?>">
                <?php endforeach;?>
            </td>
        </tr>


        <tr>
            <td colspan="11">
                <div >Gemba问题描述：</div>
                <div class="">

                    <textarea style="width: 100%;" disabled rows="7"><?=$model->content?></textarea>
                </div>

                <div class="layui-upload">
                    <button type="button" class="layui-btn" id="image_upload">多图片上传</button>
                    <blockquote class="layui-elem-quote layui-quote-nm" style="margin-top: 10px;">
                        预览图：
                        <div class="layui-upload-list" id="image_show">
                            <?php

                            if(!empty($model->files)):
                                foreach ($model->files as  $val):
                                    if(empty($val)) continue;
                                    ?>
                                    <span class="itemimg">
                                            <input type="hidden" value="<?=$val?>">
                                            <img src="<?=$val?>" class="layui-upload-img show-img">
                                            <i class="delicon layui-icon layui-icon-close-fill image-span"></i>
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
            <td colspan="1" class="boderstyle ">
                <label >是否自主完成：</label>
            </td>
            <td class="boderstyle">
                <div >
                    <?= Html::dropDownList('auto_complete',$model->auto_complete, NewGembaData::$autoCompleteList,['disabled' => ''])?>
                </div>
            </td>
            <td class="boderstyle ">
                <label >是否有标准：</label>
            </td>
            <td class="boderstyle">
                <div >
                    <?= Html::dropDownList('auto_complete',$model->norm, NewGembaData::$normList,['disabled' => ''])?>
                </div>
            </td>
            <td class="boderstyle">
                <label >选择区域：</label>
            </td>
            <td class="boderstyle">
                <div >
                    <?= Html::dropDownList('area',$model->area, UserInfo::$areaArr ,['disabled' => ''])?>
                </div>
            </td>
            <td class="boderstyle" colspan="5"></td>
        </tr>
        <tr>
            <td colspan="2" class="noborderRight noborderLeft">
                <label class="layui-form-label leftLable">周别：</label>
                <div class="layui-input-block rightbox">
                    <input class="layui-input" disabled value="<?=$model->week?>">
                </div>
            </td>
            <td colspan="2" class="noborderRight noborderLeft">
                <label class="layui-form-label leftLable">月份：</label>
                <div class="layui-input-block rightbox">
                    <input class="layui-input" disabled value="<?=$model->month?>">

                </div>
            </td>
            <td class="boderstyle noborderRight noborderLeft">
                <label class="layui-form-label leftLable">建议实施人：</label>
            </td>
            <td colspan="1" class="boderstyle noborderRight noborderLeft">
                <?= \backend\widgets\SearchUser::widget([
                    'name' => 'participant',
                    'url' => \yii\helpers\Url::to('/user-info/search-by-name'),
                    'value' => $model->participant,
                    'options' => [
                        'multiple' => true,
                        'class' => 'participant',
                        'disabled' => true
                    ],
                    'pluginOptions' => [
                        'closeOnSelect' => false,
                    ]
                ])?>
            </td>
            <td class="boderstyle" colspan="5"></td>

        </tr>


        <tr>
            <td colspan="2" class="noborderRight">
                <label class="layui-form-label leftLable">姓名：</label>
                <div class="layui-input-block rightbox">
                    <input type="text" name="" disabled required lay-verify="required" value="<?=$userInfo['chinese_name']?>" autocomplete="off" class="layui-input">

                </div>
            </td>
            <td colspan="2" class="noborderRight noborderLeft">
                <label class="layui-form-label leftLable">CID：</label>
                <div class="layui-input-block rightbox">
                    <input type="text" name="" disabled required lay-verify="required"  value="<?=$userInfo['badge_code']?>"  autocomplete="off" class="layui-input">
                </div>
            </td>

            <td class="boderstyle">
                <label class="layui-form-label leftLable">gemba小组：</label>
            </td>
            <td  class="boderstyle">
                <input type="text" name="" disabled required lay-verify="required"  value="<?=\backend\models\Team::getTeamName($userInfo['team'])?>"  autocomplete="off" class="layui-input">
            </td>
            <td colspan="2" class="noborderRight noborderLeft">
                <label class="layui-form-label leftLable">班次：</label>
                <div class="layui-input-block rightbox">
                    <input type="text" name="" disabled required lay-verify="required"  value="<?=\backend\models\UserInfo::$shiftArr[$userInfo['shift']]?>"  autocomplete="off"
                           class="layui-input">
                </div>
            </td>
            <td colspan="2" class="noborderLeft">
                <label class="layui-form-label leftLable">部门：</label>
                <div class="layui-input-block rightbox">
                    <input type="text" name="" disabled required lay-verify="required"  value="<?=\backend\models\UserInfo::$sectionArr[$userInfo['section']] ?>"  autocomplete="off" class="layui-input">
                </div>
            </td>
        </tr>

    </table>
</form>
<?php $form = LayuiActiveForm::begin([
        'fieldConfig' => [
            'template' => ' {input} ',
        ],
    ]) ?>

    <table class="layui-table" width="100%">
        <tr style="width: 100%;">
            <th rowspan="6" class="leftTitle">审<br/>核<br/>人<br/>填<br/>写</th>
            <td colspan="1" class="boderstyle ">
                <?= Html::a('更换审批区域','javascript:;',['class' => 'layui-btn','onclick' => "x_admin_show('更换审批区域', 'change-area?id=".$model->id."',500,500)"])?>
            </td>

        </tr>
        <tr>
            <td colspan="12">
                <div >审核人意见：</div>
                <div class="">
                    <?= $form->field($model,'assess_advice')->textarea(['rows' => 7 ,'style' => 'width: 100%;','lay-verify'=>'required']) ?>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="1" class="boderstyle ">
                <label >是否接受：</label>
            </td>
            <td class="boderstyle">
                <div >
                    <?=$form->field($model,'assess_status')->dropDownList(NewGembaData::$selectList,[' lay-filter' => 'assess_status'])?>
                </div>
            </td>
            <td class="boderstyle ">
                <label >审核日期：</label>
            </td>
            <td class="boderstyle">
                <div >
                    <?= $form->field($model,'assess_time')->input('text',['class' => 'layui-input','value' => date('Y-m-d'),'disabled' => '']) ?>
                </div>
            </td>

            <td class="boderstyle noborderRight noborderLeft">
                <label class="layui-form-label leftLable">实施人：</label>
            </td>
            <td colspan="1" class="boderstyle noborderRight noborderLeft">
                <?= \backend\widgets\SearchUser::widget([
                    'name' => Html::getInputName($model,'work_user'),
                    'url' => \yii\helpers\Url::to(['/user-info/search-by-name','area' => '']),
                    'value' => $model->work_user,
                    'options' => [
                        'disabled' => false,
                    ]
                ])?>
            </td>

            <td class="boderstyle">
                <label class="layui-form-label leftLable">审核人签字：</label>
            </td>
            <td  class="boderstyle">
                <input type="text" name="" disabled required lay-verify="required"  value="<?=(new \backend\models\UserInfo())->getUserName(Yii::$app->user->identity->user_id)?>"  autocomplete="off" class="layui-input">
            </td>

        </tr>
    </table>
    <div class="layui-form-item" style="margin-top: 50px;">
        <label for="" class="layui-form-label">
        </label>
        <?= Html::button('保存', ['class' => 'layui-btn','lay-submit' => '','lay-filter' => 'add']) ?>
    </div>
<?php
    LayuiActiveForm::end();

?>
<?php

$js = <<<JS
    layui.use('laydate', function(){
        var laydate = layui.laydate; 
        laydate.render({
            elem: '#confirmTime' //指定元素 
        });
    });
 
    var form = layui.form;
    form.on('submit(add)', function(data){
        layer.load(3);
        data.field.participant = $('.participant').select2('val');
        $.ajax({
        type:'post',
        dataType:'json',
        data:data.field,
        success:function(res) {
            layer.closeAll();
            if(res.status == 200){
                window.location.href='index';
            }else{
                layer.msg(res.msg);
            }
        }
        });
        return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。 
    });
   
         
    form.on('select(assess_status)', function(data){ 
        let status = data.value; //得到被选中的值 
        if(status == 0){
            $('select[name="NewGembaData[work_user]"]').attr('disabled',true); 
        }else{
            $('select[name="NewGembaData[work_user]"]').attr('disabled',false); 
        }
    });
      
JS;

$this->registerJs($js);

?>
