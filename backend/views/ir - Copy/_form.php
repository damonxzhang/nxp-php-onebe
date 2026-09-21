<?php
use backend\widgets\LayuiActiveForm;
use yii\helpers\Html;
$oldImageName = Html::getInputName($model,'old_images');
?>
<?php $form = LayuiActiveForm::begin([
        'fieldConfig' => [
            'template' => ' {input} ',
        ],
]) ?>

    <table class="layui-table">
        <tr style="width: 100%;">
            <th rowspan="4" class="leftTitle">建<br/>议<br/>人<br/>填<br/>写</th>
            <td colspan="8" class="noborderRight">
                <label class="layui-form-label leftLable">主题：</label>
                <div class="layui-input-block rightbox">
                    <?= $form->field($model,'subject')->input('text',['class' => 'layui-input', 'lay-verify'=>"required",'autocomplete'=>"off" ])?>
                </div>
            </td>
            <td colspan="4" class="noborderLeft">
                <label class="layui-form-label leftLable01">填表日期：</label>
                <div class="layui-input-block rightbox01">
                    <input type="text"  disabled class="layui-input" value="<?= !empty($model->create_time) ? date('Y-m-d',$model->create_time) : date('Y-m-d' )?>" >
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="3" class="noborder">
                <label class="layui-form-label leftLable01 ">建议类别：</label>
            </td>
            <td colspan="9" class="noborder">
                <?php foreach ($model::$adviceTypes as  $key => $val):?>
                    <input type="radio"   <?= $model->advice_type == $key  ? 'checked': '';?> name="<?= Html::getInputName($model,'advice_type')?>" value="<?=$key?>" title="<?= $val ?>">
                <?php endforeach;?>
            </td>
        </tr>
        <tr>
            <td colspan="12">
                <div id="">目前状况：</div>
                <div class="">
                    <?= $form->field($model,'old_status')->textarea( ['rows' => '7', 'style'=>"width: 100%;" ])?>

                </div>
                <div id="">建议内容：（请尽量与相关人员讨论后填写）</div>
                <div class="">
                    <?= $form->field($model,'advice_content')->textarea( ['rows' => '7', 'style'=>"width: 100%;" ])?>

                </div>
                <div class="layui-upload">
                    <button type="button" class="layui-btn" id="image_upload">多图片上传</button>
                    <blockquote class="layui-elem-quote layui-quote-nm" style="margin-top: 10px;">
                        预览图：
                        <div class="layui-upload-list" id="image_show">
                            <?php

                                if($model->old_images):
                                    foreach ($model->old_images as  $val):
                                        if(empty($model->images_path[$val])) continue;
                            ?>
                                    <span class="itemimg">
                                        <input type="hidden" name="<?=$oldImageName?>[]" value="<?=$val?>">
                                        <img src="<?=$model->images_path[$val]?>" class="layui-upload-img show-img">
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
            <td colspan="2" class="noborderRight noborderLeft">
                <td class="boderstyle"><label class="layui-form-label leftLable">gemba小组：</label></td>
                <td  class="boderstyle">
                    <input type="text" name="" disabled required lay-verify="required"  value="<?=\backend\models\Team::getTeamName($userInfo['team'])?>"  autocomplete="off" class="layui-input">
                </td>
            </td>
            <td colspan="2" class="noborderRight noborderLeft">
                <label class="layui-form-label leftLable">班次：</label>
                <div class="layui-input-block rightbox">
                    <input type="text" name="" disabled required lay-verify="required"  value="<?=$userInfo::$shiftArr[$userInfo['shift']]?>"  autocomplete="off"
                           class="layui-input">
                </div>
            </td>
            <td colspan="2" class="noborderLeft">
                <label class="layui-form-label leftLable">部门：</label>
                <div class="layui-input-block rightbox">
                    <input type="text" name="" disabled required lay-verify="required"  value="<?=$userInfo::$sectionArr[$userInfo['section']] ?>"  autocomplete="off" class="layui-input">
                </div>
            </td>
        </tr>
    </table>

    <div class="layui-form-item"  style="margin-top: 50px;">
        <label for="" class="layui-form-label">
        </label>
        <?= Html::button('保存', ['class' => 'layui-btn submit']) ?>
        <?= Html::button('保存草稿', ['class' => 'layui-btn layui-btn-normal submit-draft']) ?>
    </div>

<?php LayuiActiveForm::end()?>
<?php

$js = <<<JS
    layui.use('laydate', function(){
        var laydate = layui.laydate;
         
        laydate.render({
            elem: '#startTime' //指定元素 
        });
    });

    $(document).on('click','.image-span',function(){
        var _this = $(this).parents('span');
        layer.confirm('是否确认删除?',function(index){
            _this.remove();
            layer.closeAll();
        });
    });

       
    $('.submit').click(function() {
       layer.load(3); 
        var form = $('form').serialize(); 
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
       
    $('.submit-draft').click(function() { 
        layer.load(3); 
        var form = $('form').serialize();  
        $.ajax({ 
            url:'add-draft',
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
    /**
     * Ir页面图片上传
     */
    layui.use('upload', function() {
        var $ = layui.jquery,
            upload = layui.upload;
        //多图片上传
        upload.render({
            elem: '#image_upload',
            url: 'upload',
            multiple: true,
            done: function(res) {
                //上传完毕
                if(res.code == 200){
                    var html = '<span class="itemimg">' +
                        '<input type="hidden" name="{$oldImageName}[]" value="'+res.id+'">'+
                        '<img src="' + res.path + '"  class="layui-upload-img show-img">' +
                        '<i class="delicon layui-icon layui-icon-close-fill image-span"></i>'+
                        '</span>';
                    $('#image_show').append(html)
                }else{
                    layer.msg(res.message);
                }
    
            }
        });
    });
      
JS;

$this->registerJs($js);

?>
