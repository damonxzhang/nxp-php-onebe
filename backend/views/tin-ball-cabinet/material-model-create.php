<?php
use yii\helpers\Url;

?>
    <div class="x-body layui-anim layui-anim-up">
        <form class="layui-form">
            <div class="layui-form-item">
                <label for="name" class="layui-form-label">
                    <span class="x-red">*</span>MaterialCode:
                </label>
                <div>
                    <div class="layui-input-inline" style = "float:left" >
                        <input type="text" name="materialcode" lay-verify="required" autocomplete="off" class="layui-input raise" >
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label for="name" class="layui-form-label">
                    <span class="x-red">*</span>每条/瓶:
                </label>
                <div class="layui-input-inline">
                    <input type="number" name="each"  lay-verify="required" autocomplete="off" class="layui-input" >
                </div>
                
            </div>
            <div class="layui-form-item">
                <label for="name" class="layui-form-label">
                    <span class="x-red">*</span>每瓶/kk:
                </label>
                <div class="layui-input-inline">
                    <input type="number" name="bottle"  lay-verify="required" autocomplete="off" class="layui-input" >
                </div>
            </div>
            <div class="layui-form-item" style="margin-top:50px;">
                <label for="" class="layui-form-label"></label>
                <button  class="layui-btn layui-btn-lg" lay-filter="add" lay-submit="">
                    提交
                </button>
            </div>
        </form>
    </div>

<?php $this->beginBlock('footer') ?>
    <script>

        layui.use(['form','layer'], function(){
            $ = layui.jquery;
            var form = layui.form ,layer = layui.layer;
            form.on('submit(add)', function(data){
                layer.load(3);
                $.post('<?= Url::to(['tin-ball-cabinet/material-model-create']) ?>', data.field, function (res) {
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