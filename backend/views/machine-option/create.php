<?php

use yii\helpers\Url;
?>
<div class="x-body layui-anim layui-anim-up">
    <form class="layui-form">
        <input type="hidden" name="model_id" value="<?= $param['model_id'] ?>">
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>设备型号:
            </label>
            <div class="layui-input-inline">
                <input type="text" name="position" value="" lay-verify="required" autocomplete="off" class="layui-input" placeholder= "请输入位置编号" style="width: 800px;">
            </div>
        </div>
        <div class="layui-form-item layui-form-text">
            <label for="desc" class="layui-form-label">
                <span class="x-red">*</span>标准:
            </label>
            <div class="layui-input-block">
                <textarea placeholder="请输入标准" lay-verify="required" name="standard" class="layui-textarea" style="width: 800px;height: 200px;"></textarea>
            </div>
        </div>
        <div class="layui-upload" style="margin-left: 110px;">
            <button type="button" class="layui-btn layui-bg-cyan" id="fileList">请选择文件</button>
            <div class="layui-upload-list">
                <table class="layui-table" style="width: 800px;">
                    <thead>
                    <tr>
                        <th>文件名</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody id="demoList"></tbody>
                </table>
            </div>
            <button type="button" onclick="uploadFile(this)" class="layui-btn layui-btn-normal" id="testListAction">上传文件</button>
        </div>
        <div class="layui-form-item" style="margin-top: 50px;">
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
            $.post('<?= Url::to(['machine-option/create']) ?>', data.field, function (res) {
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
