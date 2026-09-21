<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Url;
?>
<div class="x-body layui-anim layui-anim-up">
    <form class="layui-form">
        <div class="layui-form-item">
            <label class="layui-form-label">
                工序:
            </label>
            <div class="layui-input-inline">
                <input type="text" autocomplete="off" name="nowspecname"  style="width: 400px;" lay-verify="required"   class="layui-input">
            </div>
        </div>
        <div class="layui-form-item layui-form-text">
            <label for="desc" class="layui-form-label">
                <span class="x-red">*</span>处理方法:
            </label>
            <div class="layui-input-block">
                <textarea placeholder="请填写异常" lay-verify="required" name="method" class="layui-textarea" style="width: 400px;height: 100px;"></textarea>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>状态:
            </label>
            <div class="layui-input-block">
                <input type="radio" value="1" name="status" title="启用" checked="">
                <input type="radio" value="2" name="status" title="禁用">
            </div>
        </div>
        <div class="layui-form-item" style="margin-top: 50px;">
            <label for="" class="layui-form-label"></label>
            <button class="layui-btn" lay-filter="submit" lay-submit="">
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
        form.on('submit(submit)', function(data){
            layer.load(3);
            $.post('<?= Url::to(['fccfp-list/expire-create']) ?>', data.field, function (res) {
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

