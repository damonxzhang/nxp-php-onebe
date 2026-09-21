<?php

use backend\models\UserInfo;
use backend\models\Gemba;
use yii\helpers\Url;
?>
<div class="x-body layui-anim layui-anim-up">
    <form class="layui-form">
        <div class="layui-row">
            <div class="layui-col-sm6">
                <div class="layui-form-item">
                    <label class="layui-form-label">
                        卡号:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text" name="number" id="autofocus" required lay-verify="required" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-form-mid layui-word-aux">
                        <span class="x-red">*</span> 输入卡号
                    </div>
                </div>
                <div class="layui-form-item" pane="">
                    <label class="layui-form-label">
                        状态:
                    </label>
                    <div class="layui-input-block">
                        <input type="radio" name="status" value="1" title="生效" checked="">
                        <input type="radio" name="status" value="2" title="失效">
                    </div>
                </div>

            </div>
        </div>
        <div class="layui-form-item">
            <label for="" class="layui-form-label"></label>
            <button class="layui-btn" lay-filter="add" lay-submit="" id="submitform" onclick="subform()" type="button">
                增加
            </button>
        </div>
    </form>
</div>

<?php $this->beginBlock('footer') ?>
<script>
    //提交数据
    layui.use(['form','layer'], function(){
        $ = layui.jquery;
        var form = layui.form ,layer = layui.layer;
        //监听提交
        form.on('submit(add)', function(data){
            layer.load(3);
            $.post('<?= Url::to(['user-temp-card/create']) ?>', data.field, function (res) {
                layer.closeAll();
                if (res.status === 200) {
                    layer.msg(res.msg, {icon: 1,time: 1500}, function () {
                        var index = parent.layer.getFrameIndex(window.name);
                        parent.layer.close(index);
                        parent.location.reload();
                    })
                } else {
                    layer.msg(res.msg, {icon: 2, time: 1500});
                }
            }, 'json');
            return false;
        });
    });
</script>
<?php $this->endBlock() ?>