<?php

use backend\models\search\ApproveSearch;
use backend\models\UserStation;
use kartik\select2\Select2;
use yii\helpers\Url;
use backend\models\UserInfo;

?>
<style>
    #material .layui-input-inline{
        width:10%;
    }
</style>
<div class="x-body layui-anim layui-anim-up">
    <form class="layui-form">
        <input type="hidden" name ="id" value="<?= $model['id'] ?>" >
        <div class="layui-form-item">
            <label for="nickname" class="layui-form-label">
               标题:
            </label>
            <div class="layui-input-inline">
                <input type="text" value="<?= $model['title'] ?>" readonly="readonly" autocomplete="off"
                       class="layui-input" style="width: 800px;">
            </div>
        </div>
        <div class="layui-form-item">
            <label for="nickname" class="layui-form-label">
                编号:
            </label>
            <div class="layui-input-inline">
                <input type="text" value="<?= $model['number'] ?>" autocomplete="off" class="layui-input"
                       style="width: 800px;" readonly="readonly">
            </div>
        </div>
        <div class="layui-form-item">
            <label for="tel" class="layui-form-label">
                创建日期:
            </label>
            <div class="layui-input-inline">
                <input class="layui-input" placeholder="创建日期" value="<?= $model['create_time'] ?>"
                       autocomplete="off" readonly="readonly"  style="width: 800px;">
            </div>
        </div>
        <div class="layui-form-item">
            <label for="tel" class="layui-form-label">
                到期日期:
            </label>
            <div class="layui-input-inline">
                <input class="layui-input" placeholder="到期日期" name="alarm_time" id="alarmTime" value="<?= $model['alarm_time'] ?>" autocomplete="off"  style="width: 200px;">
            </div>
            <div class="layui-input-inline" style="margin-left: 20px">
                <button class="layui-btn layui-btn-warm" lay-filter="update" lay-submit="">
                    延期
                </button>
            </div>
        </div>
        <div class="layui-form-item">
            <label for="tel" class="layui-form-label">
                发布人:
            </label>
            <div class="layui-input-inline">
                <input type="text" value="<?php echo (new UserInfo())->getUserName($model['user_id']) ?>" autocomplete="off"
                       class="layui-input" readonly="readonly" style="width: 800px;">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">站别:</label>
            <div class="layui-input-inline">
                <input type="text" value="<?php echo UserInfo::getSelfArrData((new UserStation())->getStationArr(), $model['station']) ?>" autocomplete="off"
                       class="layui-input" readonly="readonly" style="width: 800px;">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">班别:</label>
            <div class="layui-input-inline">
                <input type="text" value="<?php echo UserInfo::getSelfArrData(Userinfo::$shiftArr, $model['shift']) ?>" autocomplete="off"
                       class="layui-input" readonly="readonly" style="width: 800px;">
            </div>
        </div>
        <div class="layui-form-item">
            <label for="desc" class="layui-form-label">
                <span class="x-red">*</span>内容:
            </label>
            <div class="layui-input-inline">
            <textarea placeholder="请输入内容" lay-verify="required" name="content_data" class="layui-textarea"
                      style="width: 800px;height: 200px;"><?php echo $model['content_data']; ?></textarea>
            </div>
        </div>
        <div class="layui-form-item" style="margin-top: 50px;">
            <label for="" class="layui-form-label"></label>
            <?php if ((new ApproveSearch())->checkFinishUpdate($model['id'])): ?>
                <button class="layui-btn layui-btn-danger" lay-filter="finished" lay-submit="">
                    结束
                </button>
            <?php endif; ?>
        </div>
    </form>
</div>
<?php
$updateUrl = Url::to(['approve/update']);
$finishedUrl = Url::to(['approve/finish']);
$js = <<<JS
        //日期时间
        layui.use('laydate', function() {
            var laydate = layui.laydate;
            $(this).removeAttr("lay-key");
            laydate.render({
                elem: '#alarmTime',
                format: 'yyyy-MM-dd',
                min: +30
            });
        });
        layui.use(['form','layer'], function(){
            $ = layui.jquery;
            var form = layui.form ,layer = layui.layer;

            //监听提交
            form.on('submit(update)', function(data){
                layer.load(3);
                $.post('{$updateUrl}', data.field, function (res) {
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
            
            //修改状态为已完成
            form.on('submit(finished)', function(data){
                layer.load(3);
                $.post('{$finishedUrl}', data.field, function (res) {
                    layer.closeAll();
                    if (res.status === 200) {
                        layer.msg(res.msg, {icon: 1,time: 1500}, function () {
                            var index = parent.layer.getFrameIndex(window.name);
                            parent.layer.close(index);
                            parent.location.reload();
                        })
                    } else {
                        layer.msg(res.msg, {icon: 2, time: 3000})
                    }
                }, 'json');
                return false;
            });
          
        });
        $(".select2").next().remove(); //去掉select2 的样式问题
JS;
$this->registerJs($js);
?>

