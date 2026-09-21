<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Url;
?>
<div class="x-body layui-anim layui-anim-up">
    <form class="layui-form">
        <input type="hidden" name="bill_id" value="<?= $bill_id ?>">
        <div class="layui-form-item">
            <label class="layui-form-label">
                单据号:
            </label>
            <div class="layui-input-inline">
                <input type="text" autocomplete="off" value="<?= $document_no ?>" style="width: 400px;" readonly="readonly"  class="layui-input">
            </div>
        </div>
        <div class="layui-form-item layui-form-text">
            <label for="desc" class="layui-form-label">
                <span class="x-red">*</span>请填写异常:
            </label>
            <div class="layui-input-block">
                <textarea placeholder="请填写异常" lay-verify="required" name="abnormal" class="layui-textarea" style="width: 400px;height: 100px;"></textarea>
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
            $.post('<?= Url::to(['shelf-materials-bill/bill-abnormal-create']) ?>', data.field, function (res) {
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

