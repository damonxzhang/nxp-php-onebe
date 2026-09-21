<?php

use backend\models\Approve;
use backend\models\search\ApproveSearch;
use backend\models\UserInfo;
use backend\models\Blog;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

?>
<div class="x-body layui-anim layui-anim-up">
    <form class="layui-form">
        <div class="layui-form-item">
            <label for="nickname" class="layui-form-label">
                标题:
            </label>
            <div class="layui-input-inline" style="width: 800px;">
                <input type="text" value="<?= $model['title'] ?>" readonly="readonly" autocomplete="off" class="layui-input" style="font-weight:bold">
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
                生效时间:
            </label>
            <div class="layui-input-inline" style="width: 300px;">
                <input class="layui-input" placeholder="生效日期" value="<?= $model['effective_time'] ?>" readonly="readonly" autocomplete="off">
            </div>
            <label for="tel" class="layui-form-label">
                到期时间:
            </label>
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
                内容:
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
        <?php if ((new ApproveSearch())->checkUserConfirm($model['id'])): ?>
            <div class="layui-form-item" style="margin-top: 20px;">
                <label for="" class="layui-form-label"></label>
                <button type="button" class="layui-btn" onclick="confirmData(this)" data-id="<?=$model['id']?>">确认</button>
            </div>
        <?php endif; ?>
        <div class="layui-form-item" style="margin-top: 20px;">
            <div class="layui-card-body">
                <div class="video" id="video" style="width: 100%;"></div>
            </div>
        </div>
    </form>
</div>
<?php $this->beginBlock('footer') ?>
    <script>
        function confirmData(obj) {
            var id = $(obj).data('id');
            $.post('<?= Url::to(['confirm-data']) ?>', {id:id}, function (res) {
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
        }
    </script>
<?php $this->endBlock() ?>