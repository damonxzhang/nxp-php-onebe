<?php

use yii\helpers\Url;
use backend\models\UserInfo;
?>
<div class="x-body layui-anim layui-anim-up">
    <form class="layui-form">
        <div class="layui-form-item">
            <input type="hidden" name = "id" autocomplete="off" value="<?= $notice['id'] ?>" >
			<input type="hidden" name = "notice_user_id" autocomplete="off" value="<?= $noticeUserId ?>" >
            <label for="" class="layui-form-label"></label>
            <button class="layui-btn" lay-filter="check" lay-submit="">
                <i class="layui-icon">&#xe605;</i>一键查阅
            </button>
        </div>
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
<script>

    layui.use(['form','layer'], function(){
        $ = layui.jquery;
        var form = layui.form ,layer = layui.layer;

        //监听提交
        form.on('submit(check)', function(data){
            layer.load(3);
            $.post('<?= Url::to([$this->context->id . '/change-status']) ?>', data.field, function (res) {
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



