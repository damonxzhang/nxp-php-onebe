<?php
use yii\helpers\Url;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title>登录</title>
<link href="<?= Url::to('@web/lib/layui-v2.11.0/css/layui.css') ?>" rel="stylesheet">
<style>
  html,
  body {
      height: 100%;
  }

  body {
      background-color: #002a56;
    background-image: radial-gradient(#0036b1,#040529);
  }

  .login-form {
      background-color: #fff;
      width: 320px;
      padding: 50px;
      position: relative;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
  }
  .layui-panel {
      border-radius: 7px;
  }
  .login-form .login-title {
      margin-bottom: 20px;
      text-align: center;
  }

  .login-form input {
      padding-left: 38px;
  }

  .login-form .layui-form-item .layui-icon {
      display: inline-block;
      height: 38px;
      width: 38px;
      line-height: 38px;
      text-align: center;
      position: absolute;
  }

  .login-form input[name="captcha"] {
      width: 195px;
      display: inline-block;
      margin-right: 1px;
  }

  #particles-js {
      width: 100%;
      height: 100%;
      position: absolute;
      top: 0;
      left: 0;
      overflow: hidden;
  }

  #captcha {
      width: 120px;
      height: 40px;
  }

</style>
</head>
<body>
<form class="layui-form login-form layui-panel" autocomplete="off">
 
  <div style="display:flex;justify-content: center;align-items: center;height: 200px;">
    <img src="/file/nxp.jpg" alt="logo" style="width: 100%;"> 
  </div>
  <h1 class="login-title"><span>欢迎使用日志交接系统</span> </h1>
  <div class="layui-form-item"> <i class="layui-icon layui-icon-username"></i>
    <input type="text" name="pid" placeholder="请输入PID" class="layui-input" lay-verify="required" lay-reqtext="请输入PID">
  </div>
  <div class="layui-form-item"> <i class="layui-icon layui-icon-password"></i>
    <input type="password" name="password" placeholder="请输入密码" class="layui-input" lay-verify="required" lay-reqtext="请输入密码">
  </div>
  <div class="layui-form-item">
    <button class="layui-btn layui-btn-normal layui-btn-fluid" id="submit" type="button" lay-submit="" lay-filter="login"> 登录 </button>
  </div>
</form>
<script src="<?= Url::to('@web/lib/jquery-3.7.1/jquery-3.7.1.min.js') ?>"></script>
<script src="<?= Url::to('@web/lib/layui-v2.11.0/layui.js') ?>"></script>
<script>
    layui.use(['form','layer'], function(){
        $ = layui.jquery;
        var form = layui.form ,layer = layui.layer;
        //监听提交
        form.on('submit(login)', function(data){
            layer.load(3);
            $.post('<?= Url::to(['log-handover-pad/login']) ?>', data.field, function (res) {
                layer.closeAll();
                if (res.status === 200) {
                    layer.msg(res.msg, {icon: 1,time: 1500}, function () {
                        window.location.href = "<?= Url::to(['log-handover-pad/index']) ?>";
                    })
                } else {
                    layer.msg(res.msg, {icon: 2, time: 1500});
                }
            }, 'json');
            return false;
        });
    });
</script>
</body>
</html>