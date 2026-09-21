<?php
use backend\models\UserInfo;
?>
<div class="x-body layui-anim layui-anim-up">
    <form class="layui-form">
        <div class="layui-form-item">
            <label for="nickname" class="layui-form-label">
                通知标题:
            </label>
            <div class="layui-input-inline">
                <input type="text" autocomplete="off" value="<?= $notice['title'] ?>" readonly="readonly" class="layui-input" style="width: 800px;">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                发起人:
            </label>
            <div class="layui-input-inline">
                <input type="text" autocomplete="off" value="<?= (new UserInfo())->getUserName($notice['create_user_id']) ?>" readonly="readonly" class="layui-input" style="width: 800px;">
            </div>
        </div>
        <div class="layui-form-item layui-form-text">
            <label for="desc" class="layui-form-label">
                通知内容:
            </label>
            <div class="layui-input-block">
                <textarea class="layui-textarea" readonly="readonly" style="height: 150px;width: 800px;"> <?php echo $notice['content_data']; ?></textarea>
            </div>
        </div>
    </form>
</div>
<?php $this->beginBlock('footer') ?>
<?php $this->endBlock() ?>



