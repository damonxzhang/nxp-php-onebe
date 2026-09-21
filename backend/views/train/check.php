<?php

use yii\helpers\Url;

?>
<div class="x-body layui-anim layui-anim-up">
    <div class="layui-row layui-col-space30">
        <div class="layui-row">
            <div class="layui-col-md4 layui-col-md-offset4">
            <form class="layui-form">
                <input type="hidden" name="id" value="<?= $id ?>">
                <div class="layui-input-inline">
                    <input type="text" name ="badge_code" id="autofocus" placeholder="请输入识别证码" autocomplete="off" lay-verify="required" class="layui-input">
                </div>
                <button class="layui-btn" lay-filter="add" lay-submit="">签到</button>
            </form>
            </div>
        </div>
        <div class="layui-col-md5">
            <div class="layui-form">
                <table class="layui-table">
                    <thead>
                        <tr>
                            <th colspan="2" class="layui-bg-black" style="text-align: center">未签到人员</th>
                        </tr>
                        <tr>
                            <th style="width: 5px">#</th>
                            <th style="text-align: center">人员名称</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($unchecked as $key => $list): ?>
                        <tr>
                            <td><?= $key + 1; ?></td>
                            <td style="text-align: center"><?= $list['chinese_name'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="layui-col-md5">
            <table class="layui-table">
                <thead>
                    <tr>
                        <th colspan="3" class="layui-bg-green" style="text-align: center">已签到人员</th>
                    </tr>
                    <tr>
                        <th style="width: 5px">#</th>
                        <th style="text-align: center">人员名称</th>
                        <th>签到时间</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($checked as $key => $list): ?>
                    <tr>
                        <td><?= $key + 1 ?></td>
                        <td style="text-align: center"><?= $list['chinese_name'] ?></td>
                        <td><?= date('Y-m-d H:i:s',strtotime($list['check_time'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
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
            $.post('<?= Url::to(['train/check-in']) ?>', data.field, function (res) {
                layer.closeAll();
                if (res.status === 200) {
                    layer.msg(res.msg, {icon: 1,time: 1500}, function () {
                        location.href= 'check?id=' + res.data;
                    })
                } else {
                    layer.msg(res.msg, {icon: 2, time: 1500}, function () {
                        location.href= 'check?id=' + res.data;
                    })
                }
            }, 'json');
            return false;
        });
    });
</script>
<?php $this->endBlock() ?>

