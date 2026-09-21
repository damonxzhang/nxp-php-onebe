<?php
use yii\helpers\Url;

?>
<div class="x-body layui-anim layui-anim-up">
    <form class="layui-form">
        <div class="layui-form-item">
            <label for="pwd" class="layui-form-label">
                类型
            </label>
            <div class="layui-input-inline"  style="width: 800px;">
                <select name="flow_id">
                    <?php foreach ($flow as $list): ?>
                        <option value="<?= $list['id'] ?>"><?= $list['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label for="nickname" class="layui-form-label">标题</label>
            <div class="layui-input-inline">
                <input type="text" lay-verify="required" name="title" autocomplete="off" class="layui-input" style="width: 800px;">
            </div>
        </div>
        <div class="layui-form-item layui-form-text">
            <label for="email" class="layui-form-label">
                内容:
            </label>
            <div class="layui-input-block">
                <textarea placeholder="请输入内容"  maxlength="255" lay-verify="required" name="content_data" class="layui-textarea" style="height:300px;width: 800px;"></textarea>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                状态
            </label>
            <div class="layui-input-block">
                <input type="radio" value="0" name="status" lay-skin="primary" title="草稿" checked="">
                <input type="radio" value="1" name="status" lay-skin="warning" title="提交审批">
            </div>
        </div>
        <div class="layui-form-item">
            <label for="" class="layui-form-label">
            </label>
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
            $.post('<?= Url::to(['flow-approval/create']) ?>', data.field, function (res) {
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