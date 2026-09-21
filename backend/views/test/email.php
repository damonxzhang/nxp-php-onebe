<?php
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use backend\models\Flow;
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">后台测试</a>
        <a href="javascript:;">
          <cite>邮箱测试</cite>
        </a>
      </span>
</div>
<div class="x-body layui-anim layui-anim-up">
    <form class="layui-form">
        <div class="layui-form-item">
            <label for="name" class="layui-form-label">
                <span class="x-red">*</span>邮箱地址:
            </label>
            <div class="layui-input-inline">
                <input type="text" name="email" placeholder="请输入邮箱地址" lay-verify="required" autocomplete="off" class="layui-input" style="width: 800px;">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>邮件主题:
            </label>
            <div class="layui-input-block">
                <input type="text" name="subject" placeholder="请输入邮件主题" lay-verify="required" autocomplete="off" class="layui-input" style="width: 800px;">
            </div>
        </div>
        <div class="layui-form-item">
            <label for="pwd" class="layui-form-label">
                <span class="x-red">*</span> 邮件内容:
            </label>
            <div class="layui-input-block">
                <textarea placeholder="请输入邮件内容" lay-verify="required" name="body" class="layui-textarea"  style="width: 800px;height: 200px;"></textarea>
            </div>
        </div>
        <div class="layui-form-item">
            <label for="" class="layui-form-label"></label>
            <button  class="layui-btn" lay-filter="add" lay-submit="">
                发送
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
            // layer.load(3);
            $.post('<?= Url::to(['test/email']) ?>', data.field, function (res) {
                layer.closeAll();
                if (res.status === 200) {
                    layer.msg(res.msg, {icon: 1, time: 1500})
                } else {
                    layer.msg(res.msg, {icon: 2, time: 1500})
                }
            }, 'json');
            return false;
        });
    });
</script>
<?php $this->endBlock() ?>
