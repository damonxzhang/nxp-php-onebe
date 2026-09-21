<?php

use backend\models\UserInfo;
use yii\helpers\ArrayHelper;
use backend\models\Blog;
use backend\models\BlogUser;

$baseUrl = Yii::$app->params['base_url'];
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
                交接人:
            </label>
            <div class="layui-input-inline" style="width: 800px;">
                <input type="text" value="<?= $detailUserName ?>"  autocomplete="off" class="layui-input" readonly="readonly">
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
        <div class="layui-form-item">
            <label for="nickname" class="layui-form-label">
                反馈人:
            </label>
            <div class="layui-input-inline" style="width: 800px;">
                <input type="text" value="<?= (new UserInfo())->getUserName($detailModel['user_id']) ?>" readonly="readonly" autocomplete="off" lay-verify="required" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label for="tel" class="layui-form-label">
                反馈交接人:
            </label>
            <div class="layui-input-inline" style="width: 800px;">
                <input type="text" value="<?= (new BlogUser())->getUserName($detailModel['id']) ?>"  autocomplete="off" class="layui-input" readonly="readonly">
            </div>
        </div>
        <div class="layui-form-item">
            <label for="nickname" class="layui-form-label">
                反馈时间:
            </label>
            <div class="layui-input-inline" style="width: 800px;">
                <input type="text" value="<?= date('Y-m-d H:i:s',strtotime($detailModel['create_time'])) ?>" readonly="readonly" autocomplete="off" lay-verify="required" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                反馈状态:
            </label>
            <div class="layui-input-inline" style="width: 800px;">
                <input type="text" readonly="readonly" value="<?= Blog::$statusArr[$detailModel['status']] ?>" autocomplete="off" lay-verify="required" class="layui-input" >
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                反馈交接类型:
            </label>
            <div class="layui-input-inline" style="width: 800px;">
                <input type="text" readonly="readonly" value="<?= ArrayHelper::getValue($type, $detailModel['type'], '') ?>" autocomplete="off" lay-verify="required" class="layui-input" >
            </div>
        </div>
        <div class="layui-form-item layui-form-text">
            <label for="desc" class="layui-form-label">
                反馈内容:
            </label>
            <div class="layui-input-block">
                <textarea placeholder="请输入反馈内容" readonly="readonly" lay-verify="required" class="layui-textarea" style="width: 800px;height: 300px;"> <?php echo $detailModel['reply']; ?></textarea>
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
            <div class="layui-form-item">
                <div class="layui-card-body">
                    <div class="video" id="video" style="width: 100%;"></div>
                </div>
            </div>
        <?php endif; ?>
    </form>
</div>


