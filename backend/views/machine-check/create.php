<?php

use backend\models\Facility;
use backend\widgets\SelectSearchFacility;
use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="x-body layui-anim layui-anim-up">
    <form class="layui-form">
        <div class="form-group field-facility-cba_pass_date required">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label"><span class="x-red">*</span>设备型号:</label>
                <div class="layui-input-inline">
                    <?= SelectSearchFacility::widget([
                        'name'  => 'facility_id',
                        'url'   => Url::to('/facility/find-facility'),
                    ])?>
                </div>
                <div class="layui-col-md4" style="color:red"><div class="help-block"></div></div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>班别:
            </label>
            <div class="layui-input-block" style="margin-left: 20px">
                <input type="radio" name="class" value="1" checked title="白班" >
                <input type="radio" name="class" value="2" title="夜班">
            </div>
        </div>
        <div class="layui-form-item  count">
            <label for="tel" class="layui-form-label">
                <span class="x-red">*</span>时间:
            </label>
            <div class="layui-input-inline">
                <input class="layui-input" placeholder="时间" lay-verify="required" id="check_time" name="check_time"  autocomplete="off">
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
    layui.use(['laydate','form','layer'], function(){
        layui.laydate.render({
            elem: '#check_time'
        });

        var form = layui.form ,layer = layui.layer;
        //监听提交
        form.on('submit(update)', function(data){
            layer.load(3);
            $.post('<?= Url::to(['machine-check/create']) ?>', data.field, function (res) {
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
