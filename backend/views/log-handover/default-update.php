<?php

use backend\models\LogHandoverDefault;
use yii\helpers\Url;
?>
    <div class="x-body layui-anim layui-anim-up">
        <form class="layui-form">
            <div class="layui-form-item">
                <input type="hidden" name="id" value="<?= $model['id'] ?>">
                <label class="layui-form-label"> <span class="x-red">*</span>默认内容:</label>
                <div class="layui-input-inline"> 
                    <textarea lay-verify="required" name="title" class="layui-textarea" style="width: 400px;height: 60px;"><?php echo $model['title']; ?></textarea>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">
                    <span class="x-red">*</span> 所属分部:
                </label>
                <div class="layui-input-inline" style="width: 400px;">
                    <select name="section" lay-verify="required">
                        <?php foreach ($sectionArr as $key => $value): ?>
                            <option value="<?= $key ?>" <?= $key == $model['section'] ? 'selected' : '' ?>><?= $value ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><span class="x-red">*</span>类型:</label>
                <div class="layui-input-inline" style="width: 400px;">
                    <select name="type" lay-verify="required" lay-filter="select_type">
                        <?php foreach (LogHandoverDefault::$typeArr as $key => $value): ?>
                            <option value="<?= $key ?>" <?= $key == $model['type'] ? 'selected' : '' ?>><?= $value ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="layui-form-item" id="step1" <?php if ($model['type'] != 1): ?> style="display: none;" <?php endif; ?>>
                <label class="layui-form-label"><span class="x-red">*</span>物料类型：</label>
                <div class="layui-input-inline" style="width: 400px;">
                    <select name="type_id">
                        <option value=""></option>
                        <?php foreach ($logHandoverTypeArr as $key => $value): ?>
                            <option value="<?= $key ?>" <?= $key == $model['type_id'] ? 'selected' : '' ?>><?= $value ?></option>
                        <?php endforeach; ?>
                    </select> 
                </div>
            </div>

            <div class="layui-form-item" id="step2" <?php if ($model['type'] != 1): ?> style="display: none;" <?php endif; ?>>
                <label class="layui-form-label"><span class="x-red">*</span>批次类型：</label>
                <div class="layui-input-inline" style="width: 400px;">
                    <select name="batch_id">
                        <option value=""></option>
                        <?php foreach ($logHandoverBatchArr as $key => $value): ?>
                            <option value="<?= $key ?>" <?= $key == $model['batch_id'] ? 'selected' : '' ?>><?= $value ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="layui-form-item" id="step3"  <?php if ($model['type'] != 2): ?> style="display: none;" <?php endif; ?>>
                <label class="layui-form-label"><span class="x-red">*</span>设备类型：</label>
                <div class="layui-input-inline" style="width: 400px;">
                    <select name="device_type_id">
                        <option value=""></option>
                        <?php foreach ($logHandoverDeviceTypeArr as $key => $value): ?>
                            <option value="<?= $key ?>" <?= $key == $model['device_type_id'] ? 'selected' : '' ?>><?= $value ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="layui-form-item" id="step4" <?php if ($model['type'] != 3): ?> style="display: none;" <?php endif; ?>>
                <label class="layui-form-label"><span class="x-red">*</span>备注类型：</label>
                <div class="layui-input-inline" style="width: 400px;">
                    <select name="comment_id">
                        <option value=""></option>
                        <?php foreach ($logHandoverCommentTypeArr as $key => $value): ?>
                            <option value="<?= $key ?>" <?= $key == $model['comment_id'] ? 'selected' : '' ?>><?= $value ?></option>
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
            
            //监听下拉框的值
            form.on('select(select_type)', function (data) {
                $('#step1, #step2, #step3, #step4').hide();
                if (data.value == 1) {
                    $('#step1').show();
                    $('#step2').show();
                } else if (data.value === '2') {
                    $('#step3').show();
                    $('#type_id').prop("checked", false);
                } else if (data.value === '3') {
                    $('#step4').show();
                    $('#type_id').prop("checked", false);
                }
               form.render('select'); // 重新渲染下拉框
            });

            //监听提交
            form.on('submit(update)', function(data){
                layer.load(3);
                $.post('<?= Url::to(['log-handover/default-update']) ?>', data.field, function (res) {
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