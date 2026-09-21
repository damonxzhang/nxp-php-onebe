<?php

use yii\helpers\Url;
?>
<style>
    .coach-price{display: none}
    .input-but{display: inline-flex;margin-top: 20px;}
    #canvas{display: none}
    #showVideo{display: none}
    #input-picture{width:100%;}
</style>
<div class="x-body layui-anim layui-anim-up">
    <div class="ibox float-e-margins">
        <div class="ibox-content img-content">
            <form class="layui-form">
                <div class="form-group " id="input-picture">
                    <div class="img-box" id="results">
                        <input id="qrcode_key" type="hidden" value="">
                        <input name="img_picture" id="img_picture" type="hidden" value=""/>
                        <canvas id="canvas" width="320" height="240"></canvas>
                        <h2 style="margin-top: 10px" type="hidden" id="qrcode">设备机台号:<span id="spantitle"></span></h2>
                    </div>
                </div>
                <div class="form-group">
                    <video id="video" controls></video>
                </div>
                <div class="form-group ">
                    <div class="input-but">
                        <button type="button" class="layui-btn" id="showVideo">
                            <i class="layui-icon">&#xe617;</i>重拍
                        </button>
                        <button type="button" class="layui-btn" id="capture">
                            <i class="layui-icon">&#xe65d;</i>拍摄
                        </button>
                        <button class="layui-btn" lay-filter="update" lay-submit="">
                            <i class="layui-icon">&#x1005;</i>确定
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $this->beginBlock('footer') ?>
<script>

    //提交图片
    layui.use(['form','layer'], function(){
        var form = layui.form ,layer = layui.layer;
        //监听提交
        form.on('submit(update)', function(data){
            var img_picture = $('#img_picture').val();//图片值
            if(!img_picture){
                layer.msg('请先拍照', {icon: 2, time: 1500});
                return false;
            }
            var qrcode_key = $('#qrcode_key').val();//二维码
            if(qrcode_key == 'Error'){
                layer.msg('二维码错误', {icon: 2, time: 1500});
                return false;
            }
            parent.ChooseQrcodeValues(qrcode_key); //这是父页面函数
            var index = parent.layer.getFrameIndex(window.name);
            parent.layer.close(index);
            return false;
        });
    });

    //访问用户媒体设备的兼容方法
    function getUserMedia(constraints, success, error) {
        if (navigator.mediaDevices.getUserMedia) {
            //最新的标准API
            navigator.mediaDevices.getUserMedia(constraints).then(success).catch(error);
        } else if (navigator.webkitGetUserMedia) {
            //webkit核心浏览器
            navigator.webkitGetUserMedia(constraints,success, error)
        } else if (navigator.mozGetUserMedia) {
            //firfox浏览器
            navigator.mozGetUserMedia(constraints, success, error);
        } else if (navigator.getUserMedia) {
            //旧版API
            navigator.getUserMedia(constraints, success, error);
        }
    }

    function success(stream) {
        //兼容webkit核心浏览器
        let CompatibleURL = window.URL || window.webkitURL;
        //将视频流设置为video元素的源
        console.log(stream);
        //video.src = CompatibleURL.createObjectURL(stream);
        video.srcObject = stream;
        video.play();
    }

    function error(error) {
        alert('访问摄像头失败!');
    }

    //从 canvas 提取图片 image
    function convertCanvasToImage(canvas) {
        //新Image对象，可以理解为DOM
        var image = new Image();
        // canvas.toDataURL 返回的是一串Base64编码的URL
        // 指定格式 PNG
        image.src = canvas.toDataURL("image/png");
        return image;
    }

    function getnavigator() {
        if (navigator.mediaDevices.getUserMedia || navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia) {
            //获取video宽高
            var v_height,v_width;
            var myvObj = document.getElementById("video");
            myvObj.addEventListener("loadedmetadata", function () {
                v_height = this.videoHeight;
                v_width =this.videoWidth;
                $('#canvas').attr('width',v_width);
                $('#canvas').attr('height',v_height);
            });

            //调用用户媒体设备, 访问摄像头
            //getUserMedia({video : {width: 320, height: 240}}, success, error);
            getUserMedia({  audio: true,video : {width: 320, height: 240}}, success, error);//后置拍照
        } else {
            alert('不支持访问用户媒体');
        }
    }

    getnavigator();
    function showVideo(){
        $('#results').find('img').remove();
        $('#canvas').css('display','none');
        $('#video').css('display','block');
        $('#showVideo').css('display','none');
        $('#qrcode').css('display','none');
        getnavigator();
    }

    function showpicture(picture) {
        if($('#results').find('img').attr('src')){
            $('#results').find('img').attr('src',picture);
        }else{
            $('#results').append('<img src="'+picture+'"/>');
        }
        $('#video').css('display','none');
        $('#canvas').css('display','none');
        $('#showVideo').show();
        $('.picture').val(1);
    }

    function hidepicture() {
        $('#results').find('img').remove();
        getnavigator();
        $('#video').css('display','block');
        $('#canvas').css('display','none');
        $('#showVideo').css('display','none');
    }

    $('#showVideo').click(function () {
        showVideo();
    });


    //文件转blob格式
    function getObjectURL(file) {
        var url = null ;
        if (window.createObjectURL!=undefined) { // basic
            url = window.createObjectURL(file) ;
        } else if (window.URL!=undefined) { // mozilla(firefox)
            url = window.URL.createObjectURL(file) ;
        } else if (window.webkitURL!=undefined) { // webkit or chrome
            url = window.webkitURL.createObjectURL(file) ;
        }
        return url ;
    }

    //base64转图片
    const base64ToFile = (base64, fileName) => {
        let arr = base64.split(','), type = arr[0].match(/:(.*?);/)[1], bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
        while(n--){
            u8arr[n] = bstr.charCodeAt(n);
        }
        return new File([u8arr], fileName, {type});
    };

    document.getElementById('capture').addEventListener('click', function () {
        let video = document.getElementById('video');
        let canvas = document.getElementById('canvas');
        let context = canvas.getContext('2d');
        context.drawImage(video, 0, 0);
        //获取网页中的canvas对象
        var mycans=$('canvas')[0];
        //调用convertCanvasToImage函数将canvas转化为img形式
        var img=convertCanvasToImage(mycans);
        if(img.src){
            $('#results').find('#img_picture').val(img.src);
            $('#video').css('display','none');
            $('#canvas').css('display','block');
            $('#qrcode').css('display','block');
            $('#showVideo').show();
        }


        //将二维码转为字符串
        const file = base64ToFile(img.src, 'fileName');//base64转文件
        var strsrc=getObjectURL(file);//转为blob字符串格式
        qrcode.decode(strsrc);
        qrcode.callback = function (imgMsg) {
            // 获取地址传给span
            var divA = document.getElementById("spantitle");
            divA.innerHTML =  imgMsg;
            $('#results').find('#qrcode_key').val(imgMsg);
        }
    });

</script>
<?php $this->endBlock() ?>


