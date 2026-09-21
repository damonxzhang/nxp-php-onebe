<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Url;
?>
<div class="x-body layui-anim layui-anim-up">
    <form class="layui-form">
        <input type="hidden" name ="id" value="<?= $id ?>" >
        <div class="layui-form-item">
            <label for="name" class="layui-form-label">
                <span class="x-red">*</span>操作人员:
            </label>
            <div class="layui-input-inline">
                <input type="text" name="badge_code" id="autofocus" onkeypress="if(event.keyCode==13) focusNextInput(this);" lay-verify="required" autocomplete="off" class="layui-input" >
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>直属领导:
            </label>
            <div class="layui-input-inline">
                <input type="text" name="leader_badge_code" id="leader_badge_code" onkeypress="if(event.keyCode==13) subform();" lay-verify="required" autocomplete="off" class="layui-input" >
            </div>
        </div>
        <div class="layui-form-item">
            <label for="" class="layui-form-label"></label>
            <button  class="layui-btn" lay-filter="update" lay-submit="" id="submitform" onclick="subform()" type="button">
                完成报废
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
            $.post('<?= Url::to(['shelf-materials/reject']) ?>', data.field, function (res) {
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
