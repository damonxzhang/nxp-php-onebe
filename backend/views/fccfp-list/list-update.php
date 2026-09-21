<?php

use yii\helpers\Url;
use backend\models\FccfpList;

?>
<div class = "x-body layui-anim layui-anim-up">
    <form class = "layui-form">
        <div class="layui-row">
            <input type="hidden" name="id" value="<?= $model['id'] ?>">
            <div class = "layui-form-item">
                <label class = "layui-form-label">批号:</label>
                <div class = "layui-input-inline" style = "width: 600px;">
                    <input type="text" value="<?= $model['containername'] ?>"  autocomplete="off" class="layui-input" readonly="readonly">
                </div>
            </div>
            <div class = "layui-form-item">
                <label class = "layui-form-label">工序:</label>
                <div class = "layui-input-inline" style = "width: 600px;">
                    <input type="text" value="<?= $model['nowspecname'] ?>"  autocomplete="off" class="layui-input" readonly="readonly">
                </div>
            </div>
            <div class = "layui-form-item">
                <label class = "layui-form-label">出炉时间:</label>
                <div class = "layui-input-inline" style = "width: 600px;">
                    <input type="text" value="<?= $model['nldpmc_trackout'] ?>"  autocomplete="off" class="layui-input" readonly="readonly">
                </div>
            </div>
            <div class = "layui-form-item">
                <label class = "layui-form-label">开始工序:</label>
                <div class = "layui-input-inline" style = "width: 600px;">
                    <input type="text" value="Marking"  autocomplete="off" class="layui-input" readonly="readonly">
                </div>
            </div>
            <div class = "layui-form-item">
                <label class = "layui-form-label">开始时间:</label>
                <div class = "layui-input-inline" style = "width: 600px;">
                    <input type="text" value="<?= $model['fcmark_clean_start_time'] ?>"  autocomplete="off" class="layui-input" readonly="readonly">
                </div>
            </div>
            <div class = "layui-form-item">
                <label class = "layui-form-label">结束工序:</label>
                <div class = "layui-input-inline" style = "width: 600px;">
                    <input type="text" value="Flux_Clean"  autocomplete="off" class="layui-input" readonly="readonly">
                </div>
            </div>
            <div class = "layui-form-item">
                <label class = "layui-form-label">剩余时间:</label>
                <div class = "layui-input-inline" style = "width: 600px;">
                    <input type="text" value="<?= $model['calibration_time'] ?>"  autocomplete="off" class="layui-input" readonly="readonly">
                </div>
            </div>
            <div class = "layui-form-item">
                <label class = "layui-form-label"><span class = "x-red">*</span>状态:</label>
                <div class = "layui-input-inline" style = "width: 600px;">
                    <select name = "status" class = "select-process"  style = "height: 30px;">
                        <?php foreach (FccfpList::$statusList as $key => $value): ?>
                            <option <?= $key == $model['status'] ? 'selected' : '' ?> value = "<?= $key ?>"><?= $value ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label for="" class="layui-form-label"></label>
                <button class="layui-btn" lay-filter="update" lay-submit="">
                    修改
                </button>
            </div>
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
            $.post('<?= Url::to(['fccfp-list/list-update']) ?>', data.field, function (res) {
                layer.closeAll();
                if (res.status === 200) {
                    layer.msg(res.msg, {icon: 1,time: 1500}, function () {
                        var index = parent.layer.getFrameIndex(window.name);
                        parent.layer.close(index);
                        parent.location.reload();
                    })
                } else {
                    layer.msg(res.msg, {icon: 2, time: 1500});
                }
            }, 'json');
            return false;
        });
    });
</script>
<?php $this->endBlock() ?>
