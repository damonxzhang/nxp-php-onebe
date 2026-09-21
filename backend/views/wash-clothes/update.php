<?php
use yii\helpers\Url;
?>
    <div class="x-body layui-anim layui-anim-up">
        <form class="layui-form">
            <div class="layui-form-item">
                <input type="hidden" name="id" value="<?= $model['id'] ?>">
                <label class="layui-form-label">
                 姓名:
                </label>
                <div class="layui-input-inline" style="width: 800px;">
                    <input type="text" name="user_name" required="" lay-verify="required"
                           autocomplete="off" class="layui-input" value="<?= $model['user_name'] ?>">
                </div>
                <div class="layui-form-mid layui-word-aux">
                    <span class="x-red">*</span>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">
                RFID:
                </label>
                <div class="layui-input-inline" style="width: 800px;">
                    <input type="text" name="str" required="" lay-verify="required"
                           autocomplete="off" class="layui-input" value="<?= $model['str'] ?>">
                </div>
                <div class="layui-form-mid layui-word-aux">
                <span class="x-red">*</span>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">
                    PID:
                </label>
                <div class="layui-input-inline" style="width: 800px;">
                    <input type="text" name="pid" required="" lay-verify="required"
                           autocomplete="off" class="layui-input" value="<?= $model['pid'] ?>">
                </div>
                <div class="layui-form-mid layui-word-aux">
                <span class="x-red">*</span>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">种类</label>
                <div class="layui-input-block">
                    <input type="radio" value="1" name="type" lay-skin="primary" title="帽子" <?= $model['type'] == 1 ? 'checked' : '' ?>>
                    <input type="radio" value="2" name="type" lay-skin="warning" title="衣服" <?= $model['type'] == 2 ? 'checked' : '' ?>>
                    <input type="radio" value="3" name="type" lay-skin="warning" title="裤子" <?= $model['type'] == 3 ? 'checked' : '' ?>>
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
                $.post('<?= Url::to([$this->context->id . '/update'])?>', data.field, function (res) {
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