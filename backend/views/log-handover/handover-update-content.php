<?php

use yii\helpers\Url;
?>

<div class="x-body layui-anim layui-anim-up">
    <div class="layui-form"  style="margin: 16px;">
        <input type="hidden" name="id" value="<?= $model['id'] ?>">
        <div class="demo-login-container">
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label-layer">信息内容</label>
                <div class="layui-input-block">
                    <textarea placeholder="请输入信息内容" name="content" class="layui-textarea" lay-verify="required" > <?php echo $model['content']; ?></textarea>
                </div>
            </div>
            <div class="layui-form-item">
                <label for="" class="layui-form-label"></label>
                <button  class="layui-btn" lay-filter="handover-update" lay-submit="">
                    更新
                </button>
            </div>
        </div>
    </div>
</div>


<?php $this->beginBlock('footer') ?>
    <script>
        layui.use(['form','layer'], function(){
            $ = layui.jquery;
            var form = layui.form ,layer = layui.layer;

            //监听提交
            form.on('submit(handover-update)', function(data){
                layer.load(3);
                $.post('<?= Url::to(['log-handover/handover-update-content']) ?>', data.field, function (res) {
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

