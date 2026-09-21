<?php
use backend\widgets\LayuiActiveForm;
use yii\helpers\Html;
use backend\models\UserInfo;
use backend\models\NewGemba;
use yii\helpers\ArrayHelper;

$this->registerCssFile('@css/ir.css');
$filesName = Html::getInputName($model,'files');
$filesMappingName = Html::getInputName($model,'files_mapping');

?>
<style>
    .auto-input{
        display: none;
    }
</style>

<?php $form = LayuiActiveForm::begin([
        'fieldConfig' => [
            'template' => ' {input} ',
        ],
]) ?>

    <table class="layui-table">
        <tr style="width: 100%;">
            <th rowspan="7" class="leftTitle">创<br/>建<br/>人<br/>填<br/>写</th>
            <td colspan="7" class="noborderRight">
                <label class="layui-form-label leftLable">主题：</label>
                <div class="layui-input-block rightbox">
                    <?= $form->field($model,'title')->input('text',['class' => 'layui-input', 'lay-verify'=>"required",'autocomplete'=>"off" ])?>
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
                <?php foreach (NewGemba::$typeList as  $key => $val):?>
                    <input type="radio" <?= $model->type == $key  ? 'checked': '';?> name="<?= Html::getInputName($model,'type')?>" value="<?=$key?>" title="<?= $val ?>">
                <?php endforeach;?>
            </td>
        </tr>
        <tr>
            <td colspan="11">
                <div >Gemba问题描述：</div>
                <div class="">
                    <?= $form->field($model,'content')->textarea( ['rows' => '7', 'style'=>"width: 100%;",'lay-verify'=>'required' ])?>
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
                                        <input type="hidden" name="<?=$filesName?>[]" value="<?=$val?>">
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
                <?php  if($model->steps == 3 || $model->auto_complete == 1): ?>
                <div class="layui-upload">
                    <button type="button" class="layui-btn" id="image_upload_mapping">AFTER</button>
                    <blockquote class="layui-elem-quote layui-quote-nm" style="margin-top: 10px;">
                        预览图：
                        <div class="layui-upload-list" id="image_show_mapping">
                            <?php

                            if(!empty($model->files_mapping)):
                                foreach ($model->files_mapping as  $val):
                                    if(empty($val)) continue;
                                    ?>
                                    <span class="itemimg">
                                        <img src="<?=$val?>" class="layui-upload-img show-img">
                                        <i class="delicon layui-icon layui-icon-close-fill image-span_mapping"></i>
                                    </span>
                                <?php
                                endforeach;
                            endif;
                            ?>
                        </div>
                    </blockquote>
                </div>
                <?php  endif; ?>
            </td>

        </tr>
        <tr >
            <td class="boderstyle ">
                <label >是否自主完成：</label>
            </td>
            <td class="boderstyle">
                <div >
                    <?= $form->field($model,'auto_complete',['required' => true])->dropDownList(ArrayHelper::merge(['' => '请选择是否自主完成'] ,NewGemba::$autoCompleteList ),[ 'lay-filter'=>'auto-complete-select','lay-verify'=>"required" ])?>
                </div>
            </td>
            <td class="boderstyle ">
                <label >是否有标准：</label>
            </td>
            <td class="boderstyle">
                <div >
                    <?= $form->field($model,'norm',['required' => true])->dropDownList(ArrayHelper::merge(['' => '请选择是否有标准'] , NewGemba::$normList ),[ 'lay-filter'=>'auto-complete-select','lay-verify'=>"required" ])?>
                </div>
            </td>
            <td class="boderstyle">
                <label >选择区域：</label>
            </td>
            <td class="boderstyle">
                <div >
                    <?= $form->field($model,'area')->dropDownList( UserInfo::$areaGembaArr )?>
                </div>
            </td>
            <td class="boderstyle" colspan="5"></td>
        </tr>

        <tr class="auto-input">
            <td class="boderstyle ">
                <label >是否完成：</label>
            </td>
            <td class="boderstyle">
                <div >
                    <?= $form->field($model,'confirm')->dropDownList(\yii\helpers\ArrayHelper::merge(['' => '请选择是否完成'],\backend\models\NewGemba::$selectList))?>
                </div>
            </td>
            <td class="boderstyle ">
                <label >完成时间：</label>
            </td>
            <td class="boderstyle">
                <div >
                    <?php
                    if(!empty($model->confirm_time)){

                        $model->confirm_time = date('Y-m-d',$model->confirm_time);
                    }

                    echo $form->field($model,'confirm_time')->input('text',['class' => 'layui-input test-item','id' => 'confirmTime','value' => date('Y-m-d')])
                    ?>
                </div>
            </td>
        </tr>


        <tr>
            <td colspan="2" class="noborderRight noborderLeft">
                <label class="layui-form-label leftLable">周别：</label>
                <div class="layui-input-block rightbox">
                    <?= $form->field($model,'week')->input( 'text' ,[ 'class'=>'layui-input','disabled' => '','value' => date('W')])?>
                </div>
            </td>
            <td colspan="2" class="noborderRight noborderLeft">
                <label class="layui-form-label leftLable">月份：</label>
                <div class="layui-input-block rightbox">
                    <?= $form->field($model,'month')->input( 'text' ,[ 'class'=>'layui-input','disabled' => '','value' => date('m')])?>
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
                        'class' => 'participant'
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
                    <input type="text" name="" disabled value="<?=$userInfo['chinese_name']?>" autocomplete="off" class="layui-input">

                </div>
            </td>
            <td colspan="2" class="noborderRight noborderLeft">
                <label class="layui-form-label leftLable">CID：</label>
                <div class="layui-input-block rightbox">
                    <input type="text" name="" disabled value="<?=$userInfo['badge_code']?>"  autocomplete="off" class="layui-input">
                </div>
            </td>

            <td class="boderstyle">
                <label class="layui-form-label leftLable">gemba小组：</label>
            </td>
            <td  class="boderstyle">
                <input type="text" name="" disabled value="<?=\backend\models\Team::getTeamName($userInfo['team'])?>"  autocomplete="off" class="layui-input">
            </td>
            <td colspan="2" class="noborderRight noborderLeft">
                <label class="layui-form-label leftLable">班次：</label>
                <div class="layui-input-block rightbox">
                    <input type="text" name="" disabled value="<?=\backend\models\UserInfo::$shiftArr[$userInfo['shift']]?>"  autocomplete="off"
                           class="layui-input">
                </div>
            </td>
            <td colspan="2" class="noborderLeft">
                <label class="layui-form-label leftLable">部门：</label>
                <div class="layui-input-block rightbox">
                    <input type="text" name="" disabled value="<?=\backend\models\UserInfo::$sectionArr[$userInfo['section']] ?>"  autocomplete="off" class="layui-input">
                </div>
            </td>
        </tr>
    </table>

    <div class="layui-form-item"  style="margin-top: 50px;">
        <label for="" class="layui-form-label">
        </label>
        <?= Html::button('保存', ['class' => 'layui-btn','lay-submit' => '','lay-filter' => 'add']) ?>
        <?= Html::button('保存草稿', ['class' => 'layui-btn layui-btn-normal','style'=>'display:none','lay-submit' => '','lay-filter' => 'submit-draft']) ?>
    </div>

<?php LayuiActiveForm::end()?>
<?php
$auto_complete = $model->auto_complete;
$js = <<<JS
    layui.use('laydate', function(){
        var laydate = layui.laydate; 
        // laydate.render({
        //     elem: '#confirmTime' //指定元素 
        // });
        //同时绑定多个
        lay('.test-item').each(function(){
            laydate.render({
                elem: this
                ,format:'yyyy-MM-dd' 
                ,trigger: 'click'
            });
        }); 
    });

    $(function(){
        if("{$auto_complete}" == '1'){
            $('.auto-input').show();
        }
    });
    $(document).on('click','.image-span',function(){
        var _this = $(this).parents('span');
        layer.confirm('是否确认删除?',function(index){
            _this.remove();
            layer.closeAll();
        });
    });
    var form = layui.form;
    form.on('select(auto-complete-select)',function(data) {
        var data = data.value;
        if( data == 1 ){
            $('.auto-input').show();
        }else{
            $('.auto-input').hide(); 
        }
    });
    form.on('submit(add)', function(data){
        var checkb = true;
        var json=eval(data.field);
        $.each(json,function(key,value){  
            if (key == 'NewGembaData[auto_complete]') {
                if (value == 1 && json['NewGembaData[confirm]'] == 1) {
                    if(!json.hasOwnProperty("NewGembaData[files][0]")){
                        checkb = false;
                        return false;
                    }
                }
            }
        });
      
        if (checkb == false) {
            layer.msg('请上传图片！');
            return false;
        }else{
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
        }
    });
  
    form.on('submit(submit-draft)', function(data){ 
        layer.load(3); 
        data.field.participant = $('.participant').select2('val'); 
        data.field.draft = 1; 
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
                        '<input type="hidden" name="{$filesName}[]" value="'+res.path+'">'+
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
    $(document).on('click','.image-span_mapping',function(){
        var _this = $(this).parents('span');
        layer.confirm('是否确认删除?',function(index){
            _this.remove();
            layer.closeAll();
        });
    });
       /**
     * 面图片上传
     */
    layui.use('upload', function() {
        var $ = layui.jquery,
            upload = layui.upload;
        //多图片上传
        upload.render({
            elem: '#image_upload_mapping',
            url: 'upload',
            multiple: true,
            done: function(res) {
                //上传完毕
                if(res.code == 200){
                    var html = '<span class="itemimg">' +
                        '<input type="hidden" name="{$filesMappingName}[]" value="'+res.path+'">'+
                        '<img src="' + res.path + '"  class="layui-upload-img show-img">' +
                        '<i class="delicon layui-icon layui-icon-close-fill image-span_mapping"></i>'+
                        '</span>';
                    $('#image_show_mapping').append(html)
                }else{
                    layer.msg(res.message);
                }
    
            }
        });
    });
JS;

$this->registerJs($js);

?>
