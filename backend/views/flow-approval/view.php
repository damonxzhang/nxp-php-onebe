<?php
use backend\models\search\FlowAuditSearch;
use yii\helpers\ArrayHelper;

?>
<div class="x-body layui-anim layui-anim-up">
    <ul class="layui-timeline">
        <li class="layui-timeline-item">
            <i class="layui-icon layui-timeline-axis">&#xe643;</i>
            <div class="layui-timeline-content layui-text">
                <div class="layui-timeline-title">开始申请</div>
            </div>
        </li>
        <?php if (!empty($data)): ?>
            <?php foreach ($data as $value): ?>
                <li class="layui-timeline-item">
                    <i class="layui-icon layui-timeline-axis"><?php if ($value['type'] == 0): ?>&#xe60e;<?php else: ?>&#x1005; <?php endif; ?></i>
                    <div class="layui-timeline-content layui-text">
                        <h3 class="layui-timeline-title"><?= ArrayHelper::getValue($value,'role_name','') ?></h3>
                        <h4 class="layui-timeline-title" style="font-weight:bold ">审批人:<?= $value['chinese_name'] ?></h4>
                        <blockquote class="layui-elem-quote" style="width: 800px;">
                            <?php if ($value['type'] == 0): ?>
                                处理中...
                            <?php else: ?>
                                审批时间:<?= date('Y-m-d H:i:s',strtotime($value['create_time'])) ?><br>
                                审批状态:<?= (new FlowAuditSearch())->getStatus($value['status']) ?><br>
                                审批意见:<?= $value['audit_info'] ?><br>
                            <?php endif; ?>
                        </blockquote>
                    </div>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
        <li class="layui-timeline-item">
            <i class="layui-icon layui-timeline-axis">&#xe617;</i>
            <div class="layui-timeline-content layui-text">
                <div class="layui-timeline-title">审批结束</div>
            </div>
        </li>
    </ul>
</div>