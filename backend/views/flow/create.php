<?php
use yii\helpers\Url;
?>
<div class="x-body layui-anim layui-anim-up">
    <form class="layui-form">
        <div class="layui-form-item">
            <label for="name" class="layui-form-label">
                <span class="x-red">*</span>流程名称
            </label>
            <div class="layui-input-inline">
                <input type="text" name="name" lay-verify="required" autocomplete="off" class="layui-input" style="width: 800px;">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>审批人:
            </label>
            <div class="layui-input-inline" style="width: 800px;">
                <select name="user_id" lay-verify="required" xm-select="selectId" xm-select-search="">
                    <option value="">直接选择或搜索选择</option>
                    <?php foreach ($connectUser as $value): ?>
                        <option value="<?= $value['id'] ?>" ><?= $value['chinese_name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label for="pwd" class="layui-form-label">
                <span class="x-red">*</span> 备注
            </label>
            <div class="layui-input-block">
                <input type="text" name="remark" lay-verify="required" autocomplete="off" class="layui-input" style="width: 800px;">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span> 状态
            </label>
            <div class="layui-input-block">
                <input type="radio" value="1" name="status" lay-skin="primary" title="启用" checked="">
                <input type="radio" value="2" name="status" lay-skin="warning" title="禁用">
            </div>
        </div>
        <div class="layui-form-item">
            <label for="" class="layui-form-label"></label>
            <button  class="layui-btn" lay-filter="add" lay-submit="">
                增加
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
        form.on('submit(add)', function(data){
            layer.load(3);
            $.post('<?= Url::to(['flow/create']) ?>', data.field, function (res) {
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