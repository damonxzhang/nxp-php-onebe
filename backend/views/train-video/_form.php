<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use backend\models\TrainFileType;
use backend\widgets\LayuiActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\TrainVideo */
/* @var $form yii\widgets\ActiveForm */
?>
<style>
    .upload-type-1{
        display: none;
    }
</style>
<div class="reain-vedio-form">

    <?php $form = LayuiActiveForm::begin(['id' => 'video_form']); ?>

    <?= $form->field($model, 'name',['required' => true])->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList( \backend\models\TrainPpt::$statusArr) ?>

    <?= $form->field($model, 'type',['required' => true])->dropDownList(ArrayHelper::merge(['' => '请选择类型'],TrainFileType::getTypes(TrainFileType::TYPE_VIDEO))) ?>

    <div class="form-group field-trainvideo-name required">
        <div class="layui-form-item">
            <label for="tel" class="layui-form-label">
                <label class="control-label" for="trainvideo-name">上传方式</label>:
            </label>
            <div class="layui-input-inline" style="width: 800px;">
                <select lay-filter="upload-type">
                    <option value="1">文件上传</option>
                    <option value="2">名称输入</option>
                </select>
                <div class="layui-col-md4" style="color:red"><div class="help-block"></div></div>
            </div>
        </div>
    </div>

    <div class="form-group field-trainvideo-name required upload-type-1">
        <div class="layui-form-item">
            <label for="tel" class="layui-form-label">
                <label class="control-label" for="trainvideo-name">文件名称</label>:
            </label>
            <div class="layui-input-inline" style="width: 800px;">
                <input type="text" class="layui-input" name="file_path" aria-required="true" placeholder="请输入文件名称,对个文件可用逗号隔开"></div>
            <div class="layui-col-md4" style="color:red"><div class="help-block"></div></div>
        </div>
    </div>

    <div class="layui-upload upload-type-2" style="margin-left: 110px;">
        <button type="button" class="layui-btn layui-bg-cyan" id="fileListVideo">请选择文件</button>
        <div class="layui-upload-list">
            <table class="layui-table" style="width: 800px;">
                <thead>
                <tr>
                    <th>文件名</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody id="demoListVideo">
                <?php
                $paths = json_decode($model->video_path,1);
                if(is_array($paths) && count($paths) > 0):
                    foreach ($paths as $val):
                        ?>
                        <tr class="vedio-paths">
                            <td><?= substr($val,strrpos($val,'/') + 1) ?></td>
                            <td><span style="color: #5FB878;">上传成功</span></td>
                            <td>
                                <a class="layui-btn layui-btn-xs delete-vedio">删除</a><input type="hidden" name="upload_file[]" value="<?=$val?>">
                            </td>
                        </tr>
                    <?php endforeach;?>
                <?php endif;?>
                </tbody>
            </table>
        </div>
        <button type="button" onclick="uploadFileVideo(this)" class="layui-btn layui-btn-normal" id="testListActionVideo">上传文件</button>
    </div>

    <div class="layui-form-item"  style="margin-top: 50px;">
        <label for="" class="layui-form-label">
        </label>
        <?= Html::button('保存', ['class' => 'layui-btn submit']) ?>
    </div>

    <?php LayuiActiveForm::end(); ?>

</div>
<?php $this->beginBlock('footer') ?>
<script>
    layui.use('layer', function(){
        var layer = layui.layer;
                  
        $('.delete-vedio').click(function() {
            $(this).parents('.vedio-paths').remove();
        });
        $('.submit').click(function() {
            var form = $('#video_form').serialize();
            layer.load(3);
            $.post('',form,function(res) { 
                layer.closeAll();
                if(res.status == 200){
                    location.href="/train-video/index"
                }else{
                    layer.closeAll();
                    layer.msg(res.msg);
                }
            },'json');
        });
        
        var form = layui.form;
        form.on('select(upload-type)',function(data){
            var type = data.value;
             console.log(type);
            if(type == 1){
                
                $('.upload-type-2').show();
                $('.upload-type-1').hide();
            }else if( type == 2 ){
                $('.upload-type-1').show();
                $('.upload-type-2').hide();
            }
        });
    }); 
    //上传视频
    function uploadFileVideo(obj) {
        var content = $("#demoListVideo").html();
        if (content== null || content == 0) {
            layer.alert("请选择文件")
        }
        $(obj).text('提交中').attr("disabled","disabled").addClass("layui-disabled");
    }
    layui.use('upload', function(){
        var $ = layui.jquery,upload = layui.upload;
        //多文件列表示例
        var demoListView = $('#demoListVideo'),uploadListIns = upload.render({
            elem: '#fileListVideo'
            ,url: 'upload'
            ,method: 'post'
            ,accept: 'file'
            ,multiple: true
            ,auto: false
            ,bindAction: '#testListActionVideo'
            ,choose: function(obj){
                var files = this.files = obj.pushFile(); //将每次选择的文件追加到文件队列
                //读取本地文件
                obj.preview(function(index, file, result){
                    var tr = $(['<tr id="upload-'+ index +'">'
                        ,'<td>'+ file.name +'</td>'
                        ,'<td>等待上传</td>'
                        ,'<td>'
                        ,'<button class="layui-btn layui-btn-xs demo-delete">删除</button>'
                        ,'</td>'
                        ,'</tr>'].join(''));
                    //单个重传
                    tr.find('.demo-reload').on('click', function(){
                        obj.upload(index, file);
                    });
                    //删除
                    tr.find('.demo-delete').on('click', function(){
                        delete files[index]; //删除对应的文件
                        tr.remove();
                        uploadListIns.config.elem.next()[0].value = ''; //清空 input file 值，以免删除后出现同名文件不可选
                    });
                    demoListView.append(tr);
                });
            }
            ,done: function(res, index, upload){
                if(res.code == 200){ //上传成功
                    var tr = demoListView.find('tr#upload-'+ index),tds = tr.children();
                    tds.eq(1).html('<span style="color: #5FB878;">上传成功</span>');
                    tds.eq(2).append('<input type="hidden" name="upload_file[]" value="'+res.path+'">');
                    tds.eq(3).html(''); //清空操作
                    $("#testListActionVideo").text('上传文件').attr("disabled","disabled").removeClass("layui-disabled");
                    $("#testListActionVideo").removeAttr('disabled');
                    return delete this.files[index]; //删除文件队列已经上传成功的文件
                }
                this.error(index, upload);
            }
            ,error: function(index, upload){
                var tr = demoListView.find('tr#upload-'+ index),tds = tr.children();
                tds.eq(1).html('<span style="color: #FF5722;">上传失败</span>');
                tds.eq(2).find('.demo-reload').removeClass('layui-hide'); //显示重传
                $("#testListActionVideo").text('上传文件').attr("disabled","disabled").removeClass("layui-disabled");
                $("#testListActionVideo").removeAttr('disabled');
            }
        });
    });

</script>
<?php $this->endBlock() ?>
