<?php
use yii\helpers\Url;

?>
    <div class="x-body layui-anim layui-anim-up">
        <form class="layui-form">
            <div class="layui-form-item">
                <label for="name" class="layui-form-label">
                    <span class="x-red">*</span>胶的型号:
                </label>
                <div class="layui-input-inline">
                    <select lay-filter="pectinSelect" name ="pectin_no" lay-verify="required">
                        <option value="" disabled selected>--请选择--</option>
                        <option value="WSX-HF-HAHV">WSX-HF-HAHV</option>
                        <option value="WF-6317">WF-6317</option>
                        <option value="WS-608">WS-608</option>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label for="name" class="layui-form-label">
                    产线库存:
                </label>
                <div class="layui-input-inline">
                    <input type="number" name="product_number" id="product_number" readonly="readonly" autocomplete="off" class="layui-input" >
                </div>
                
            </div>
            <div class="layui-form-item">
                <label for="name" class="layui-form-label">
                    <span class="x-red">*</span>申请数量:
                </label>
                <div class="layui-input-inline">
                    <input type="number" name="apply_number"  lay-verify="required" autocomplete="off" class="layui-input" >
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
    layui.use(['layer', 'jquery', 'form'], function () {
        var layer = layui.layer,
        $ = layui.jquery,
        form = layui.form;

        form.on('select(pectinSelect)', function(data){
            if(data.value == '' || data.value == "underfined"){
                layer.msg('请选择胶的型号', {icon: 2, time: 1500});
                $('#product_number').val('');
                return false;
            }else{
                layer.load(3);
                $.post('<?= Url::to(['tin-ball-cabinet/get-pectin-product-number']) ?>', {pectin_no:data.value}, function (res) {
                    layer.closeAll();
                    if (res.status === 100) {
                        layer.msg(res.msg, {icon: 2, time: 1500});
                    } else {
                        layer.msg('获取成功!');
                        $('#product_number').val(res.msg);
                    }
                }, 'json');
                return false;
            }
        });
    });

    layui.use(['form','layer'], function(){
        $ = layui.jquery;
        var form = layui.form ,layer = layui.layer;
        form.on('submit(add)', function(data){
            layer.load(3);
            $.post('<?= Url::to(['tin-ball-cabinet/pectin-create']) ?>', data.field, function (res) {
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
