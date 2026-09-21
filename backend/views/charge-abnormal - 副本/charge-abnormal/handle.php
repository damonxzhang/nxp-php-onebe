<?php
use yii\helpers\Url;
use backend\models\UserInfo;
use yii\helpers\ArrayHelper;
?>
<div class="x-body layui-anim layui-anim-up">
    <form class="layui-form">
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>状态:
            </label>
            <div class="layui-input-inline" style="width:600px">
                <select name="status" >
                    <?php if($model->status == $model::NOT_STATUS):?>
                        <?php foreach ($model::$handleStatusArr  as $key => $value): ?>
                            <option value="<?= $key ?>" ><?= $value ?></option>
                        <?php endforeach; ?>
                    <?php else:?>
                        <option value="<?=$model::OK_STATUS?>" >完成</option>
                    <?php endif;?>
                </select>
            </div>
        </div>
        <div class="layui-form-item" >
            <label for="tel" class="layui-form-label">
                <span class="x-red">*</span>处理信息:
            </label>
            <div class="layui-input-inline" >
                <input type="text" name="comment" autocomplete="off" class="layui-input"  style="width:600px">
            </div>
        </div>
        <div class="layui-form-item"  style="margin-top: 50px;">
            <label for="" class="layui-form-label">
            </label>
            <button class="layui-btn" lay-filter="add" lay-submit="">
                保存
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
            $.post('<?= Url::to(['handle','id' => $model->id]) ?>', data.field, function (res) {
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
