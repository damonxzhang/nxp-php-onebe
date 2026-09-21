<?php

use backend\models\UserInfo;
$baseUrl = Yii::$app->params['base_url'];
?>
<div class="x-body layui-anim layui-anim-up">
    <form class="layui-form">
        <div class="layui-form-item">
            <label for="nickname" class="layui-form-label">
                培训标题:
            </label>
            <div class="layui-input-inline">
                <input type="text" autocomplete="off" value="<?= $train['title'] ?>" readonly="readonly" class="layui-input" style="width: 800px;">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                发起人:
            </label>
            <div class="layui-input-inline">
                <input type="text" autocomplete="off" value="<?= (new UserInfo())->getUserName($train['user_id']) ?>" readonly="readonly" class="layui-input" style="width: 800px;">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                培训对象:
            </label>
            <div class="layui-input-inline">
                <input type="text" autocomplete="off" value="<?= $trainUser ?>" readonly="readonly" class="layui-input" style="width: 800px;">
            </div>
        </div>
        <div class="layui-form-item">
            <label for="nickname" class="layui-form-label">
                预期效果:
            </label>
            <div class="layui-input-inline">
                <input type="text" autocomplete="off" lay-verify="required" readonly="readonly" value="<?= $train['expected'] ?>" class="layui-input" style="width: 800px;">
            </div>
        </div>
        <div class="layui-form-item layui-form-text">
            <label for="desc" class="layui-form-label">
                培训内容:
            </label>
            <div class="layui-input-block">
                <textarea class="layui-textarea" readonly="readonly" style="height: 150px;width: 800px;"> <?php echo $train['content_data']; ?></textarea>
            </div>
        </div>
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
                    <?php foreach ($trainFile as $list): ?>
                        <tr>
                            <td><?= $list['file_name'] . '.' . $list['file_extension']?></td>
                            <td>
                                <?php if ($list['file_extension'] == 'pdf'): ?>
                                    <img src="<?= $baseUrl.'images/file.png'?>" class="ez" href="<?= $baseUrl.$list['file_path']?>" alt="">
                                <?php elseif(in_array($list['file_extension'], ['jpg', 'png','jpeg', 'gif'])): ?>
                                    <img src="<?= $baseUrl.$list['file_path']?>" class="ez" alt="">
                                <?php elseif($list['file_extension'] == 'mp4'): ?>
                                     <a type="button" onclick="palyer_video('http://10.192.134.255/training_video/test.mp4')" class="layui-btn layui-btn-normal" ><i class="layui-icon">&#xe652;</i>播放视频</a>
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
        <div class="layui-form-item">
            <div class="layui-card-body">
                <div class="video" id="video" style="width: 100%;"></div>
            </div>
        </div>
    </form>
</div>
<?php $this->beginBlock('footer') ?>
<script>
</script>
<?php $this->endBlock() ?>
