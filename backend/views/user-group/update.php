<?php
use yii\helpers\Url;
?>
    <div class="x-body layui-anim layui-anim-up">
        <form class="layui-form">
            <div class="layui-form-item">
                <input type="hidden" name="id" value="<?= $model['id'] ?>">
                <label class="layui-form-label">
                    分组名称:
                </label>
                <div class="layui-input-inline" style="width: 800px;">
                    <input type="text" name="name" required="" lay-verify="required"
                           autocomplete="off" class="layui-input" value="<?= $model['name'] ?>">
                </div>
                <div class="layui-form-mid layui-word-aux">
                    <span class="x-red">*</span> 分组名称必须唯一
                </div>
            </div>
            <div class="layui-form-item">
                <label for="pwd" class="layui-form-label">
                    <span class="x-red">*</span> 交接人:
                </label>
                <div class="layui-input-inline" style="width: 800px;">
                    <select name="group_id" lay-verify="required" xm-select="selectId" xm-select-search="">
                        <option value="">直接选择或搜索选择</option>
                        <?php foreach ($connectUser as $value): ?>
                            <option value="<?= $value['id'] ?>" <?= in_array($value['id'], $acceptUserArr) ? 'selected' : ''; ?>><?= $value['chinese_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">状态</label>
                <div class="layui-input-block">
                    <input type="radio" value="1" name="status" lay-skin="primary" title="启用" <?= $model['status'] == 1 ? 'checked' : '' ?>>
                    <input type="radio" value="2" name="status" lay-skin="warning" title="禁用" <?= $model['status'] == 2 ? 'checked' : '' ?>>
                </div>
            </div>
            <div class="layui-form-item">
                <label for="" class="layui-form-label">
                </label>
                <button  class="layui-btn" lay-filter="update" lay-submit="">
                    更新
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
                $.post('<?= Url::to(['user-group/update']) ?>', data.field, function (res) {
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