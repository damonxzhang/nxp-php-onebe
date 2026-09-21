<?php

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
        <input type="hidden" name ="approve_id" value="<?= $model['id'] ?>" >
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
            <label class="layui-form-label">通知对象:</label>
            <div class="layui-input-inline" style="width: 260px;">
                <?php echo Select2::widget([
                    'name'    => 'area',
                    'value'   => explode(',', $model['area']),
                    'data'    => UserInfo::$areaArr,
                    'options' => ['multiple' => true,'class' => 'station-select', 'placeholder' => '区域']
                ]); ?>
            </div>
            <div class="layui-input-inline" style="width: 260px;">
                <?php echo Select2::widget([
                    'name'    => 'station',
                    'value'   => explode(',', $model['station']),
                    'data'    => $stationArr,
                    'options' => ['multiple' => true,'class' => 'station-select', 'placeholder' => '站别']
                ]); ?>
            </div>
            <div class="layui-input-inline" style="width: 260px;">
                <?php echo Select2::widget([
                    'name'    => 'shift',
                    'value'   => explode(',', $model['shift']),
                    'data'    => UserInfo::$shiftArr,
                    'options' => ['multiple' => true,'class' => 'shift-select', 'placeholder' => '班别']
                ]); ?>
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
        <div class="layui-upload" style="margin-left: 80px;">
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
                    <tbody id="demoList">
                    <?php if(!empty($approveFile)):?>
                        <?php foreach($approveFile as $value):?>
                            <tr >
                                <td><?=$value['file_name']?></td>
                                <td><span style="color: #5FB878;">上传成功</span></td>
                                <td>
                                    <a class="layui-btn layui-btn-xs delete-file">删除</a>
                                    <input type="hidden" name="upload_file[]" value="<?=$value['file_path']?>">
                                </td>
                            </tr>
                        <?php endforeach;?>
                    <?php endif;?>
                    </tbody>
                </table>
            </div>
            <button type="button" onclick="uploadFile(this)" class="layui-btn layui-btn-normal" id="testListAction">
                上传文件
            </button>
        </div>
        <div class="layui-form-item" style="margin-top: 50px;">
            <label for="" class="layui-form-label">
            </label>
            <button class="layui-btn" lay-filter="update" lay-submit="">
                保存
            </button>
        </div>
    </form>
</div>

<?php
$updateUrl = Url::to(['approve/update-draft']);
$js = <<<JS
   
        layui.use('laydate', function() {
            var laydate = layui.laydate;
            $(this).removeAttr("lay-key");
            laydate.render({
                elem: '#alarmTime',
                format: 'yyyy-MM-dd'
            });
        });
        layui.use(['form','layer'], function(){
        $ = layui.jquery;
        var form = layui.form ,layer = layui.layer;

        //监听提交
        form.on('submit(update)', function(data){
            layer.load(3);
             delete data.field["station[0]"];
             delete data.field["shift[0]"];
             data.field.station = $('.station-select').select2('val');
             data.field.shift = $('.shift-select').select2('val');
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
        });

    $(".select2").next().remove(); //去掉select2 的样式问题

JS;
$this->registerJs($js);
?>



