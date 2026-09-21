<?php

use yii\helpers\Url;
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">后台测试</a>
        <a href="javascript:;">
          <cite>MQTT测试</cite>
        </a>
      </span>
</div>
<div class="x-body layui-anim layui-anim-up">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so">
            <div class="layui-input-inline">
                <select name="number" lay-verify="required">
                    <option value="">请选择货架编号</option>
                    <option value="20">01</option>
                    <option value="19">02</option>
                    <option value="18">03</option>
                    <option value="17">04</option>
                    <option value="16">05</option>
                    <option value="11">06</option>
                    <option value="12">07</option>
                    <option value="13">08</option>
                    <option value="14">09</option>
                    <option value="15">10</option>
                    <option value="10">11</option>
                    <option value="09">12</option>
                    <option value="08">13</option>
                    <option value="07">14</option>
                    <option value="06">15</option>
                    <option value="01">16</option>
                    <option value="02">17</option>
                    <option value="03">18</option>
                    <option value="04">19</option>
                    <option value="05">20</option>
                </select>
            </div>
            <div class="layui-input-inline">
                <select name="status" lay-verify="required">
                    <option value="">请选择状态</option>
                    <option value="1">可领取</option>
                    <option value="2">醒料中</option>
                    <option value="3">空闲</option>
                    <option value="4">异常</option>
                    <option value="5">预警中</option>
                    <option value="6">过期</option>
                    <option value="7">领取中</option>
                </select>
            </div>
            <button class="layui-btn"  lay-filter="add" lay-submit="" >发送</button>
        </form>
    </div>
</div>
<?php $this->beginBlock('footer') ?>
<script>
    layui.use(['form','layer'], function(){
        $ = layui.jquery;
        var form = layui.form ,layer = layui.layer;
        //监听提交
        form.on('submit(add)', function(data){
            layer.load(3);
            $.post('<?= Url::to(['test/mqtt']) ?>', data.field, function (res) {
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

