<?php
use backend\models\UserInfo;
use backend\models\Blog;
use yii\helpers\ArrayHelper;
?>
<div class="x-body layui-anim layui-anim-up">
    <form class="layui-form">
        <div class="layui-form-item">
            <label for="nickname" class="layui-form-label">
                日志标题:
            </label>
            <div class="layui-input-inline" style="width: 800px;">
                <input type="text" value="<?= $model['title'] ?>" readonly="readonly" autocomplete="off" lay-verify="required" class="layui-input">
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

        <div class="layui-form-item">
            <label for="tel" class="layui-form-label">
                日志状态:
            </label>
            <div class="layui-input-inline" style="width: 800px;">
                <input type="text" value="<?=  ArrayHelper::getValue(Blog::$statusArr, $model['status'],'') ?>"  autocomplete="off" class="layui-input" readonly="readonly">
            </div>
        </div>
        <div class="layui-form-item">
            <label for="tel" class="layui-form-label">
                过期时间:
            </label>
            <div class="layui-input-inline" style="width: 800px;">
                <input class="layui-input" placeholder="过期时间" value="<?= date('Y-m-d H:i:s',strtotime($model['alarm_time'])) ?>" readonly="readonly" autocomplete="off">
            </div>
        </div>
        <div class="layui-form-item layui-form-text">
            <label for="desc" class="layui-form-label">
                日志内容:
            </label>
            <div class="layui-input-block">
                <textarea  lay-verify="required" readonly="readonly" class="layui-textarea" style="width: 800px;height: 300px;"> <?php echo $model['content_data']; ?></textarea>
            </div>
        </div>
        <?php if (!empty($blogFile)): ?>
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
                        <?php foreach ($blogFile as $list): ?>
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
        <div class="layui-form-item" style="margin-top: 20px;">
            <label for="" class="layui-form-label"></label>
            <button type="button" class="layui-btn" onclick="compareData(this)" data-id="<?=$model['id']?>">跳转到编辑</button>
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
        function compareData(obj) {
            var id = $(obj).data('id');
            window.location.href= 'update?id=' + id;
        }
    </script>
<?php $this->endBlock() ?>