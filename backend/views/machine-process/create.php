<?php

use yii\helpers\Url;
?>
<div class="x-body layui-anim layui-anim-up">
    <form class="layui-form">
        <div class="layui-form-item">
            <label class="layui-form-label">
                工序:
            </label>
            <div class="layui-input-inline">
                <input type="text" name="name" value="" lay-verify="required" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item" pane="">
            <label class="layui-form-label">
                状态:
            </label>
            <div class="layui-input-block">
                <input type="radio" name="status" value="1" checked title="启用" >
                <input type="radio" name="status" value="0" title="禁用" >
            </div>
        </div>

        <div class="layui-form-item">
            <label for="" class="layui-form-label"></label>
            <button class="layui-btn" lay-filter="update" lay-submit="">
                保存
            </button>
        </div>
    </form>
</div>
<?php $this->beginBlock('footer') ?>
<script>
    layui.use(['form','layer'], function(){
        $ = layui.jquery;
        var form = layui.form ,layer = layui.layer;
        //监听提交
        form.on('submit(update)', function(data){
            layer.load(3);
            $.post('<?= Url::to(['machine-process/create']) ?>', data.field, function (res) {
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
