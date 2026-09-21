<?php
use yii\helpers\Url;
use backend\models\FlowNode;
?>
    <div class="x-body layui-anim layui-anim-up">
        <form class="layui-form">
            <input type="hidden" name ="id" value="<?= $model['id'] ?>" >
            <div class="layui-form-item">
                <label for="nickname" class="layui-form-label" >
                    流程名称:
                </label>
                <div class="layui-input-inline" style="width: 800px;">
                    <input type="text" value="<?= $model['name'] ?>" readonly="readonly" autocomplete="off" lay-verify="required" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">
                    审批人:
                </label>
                <div class="layui-input-inline" style="width: 800px;">
                    <input type="text" value="<?php echo (new FlowNode())->getUserName($model['id']) ?>"  autocomplete="off" class="layui-input" readonly="readonly">
                </div>
            </div>
            <div class="layui-form-item" pane="">
                <label class="layui-form-label">
                    状态:
                </label>
                <div class="layui-input-block">
                    <input type="radio" name="status" value="1" title="启用" <?= $model['status'] == 1 ? 'checked' : '' ?>>
                    <input type="radio" name="status" value="2" title="禁用" <?= $model['status'] == 2 ? 'checked' : '' ?>>
                </div>
            </div>
            <div class="layui-form-item">
                <label for="" class="layui-form-label"></label>
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
                $.post('<?= Url::to([$this->context->id .'/update']) ?>', data.field, function (res) {
                    layer.closeAll();
                    if (res.status === 200) {
                        layer.msg(res.msg, {icon: 1, time: 1500}, function () {
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