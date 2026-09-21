<?php

use backend\models\MachineCheck;
use kartik\select2\Select2;
use yii\helpers\Url;
/* @var $facilityArr MachineCheck */

?>
<div class="x-body layui-anim layui-anim-up">
    <form class="layui-form">
        <div class="layui-form-item">
            <label class="layui-form-label"><span class="x-red">*</span>设备:</label>
            <div class="layui-input-inline" style="width: 600px;">
                <?php echo Select2::widget([
                    'name'    => 'facility_id',
                    'data'    => $facilityArr,
                    'options' => ['multiple' => true,'class' => 'facility_id-select', 'placeholder' => '设备']
                ]); ?>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>班别:
            </label>
            <div class="layui-input-block" style="margin-left: 20px">
                <input type="radio" name="class" value="1" checked title="白班">
                <input type="radio" name="class" value="2" title="夜班">
            </div>
        </div>
        <div class="layui-form-item  count">
            <label for="tel" class="layui-form-label">
                <span class="x-red">*</span>起始日期:
            </label>
            <div class="layui-input-inline">
                <input class="layui-input" placeholder="起始日期" lay-verify="required" id="startDate" name="start_date"
                       autocomplete="off">
            </div>
        </div>
        <div class="layui-form-item  count">
            <label for="tel" class="layui-form-label">
                <span class="x-red">*</span>截止日期:
            </label>
            <div class="layui-input-inline">
                <input class="layui-input" placeholder="截止日期" lay-verify="required" id="endDate" name="end_date"
                       autocomplete="off">
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
<?php
$importUrl = Url::to(["machine-check/import"]);
$js        = <<<JS

        layui.use(['laydate', 'form', 'layer'], function () {
            
            $(this).removeAttr("lay-key");
            layui.laydate.render({
                elem: '#startDate'
            });
            layui.laydate.render({
                elem: '#endDate'
            });
            var form = layui.form, layer = layui.layer;
            //监听提交
            form.on('submit(update)', function (data) {
                layer.load(3);
                delete data.field["facility_id[0]"];
                 data.field.facility_id = $('.facility_id-select').select2('val');
                $.post('{$importUrl}', data.field, function (res) {
                    layer.closeAll();
                    if (res.status === 200) {
                        layer.msg(res.msg, {icon: 1, time: 1500}, function () {
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
        $(".select2").next().remove(); //去掉select2 的样式问题
JS;
$this->registerJs($js);
?>
