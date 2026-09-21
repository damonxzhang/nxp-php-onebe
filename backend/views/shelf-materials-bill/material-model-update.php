<?php
use yii\helpers\Url;

?>
    <div class="x-body layui-anim layui-anim-up">
        <form class="layui-form">
            <input type="hidden" name="id" value="<?= $model['id'] ?>">
          
            <div class="layui-form-item">
                <label for="name" class="layui-form-label">
                    PART NO:
                </label>
                <div class="layui-input-inline">
                    <input type="hidden" name="id" value="<?= $model['id'] ?>">
                    <input type="text" value="<?= $model['part_no'] ?>" readonly="readonly" autocomplete="off" class="layui-input" >
                </div>
            </div>
            <div class="layui-form-item">
                <label for="name" class="layui-form-label">
                    DM 12NC:
                </label>
                <div class="layui-input-inline">
                    <input type="text" name="dm_nc" value="<?= $model['dm_nc'] ?>" readonly="readonly" autocomplete="off" class="layui-input" >
                </div>
            </div>
            <div class="layui-form-item">
                <label for="name" class="layui-form-label">
                    <span class="x-red">*</span>解冻时间:
                </label>
                <div class="layui-input-inline">
                    <input type="number" name="thawing_time" value="<?= $model['thawing_time'] ?>" lay-verify="required" autocomplete="off" class="layui-input" >
                </div>
                小时 &nbsp;&nbsp;&nbsp;&nbsp;按解冻开始时间计算小时
            </div>
            <div class="layui-form-item">
                <label for="name" class="layui-form-label">
                    <span class="x-red">*</span>过期时间:
                </label>
                <div class="layui-input-inline">
                    <input type="number" name="expiry_time" value="<?= $model['expiry_time'] ?>" lay-verify="required" autocomplete="off" class="layui-input" >
                </div>
                小时 &nbsp;&nbsp;&nbsp;&nbsp;按解冻开始时间计算小时
            </div>
            <div class="layui-form-item" pane="">
                <label class="layui-form-label">
                   <span class="x-red">*</span>NO BOX:
                </label>
                <div class="layui-input-block">
                    <input type="radio" value="0" name="no_box" title="不显示" <?= $model['no_box'] == 0 ? 'checked' : '' ?>>
                    <input type="radio" value="1" name="no_box" title="显示" <?= $model['no_box'] == 1 ? 'checked' : '' ?>>
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
                $.post('<?= Url::to(['shelf-materials-bill/material-model-update']) ?>', data.field, function (res) {
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
