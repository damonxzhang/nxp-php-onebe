<?php
use yii\helpers\Url;
?>
<div class="x-body layui-anim layui-anim-up">
    <form class="layui-form">

        <div class="layui-form-item layui-form-text" >
            <label for="desc" class="layui-form-label">
                <span class="x-red">*</span>用户名称:
            </label>
            <div class="layui-input-block">
                <div class="layui-input-inline" style="width:600px">
                    <input type="text" readonly="readonly" value="<?=$model->chinese_name?>" autocomplete="off" lay-verify="required" class="layui-input">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>选择权限:
            </label>
            <div class="layui-input-inline"  style="width:600px">
                <select name="gemba_role" >
                    <option value="" >无权限</option>

                    <?php foreach (\backend\models\Gemba::$showGeambaRole as $key => $value): ?>
                        <option value="<?= $key ?>"  <?= $model->gemba_role == $key? 'selected':''?> ><?= $value ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>选择小组:
            </label>
            <div class="layui-input-inline" style="width:600px">
                <select name="team" name="team">
                    <option value="" >选择小组</option>
                    <?php foreach ($gembaTeam as $key => $value): ?>
                        <option value="<?= $key ?>"  <?= $model->team == $key? 'selected':''?>  ><?= $value ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="layui-form-item"  style="margin-top: 50px;">
            <label for="" class="layui-form-label">
            </label>
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
            $.post('<?= Url::to(['update','id' => $model->id]) ?>', data.field, function (res) {
                layer.closeAll();
                if (res.status === 200) {
                    layer.msg(res.msg, {icon: 1,time: 1500}, function () {
                        var index = parent.layer.getFrameIndex(window.name);
                        parent.layer.close(index);
                        parent.location.reload();
                    })
                } else {
                    layer.msg(res.msg, {icon: 2, time: 1500})
                }
            }, 'json');
            return false;
        });
    });
</script>
<?php $this->endBlock() ?>
