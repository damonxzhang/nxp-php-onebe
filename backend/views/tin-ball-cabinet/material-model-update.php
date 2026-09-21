<?php
use yii\helpers\Url;

?>
    <div class="x-body layui-anim layui-anim-up">
        <form class="layui-form">
            <input type="hidden" name="id" value="<?= $model['id'] ?>">
          
            <div class="layui-form-item">
                <label for="name" class="layui-form-label">
                MaterialCode:
                </label>
                <div class="layui-input-inline">
                    <input type="hidden" name="id" value="<?= $model['id'] ?>">
                    <input type="text" value="<?= $model['materialcode'] ?>" readonly="readonly" style="background-color:#F5F5F5" autocomplete="off" class="layui-input" >
                </div>
            </div>
            <div class="layui-form-item">
                <label for="name" class="layui-form-label">
                每条/瓶:
                </label>
                <div class="layui-input-inline">
                    <input type="number" name="each" value="<?= $model['each'] ?>" lay-verify="required" autocomplete="off" class="layui-input" >
                </div>
            </div>
            <div class="layui-form-item">
                <label for="name" class="layui-form-label">
                每瓶/kk:
                </label>
                <div class="layui-input-inline">
                    <input type="number" name="bottle" value="<?= $model['bottle'] ?>"  lay-verify="required" autocomplete="off" class="layui-input" >
                </div>
            </div>
            <div class="layui-form-item" style="margin-top:50px;">
                <label for="" class="layui-form-label"></label>
                <button class="layui-btn layui-btn-lg" lay-filter="update" lay-submit="">更新</button>
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
                $.post('<?= Url::to(['tin-ball-cabinet/material-model-update']) ?>', data.field, function (res) {
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
