<?php
use yii\helpers\Url;
?>
<div class="login layui-anim layui-anim-up">
    <div class="message">One BE- 登录</div>
    <div id="darkbannerwrap"></div>
    <form method="post" class="layui-form" >
        <input type="hidden" name="_csrf-frontend" value="<?= Yii::$app->request->csrfToken ?>">
        <div id="id-login">
            <input name="name" placeholder="用户名" autofocus id="name" type="text" lay-verify="required" class="layui-input" >
            <hr class="hr15">
        </div>
        <div id="card-login" style="display: none">
            <hr class="hr15">
            <input name="card" placeholder="识别证"  type="text" lay-verify="false" class="layui-input" >
        </div>

        <div id="pid-login" style="display: none">
            <input name="pid" placeholder="PID" id="pid" type="text" lay-verify="false" class="layui-input" >
            <hr class="hr15">

        </div>
        <div id="pass">
            <input name="password" lay-verify="required" placeholder="密码"  type="password" class="layui-input">
        </div>

        <hr class="hr15">
        <input type="radio" name="logType" title="账号登录" value="1" lay-filter="logType" checked>
        <input type="radio" name="logType" title="PID登录"  value="3" lay-filter="logType" >
        <input type="radio" name="logType" title="扫卡登录" value="2" lay-filter="logType" >

        <hr class="hr20" >
        <hr class="hr15">
        <input value="登录" lay-submit="" lay-filter="login" style="width:100%;" type="submit">
        <hr class="hr20" >
    </form>
</div>

<?php $this->beginBlock('footer') ?>
<script>
    $('body').addClass('login-bg');
    $(function  () {
        layui.use('form', function(){
            var form = layui.form;
            form.on('submit(login)', function(data){
                layer.load(3);
                $.post('<?= Url::to(['site/login']) ?>', data.field, function (res) {
                    layer.closeAll();
                    if (res.status === 200) {
                        layer.msg(res.msg, {icon: 1,time: 1500}, function () {
                            location.href = '<?= Url::to(['/']) ?>';
                        })
                    } else {
                        layer.msg(res.msg, {icon: 2})
                    }
                }, 'json');
                return false;
            });
        });

    });

    layui.use('form', function(){
        let form = layui.form;
        let idLogin = $('#id-login'),
            cardLogin = $('#card-login'),
            pass = $('#pass'),
            pidLogin = $('#pid-login');

        form.on('radio(logType)', function(data){
            if(data.value == 1){
                idLogin.show();
                pass.show();
                cardLogin.hide();
                pidLogin.hide();
                required(idLogin,'required');
                required(pass,'required');

                required(cardLogin,false);
                required(pidLogin,false);
            }else if(data.value == 2){
                idLogin.hide();
                pidLogin.hide();
                pass.hide();
                cardLogin.show();
                required(cardLogin,'required');
                required(pass,false);
                required(idLogin,false);
                required(pidLogin,false);

            }else if(data.value == 3){
                idLogin.hide();
                pidLogin.show();
                pass.show();
                cardLogin.hide();
                required(pidLogin,'required');
                required(pass,'required');
                required(idLogin,false)
                required(cardLogin,false)
            }
        });

        //修改必填项
        function required(obj,status){
            $.each(obj,function(){
                let _inpit = $(this).children('input');
                _inpit.attr('lay-verify',status)
                let iName = _inpit.attr('name');
                if(iName == 'name' || iName == 'pid' || iName == 'card' ){
                    _inpit.focus();
                }
            })
        }
    });
    setTimeout(function(){
        $("input[name='name']").focus();
    },100)

</script>
<?php $this->endBlock() ?>
