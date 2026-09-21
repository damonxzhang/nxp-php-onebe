

var url = 'upload-video';
var cancel_url = 'cancel-upload';

var fileNameTime = '';
var video_num = 0;
var jdt_s = '';
var video_upload = '';
var Timeout = '';
$('.upload-file').click(function () {

    $('#video_file').trigger('click');
});
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
        $.post(cancel_url,{file_name:fileNameTime},function (res) {

        },'json');
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
        ,'<a href="javascript:;" class="layui-btn layui-btn-xs layui-btn-warm cancelling">取消</a>'
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
        form_data.append('file_name', file.name.substring(0,file.name.lastIndexOf(".")) +fileNameTime + file.name.substr(file.name.lastIndexOf(".")));

        $.ajax({
            url:url,
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
                $('#upload'+video_num).remove();
            }
        });
    }
}