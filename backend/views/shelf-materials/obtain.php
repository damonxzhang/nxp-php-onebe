<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
?>
<div class="x-body layui-anim layui-anim-up">
    <form class="layui-form">
        <input type="hidden" name ="id" value="<?= $id ?>" >
        <input type="hidden" name ="cont_no" value="<?= $cont_no ?>" >
        <div class="layui-form-item" style="width: 300px">
            <input type="text" id="materials" name="compared_cont_no" lay-verify="required" placeholder="物料编号" autocomplete="off" class="layui-input" >
        </div>
        <div class="layui-form-item">
            <label for="" class="layui-form-label"></label>
            <button class="layui-btn" lay-filter="compard"  lay-submit="">
                确定
            </button>
        </div>
    </form>
</div>

<?php $this->beginBlock('footer') ?>
<script>
    $(document).ready(function () {
        setTimeout(function () {$('#materials').focus();}, 50);
    });
    //下架物料
    layui.use(['form','layer'], function(){
        $ = layui.jquery;
        var form = layui.form ,layer = layui.layer;
        //监听提交
        form.on('submit(compard)', function(data){
            layer.load(3);
            $.post('<?= Url::to(['shelf-materials/obtain'])?>', data.field, function (res) {
                layer.closeAll();
                if (res.status === 200) {
                    layer.msg(res.msg, {icon: 1,time: 2000}, function () {
                        var index = parent.layer.getFrameIndex(window.name);
                        parent.layer.close(index);
                        parent.location.reload();
                    })
                } else {
                    layer.msg(res.msg, {icon: 2, time: 2000})
                }
            }, 'json');
            return false;
        });
    });
</script>
<?php $this->endBlock() ?>


