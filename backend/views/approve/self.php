<?php

use backend\models\UserInfo;
use yii\helpers\Url;

$baseUrl = Yii::$app->params['base_url'];
?>
<div class="x-body layui-anim layui-anim-up">
    <form class="layui-form">
        <div class="layui-form-item">
            <label for="nickname" class="layui-form-label">
                标题:
            </label>
            <div class="layui-input-inline" style="width: 800px;">
                <input type="text" value="<?= $model['title'] ?>" readonly="readonly" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label for="tel" class="layui-form-label">
                编号:
            </label>
            <div class="layui-input-inline" style="width: 800px;">
                <input type="text" value="<?= $model['number'] ?>"  autocomplete="off" class="layui-input" readonly="readonly">
            </div>
        </div>
        <div class="layui-form-item">
            <label for="tel" class="layui-form-label">
                时间:
            </label>
            <div class="layui-input-inline" style="width: 300px;">
                <input class="layui-input" placeholder="生效日期" value="<?= $model['effective_time'] ?>" readonly="readonly" autocomplete="off">
            </div>
            <div class="layui-input-inline" style="width: 300px;">
                <input class="layui-input" placeholder="到期日期" value="<?= $model['alarm_time'] ?>" readonly="readonly" autocomplete="off">
            </div>
        </div>
        <div class="layui-form-item">
            <label for="tel" class="layui-form-label">
                发布人:
            </label>
            <div class="layui-input-inline" style="width: 800px;">
                <input type="text" value="<?php echo (new UserInfo())->getUserName($model['user_id']) ?>"  autocomplete="off" class="layui-input" readonly="readonly">
            </div>
        </div>
        <div class="layui-form-item layui-form-text">
            <label for="desc" class="layui-form-label">
                消息内容:
            </label>
            <div class="layui-input-block">
                <textarea  lay-verify="required" readonly="readonly" class="layui-textarea" style="width: 800px;height: 300px;"> <?php echo $model['content_data']; ?></textarea>
            </div>
        </div>
        <?php if (!empty($approveFile)): ?>
            <div class="layui-upload" style="margin-left: 110px;">
                <div class="layui-upload-list">
                    <table class="layui-table" style="width: 800px;">
                        <thead>
                        <tr>
                            <th>文件名</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody >
                        <?php foreach ($approveFile as $list): ?>
                            <tr>
                                <td><?= $list['file_name'] . '.' . $list['file_extension']?></td>
                                <td>
                                    <?php if ($list['file_extension'] == 'pdf'): ?>
                                        <img src="<?= $baseUrl.'images/file.png'?>" class="ez" href="<?= $baseUrl.$list['file_path']?>" alt="">
                                    <?php elseif(in_array($list['file_extension'], ['jpg', 'png','jpeg', 'gif'])): ?>
                                        <img src="<?= $baseUrl.$list['file_path']?>" class="ez" alt="">
                                    <?php elseif($list['file_extension'] == 'mp4'): ?>
                                        <button type="button" onclick="palyer_video('<?= $baseUrl.$list['file_path']?>')" class="layui-btn layui-btn-normal" ><i class="layui-icon">&#xe652;</i>播放视频</button>
                                    <?php else: ?>
                                        <a href="<?= $baseUrl.$list['file_path']?>" class="layui-btn"><i class="layui-icon">&#xe601;</i>下载</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
        <div class="layui-form-item">
            <label class="layui-form-label">
                主管建议:
            </label>
            <div class="layui-input-inline" style="width: 800px;">
                <select name="status">
                    <option value="1">同意</option>
                    <option value="2">不同意</option>
                </select>
            </div>
        </div>
        <div class="layui-form-item layui-form-text">
            <label for="desc" class="layui-form-label">
                审批内容:
            </label>
            <div class="layui-input-block">
                <textarea placeholder="审批内容" lay-verify="required" name="reply" class="layui-textarea"
                          style="width: 800px;height: 200px;"></textarea>
            </div>
        </div>
        <div class="layui-form-item" style="margin-top: 20px;">
            <input name="_csrf" type="hidden" id="_csrf" value="<?php echo \Yii::$app->request->getCsrfToken(); ?>">
            <input type="hidden" name="approve_id" value="<?= $model['id'] ?>" >
            <label for="" class="layui-form-label"></label>
            <button class="layui-btn" lay-filter="update" lay-submit="">保存</button>
        </div>
        <div class="layui-form-item" style="margin-top: 20px;">
            <div class="layui-card-body">
                <div class="video" id="video" style="width: 100%;"></div>
            </div>
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
            $.post('<?= Url::to(['approve/self']) ?>', data.field, function (res) {
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

