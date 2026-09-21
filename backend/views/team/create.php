<?php
use yii\helpers\Url;
?>
    <div class="x-body layui-anim layui-anim-up">
        <form class="layui-form">
            <div class="layui-form-item">
                <label class="layui-form-label">
                    小组名称:
                </label>
                <div class="layui-input-inline">
                    <input type="text" name="teamName" id="autofocus" required="" lay-verify="required"
                           autocomplete="off" class="layui-input">
                </div>
                <div class="layui-form-mid layui-word-aux">
                    <span class="x-red">*</span>小组名称必须唯一,多个名称可用 ‘,’ 隔开。例：LD Cell A1，	LD Cell A1
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">状态:</label>
                <div class="layui-input-block">
                    <input type="radio" value="1" name="status" lay-skin="primary" title="启用" checked="">
                    <input type="radio" value="2" name="status" lay-skin="warning" title="禁用">
                </div>
            </div>
            <div class="layui-form-item">
                <label for="" class="layui-form-label">
                </label>
                <button  class="layui-btn" lay-filter="add" lay-submit="">
                    新增
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
                $.post('<?= Url::to(['team/create']) ?>', data.field, function (res) {
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