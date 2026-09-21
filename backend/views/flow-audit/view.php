<?php
use backend\models\search\FlowAuditSearch;
use backend\models\search\FlowApprovalSearch;
use yii\helpers\Url;

?>
<div class="x-body layui-anim layui-anim-up">
    <fieldset class="layui-elem-field layui-field-title">
        <?php if (!empty($data)): ?>
            <div class="layui-field-box" style="font-weight:bold">
                申请标题:&nbsp;&nbsp;&nbsp;&nbsp;<?= $data['title'] ?>
            </div>
            <div class="layui-field-box" style="font-weight:bold">
                审批状态:&nbsp;&nbsp;&nbsp;&nbsp;<?= (new FlowApprovalSearch())->getStatus($data['status']) ?>
            </div>
            <div class="layui-field-box" style="font-weight:bold">
                申请时间:&nbsp;&nbsp;&nbsp;&nbsp;<?= date('Y-m-d H:i:s',strtotime($data['create_time'])) ?>
            </div>
            <div class="layui-field-box" style="font-weight:bold">
                申请内容:
            </div>
            <blockquote class="layui-elem-quote" style="width: 800px;margin-left: 70px;"><?= $data['content_data'] ?></blockquote>
            <hr class="layui-bg-cyan">
            <div class="layui-field-box" style="font-weight:bold">
                审批流程:
            </div>
            <table class="layui-table" lay-size="sm" style="width: 840px;margin-left: 70px;">
                <thead>
                    <tr>
                        <th>审批节点</th>
                        <th>审批状态</th>
                        <th>审批人</th>
                        <th>审批意见</th>
                        <th>审批时间</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($data['audit'] as $value): ?>
                    <tr>
                        <td><?= $value['role_name'] ?></td>
                        <td><?= (new FlowAuditSearch())->getStatus($value['status']); ?></td>
                        <td><?= $value['chinese_name'] ?></td>
                        <td><?= $value['audit_info'] ?></td>
                        <td><?= date('Y-m-d H:i:s',strtotime($value['create_time'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </fieldset>
    <form class="layui-form" style="margin-left: 30px;">
        <input name="_csrf" type="hidden" id="_csrf" value="<?php echo \Yii::$app->request->getCsrfToken(); ?>">
        <input type="hidden" name="id" value="<?= $id ?>" >
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>
                审核状态
            </label>
            <div class="layui-input-block">
                <input type="radio" name="status" value="1" title="同意" checked>
                <input type="radio" name="status" value="2" title="驳回">
            </div>
        </div>
        <div class="layui-form-item layui-form-text">
            <label class="layui-form-label">
                审核意见
            </label>
            <div class="layui-input-block" style="width: 800px;">
                <textarea name="audit_info" placeholder="请输入审核意见" class="layui-textarea"></textarea>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-filter="update" lay-submit="">立即提交</button>
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
                $.post('<?= Url::to(['flow-audit/update']) ?>', data.field, function (res) {
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