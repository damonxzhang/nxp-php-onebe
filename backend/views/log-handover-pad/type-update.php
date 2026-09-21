<?php

use yii\helpers\Url;
?>
<link href="<?= Url::to('@web/lib/layui-v2.11.0/css/layui.css') ?>" rel="stylesheet">
<div class="x-body layui-anim layui-anim-up">
    <div class="layui-form"  style="margin: 16px;">
        <input type="hidden" name="id" value="<?= $model['id'] ?>">
        <div class="demo-login-container">
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label-layer" style="float: left;display: block;padding: 9px 15px;width: 80px;font-weight: 400;line-height: 20px;text-align: right;">信息内容</label>
                <div class="layui-input-block">
                    <textarea placeholder="请输入信息内容" name="content" class="layui-textarea" lay-verify="required" > <?php echo $model['content']; ?></textarea>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label-layer" style="float: left;display: block;padding: 9px 15px;width: 80px;font-weight: 400;line-height: 20px;text-align: right;">物料类型：</label>
                <div class="layui-input-block">
                    <select name="type_id" lay-verify="required">
                        <option value=""></option>
                        <?php foreach ($logHandoverTypeArr as $key => $value): ?>
                            <option value="<?= $key ?>" <?= $key == $model['type_id'] ? 'selected' : '' ?>><?= $value ?></option>
                        <?php endforeach; ?>
                    </select>
                        
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label-layer" style="float: left;display: block;padding: 9px 15px;width: 80px;font-weight: 400;line-height: 20px;text-align: right;">批次类型：</label>
                <div class="layui-input-block">
                    <select name="batch_id" lay-verify="required">
                        <option value=""></option>
                        <?php foreach ($logHandoverBatchArr as $key => $value): ?>
                            <option value="<?= $key ?>" <?= $key == $model['batch_id'] ? 'selected' : '' ?>><?= $value ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label-layer" style="float: left;display: block;padding: 9px 15px;width: 80px;font-weight: 400;line-height: 20px;text-align: right;">状态是否延续：</label>
                <div class="layui-input-block">
                <input type="radio" name="status" value="1" title="是" <?= $model['status'] == 1 ? 'checked' : '' ?>>
                <input type="radio" name="status" value="2" title="否" <?= $model['status'] == 2 ? 'checked' : '' ?>>
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-input-block" style="margin-bottom: 6px; text-align: right;">
                    <button type="button" class="layui-btn layui-btn-primary layui-bg-orange" lay-on="return-button">返回</button>
                    <button type="submit" class="layui-btn" lay-submit lay-filter="type-update-val">提交</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= Url::to('@web/lib/jquery-3.7.1/jquery-3.7.1.min.js') ?>"></script>
<script src="<?= Url::to('@web/lib/layui-v2.11.0/layui.js') ?>"></script>

<script>
    layui.use(['form','layer','util'], function(){
        $ = layui.jquery;
        var util = layui.util;
        var form = layui.form ,layer = layui.layer;
        // 事件
        util.on('lay-on', {
            'return-button': function(){
                var index = parent.layer.getFrameIndex(window.name);
                parent.layer.close(index);
            }
        });
        //监听提交
        form.on('submit(type-update-val)', function(data){
            layer.load(3);
            $.post('<?= Url::to(['log-handover-pad/type-update']) ?>', data.field, function (res) {
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


