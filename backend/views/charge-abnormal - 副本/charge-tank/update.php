<?php

use backend\models\ChargingWarehouse;
use yii\helpers\Url;
?>
<div class="x-body layui-anim layui-anim-up">
    <form class="layui-form">
        <input type="hidden" name="id" value="<?= $model['id'] ?>">
        <div class="layui-form-item">
            <label class="layui-form-label">
                当前状态:
            </label>
            <div class="layui-input-inline">
                <select name="status" lay-verify="required">
                    <?php foreach (ChargingWarehouse::$statusArr as $key => $value): ?>
                        <option value="<?= $key ?>" <?= $key == $model['status'] ? 'selected' : '' ?>><?= $value ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                设备类型:
            </label>
            <div class="layui-input-inline">
                <select name="stype" lay-verify="required">
                    <?php foreach (ChargingWarehouse::$stypeArr as $key => $value): ?>
                        <option value="<?= $key ?>" <?= $key == $model['stype'] ? 'selected' : '' ?>><?= $value ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label for="" class="layui-form-label"></label>
            <button class="layui-btn" lay-filter="update" lay-submit="">
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
        form.on('submit(update)', function(data){
            layer.load(3);
            $.post('<?= Url::to(['charge-tank/update']) ?>', data.field, function (res) {
                layer.closeAll();
                if (res.status === 200) {
                    layer.msg(res.msg, {icon: 1,time: 1500}, function () {
                        location.href= '<?= Url::to(['charge-tank/index']) ?>';
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

