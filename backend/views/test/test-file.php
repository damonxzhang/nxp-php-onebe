<link rel="stylesheet" href="https://heerey525.github.io/layui-v2.4.3/layui/css/layui.css"  media="all">
<script src="https://heerey525.github.io/layui-v2.4.3/layui/layui.js" charset="utf-8"></script>
<style>
    #progress{
        width: 300px;
        height: 20px;
        background-color:#f7f7f7;
        box-shadow:inset 0 1px 2px rgba(0,0,0,0.1);
        border-radius:4px;
        background-image:linear-gradient(to bottom,#f5f5f5,#f9f9f9);
    }

    .finish{
        background-color: #149bdf;
        background-image:linear-gradient(45deg,rgba(255,255,255,0.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,0.15) 50%,rgba(255,255,255,0.15) 75%,transparent 75%,transparent);
        background-size:40px 40px;
        height: 100%;
    }
    form{
        margin-top: 50px;
    }
</style>
<?php

use yii\helpers\Html;
use backend\widgets\LayuiActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\TrainVideo */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="reain-vedio-form">
    <label for="tel" class="layui-form-label">
        <span class="x-red">*</span>
        <label class="control-label" for="trainvideo-name">视频</label>:
    </label>
    <div class="layui-upload upload-div" style="margin-left: 110px;">
        <button type="button" class="layui-btn upload-file" >请选择文件</button>
        <div class="layui-upload-list">
            <table class="layui-table" style="width: 800px;">
                <thead>
                <tr>
                    <th>文件名</th>
                    <th>文件大小</th>
                    <th>进度</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody id="demoList">

                </tbody>
            </table>
        </div>
        <input id="video_file" type="file" class="layui-input" style="visibility: hidden">
    </div>

    <div class="layui-form-item"  style="margin-top: 50px;">
        <label for="" class="layui-form-label">
        </label>
        <?= Html::button('保存', ['class' => 'layui-btn submit']) ?>
    </div>


</div>

<script>

    $('.upload-file').click(function () {
        $('#video_file').trigger('click');
    });

    var fileNameTime = '';
    var video_num = 0;
    var jdt_s = '';
    var video_upload = '';
    var Timeout = '';
    //上传视频
    $('#video_file').on('change',function () {
        fileNameTime = Date.parse(new Date())/1000;
        video_num += 1;
        video_upload = new Upload();
        fileNameTime = fileNameTime+1;
        showFile(this.files[0]);
        jdt_s = $('.finish_video'+video_num);
        video_upload.addFileAndSend(this);
    });
    //删除已经上传的文件
    $(document).on('click','.delete-video',function(){
        var _this = $(this);
        layer.confirm('是否确认删除！', function () {
            _this.parents('tr').remove();
            layer.closeAll();
        });
    });

    //取消已经上传的文件
    $(document).on('click','.cancelling',function(){
        var _this = $(this);
        layer.confirm('是否确认取消！',function () {
            clearTimeout(Timeout);
            _this.parents('tr').remove();
            layer.closeAll();
        });
    });

    //显示文件信息
    function showFile(file){
         var  tr = $(['<tr id="upload'+video_num+'">'
             ,'<td>'+ file.name +'</td>'
             ,'<td>'+ (file.size/1024).toFixed(1) +'kb</td>'
             ,'<td><div id="progress"><div class="finish finish_video'+video_num+'" style="width: 0%;" progress="0"></div>'
             ,''
             ,'</div></td>'
             ,'<td>'
             ,'<button class="layui-btn layui-btn-xs layui-btn-warm cancelling">取消</button>'
             ,'</td>'
             ,'</tr>'].join(''));
         $('#demoList').append(tr);
    }
    

    //上传文件
    function Upload(){
        const LENGTH = 1024 * 5120;
        var start = 0;
        var end = start + LENGTH;
        var blob;
        var blob_num = 1;
        var is_stop = 0;
        //对外方法，传入文件对象
        this.addFileAndSend = function(that){
            var file = that.files[0];
            blob = cutFile(file);
            sendFile(blob,file);
            blob_num  += 1;
        };
        //切割文件
        function cutFile(file){
            var file_blob = file.slice(start,end);
            start = end;
            end = start + LENGTH;
            return file_blob;
        }
        //上传完成  修改状态
        function setButAndPath(path){
            var upload_obj = $('#upload'+video_num);
            var btn_obj = upload_obj.find('.cancelling');
            var input = '<input type="hidden" name="upload_file[]" value="'+path+'">';
            btn_obj.html('删除');
            btn_obj.removeClass('cancelling');
            btn_obj.removeClass('layui-btn-warm');
            btn_obj.addClass('layui-btn-danger');
            btn_obj.addClass('delete-video');
            btn_obj.parent().append(input);
        }
        function setResult(res,total_blob_num) {
            var progress;
            var progressObj = jdt_s;

            if(total_blob_num == 1){
                progress = '100%';
            }else{
                progress =  parseInt(Math.min(100,(blob_num/total_blob_num)* 100 )) +'%';
            }
            progressObj.css('width',progress);
            progressObj.html(progress);
            Timeout = setTimeout(function(){
                if(start < file.size && is_stop === 0){
                    blob = cutFile(file);
                    sendFile(blob,file);
                    blob_num  += 1;
                }else{
                    if(res.code == 2){
                        setButAndPath(res.file_path);
                        layer.msg(res.msg);
                    }else{
                        layer.msg(res.msg);
                    }
                    $('#VideoName').val('');
                    xhr = undefined;
                    form_data = undefined;
                }
            },1000);
        }
        //发送文件
        function sendFile(blob,file) {
            // var xhr = new XMLHttpRequest();
            var form_data = new FormData();
            var total_blob_num = Math.ceil(file.size / LENGTH);
            form_data.append('file', blob);
            form_data.append('blob_num', blob_num);
            form_data.append('total_blob_num', total_blob_num);
            form_data.append('file_name', fileNameTime + file.name.substr(file.name.lastIndexOf(".")));

            $.ajax({
                dataType: 'json',
                type: 'POST',
                async: false,
                data: form_data,
                processData: false, // 使数据不做处理
                contentType: false, // 不要设置Content-Type请求头
                success: function (res) {

                    var progress;
                    var progressObj = jdt_s;

                    if(total_blob_num == 1){
                        progress = '100%';
                    }else{
                        progress =  parseInt(Math.min(100,(blob_num/total_blob_num)* 100 )) +'%';
                    }
                    progressObj.css('width',progress);
                    progressObj.html(progress);
                    Timeout = setTimeout(function(){
                        if(start < file.size && is_stop === 0){
                            blob = cutFile(file);
                            sendFile(blob,file);
                            blob_num  += 1;
                        }else{
                            if(res.code == 2){
                                setButAndPath(res.file_path);
                                layer.msg(res.msg);
                            }
                            $('#VideoName').val('');
                            xhr = undefined;
                            form_data = undefined;
                        }
                    },1000);
                },
                error: function (response) {
                     layer.msg('上传失败！');
                     clearTimeout(Timeout);
                     _this.parents('tr').remove();
                }
            });
        }
    }
</script>