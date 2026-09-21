<?php
use yii\helpers\Url;

?>
    <div class="x-body layui-anim layui-anim-up">
        <form class="layui-form">
            <div class="layui-form-item">
                <label for="name" class="layui-form-label">
                    <span class="x-red">*</span>PART NO:
                </label>
                <div>
                    <div class="layui-input-inline" style = "float:left" >
                        <input type="text" name="part_no" lay-verify="required" autocomplete="off" class="layui-input raise" >
                    </div>
                    <div >
                        <button type="button" class="layui-btn get-dm-nc">获取DM 12NC</button>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label for="name" class="layui-form-label">
                    DM 12NC:
                </label>
                <div class="layui-input-inline">
                    <input type="text" name="dm_nc"  readonly="readonly" lay-verify="required" autocomplete="off" id="dm-nc" class="layui-input" >
                </div>
            </div>
            <div class="layui-form-item">
                <label for="name" class="layui-form-label">
                    <span class="x-red">*</span>解冻时间:
                </label>
                <div class="layui-input-inline">
                    <input type="number" name="thawing_time"  lay-verify="required" autocomplete="off" class="layui-input" >
                </div>
                小时 &nbsp;&nbsp;&nbsp;&nbsp;按解冻开始时间计算小时
            </div>
            <div class="layui-form-item">
                <label for="name" class="layui-form-label">
                    <span class="x-red">*</span>过期时间:
                </label>
                <div class="layui-input-inline">
                    <input type="number" name="expiry_time"  lay-verify="required" autocomplete="off" class="layui-input" >
                </div>
                小时 &nbsp;&nbsp;&nbsp;&nbsp;按解冻开始时间计算小时
              
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">
                   <span class="x-red">*</span>NO BOX:
                </label>
                <div class="layui-input-block">
                    <input type="radio" value="0" name="no_box" title="不显示" checked="">
                    <input type="radio" value="1" name="no_box" title="显示">
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
        $('.get-dm-nc').click(()=>{
            let part_no = $(".raise").val();
            if(part_no == '' || part_no == "underfined"){
                layer.msg('请输入PART NO', {icon: 2, time: 1500});
            }
            $.post('<?= Url::to(['shelf-materials-bill/get-dm-nc']) ?>', {part_no:part_no}, function (res) {
                layer.closeAll();
                if (res.status === 100) {
                    layer.msg(res.msg, {icon: 2, time: 1500});
                } else {
                    layer.msg('获取成功!');
                    $('#dm-nc').val(res.msg);
                }
            }, 'json');
            return false;
        })

        layui.use(['form','layer'], function(){
            $ = layui.jquery;
            var form = layui.form ,layer = layui.layer;
            form.on('submit(add)', function(data){
                layer.load(3);
                $.post('<?= Url::to(['shelf-materials-bill/material-model-create']) ?>', data.field, function (res) {
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