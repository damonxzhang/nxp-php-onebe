<?php
use yii\helpers\Url;
?>
    <div class="x-body layui-anim layui-anim-up">
        <form class="layui-form">
            <div class="layui-form-item">
                <input type="hidden" name="id" value="<?= $model['id'] ?>">
                <label class="layui-form-label"> <span class="x-red">*</span>物料种类名称:</label>
                <div class="layui-input-inline"> 
                    <textarea lay-verify="required" name="title" class="layui-textarea" style="width: 400px;height: 60px;"><?php echo $model['title']; ?></textarea>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">
                    <span class="x-red">*</span> 所属分部:
                </label>
                <div class="layui-input-inline">
                    <select name="section" lay-verify="required">
                        <?php foreach ($sectionArr as $key => $value): ?>
                            <option value="<?= $key ?>" <?= $key == $model['section'] ? 'selected' : '' ?>><?= $value ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"> <span class="x-red">*</span>状态:</label>
                <div class="layui-input-block">
                    <input type="radio" value="1" name="status"  title="启用" <?= $model['status'] == 1 ? 'checked' : '' ?>>
                    <input type="radio" value="2" name="status"  title="禁用" <?= $model['status'] == 2 ? 'checked' : '' ?>>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"> <span class="x-red">*</span>是否同步Audit:</label>
                <div class="layui-input-block">
                    <input type="radio" value="1" name="is_audit"  title="不同步" <?= $model['is_audit'] == 1 ? 'checked' : '' ?>>
                    <input type="radio" value="2" name="is_audit"  title="同步"   <?= $model['is_audit'] == 2 ? 'checked' : '' ?>>
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
                $.post('<?= Url::to(['log-handover/type-update']) ?>', data.field, function (res) {
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