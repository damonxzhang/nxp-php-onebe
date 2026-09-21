<?php
use yii\helpers\Url;
use backend\models\Appbutton;


// $userId = $_GET['userId'];
$pid = $_GET['pid'];

$query = Appbutton::find();
$queryData = $query->asArray()->all();

?>
<div class="login layui-anim layui-anim-up">
    <div class="message">APP 跳转链接</div>
    <div id="darkbannerwrap"></div>
    <form method="post" class="layui-form" >
        <hr class="hr20" >
        <?php foreach ($queryData as $key => $value): ?>
            <hr class="hr15">
            <p style="text-align: center">
                <a style="display: inline-block;width: 100px;height: 40px;text-align: center"  href="<?php echo $value['url'].$pid; ?>">
                    <font size="4"><?php echo $value['name']; ?></font>
                </a>
            </p>
            <HR style="FILTER:alpha(opacity=100,finishopacity=0,style=3)" width="80%"color=#987cb9 SIZE=3>
        <?php endforeach;  ?>
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
