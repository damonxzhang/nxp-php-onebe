<?php
use yii\helpers\Url;
?>
    <div class="x-body layui-anim layui-anim-up">
        <form class="layui-form">
            <div class="layui-form-item">
                <label for="name" class="layui-form-label">
                    <span class="x-red">*</span>未清洗原因:
                </label>
                <div class="layui-input-inline">
                    <input type="hidden" name="pid" value="<?= $pid ?>">
                    <textarea placeholder="请输入本周未清洗原因" lay-verify="required" name="common" class="layui-textarea" style="width: 300px;height: 200px;"></textarea>
                </div>
            </div>
            <div class="layui-form-item">
                <label for="" class="layui-form-label"></label>
                <button class="layui-btn" lay-filter="add" lay-submit="" id="submitform" onclick="subform()" type="button">
                    添加
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
            $.post('<?= Url::to(['wash-clothes/addcommon']) ?>', data.field, function (res) {
                if (res.status === 200) {
                    layer.msg(res.msg, {icon: 1, time: 1500}, function () {
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