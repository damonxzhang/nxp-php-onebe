<?php

use yii\helpers\ArrayHelper;
use backend\models\LogHandover;
use backend\models\UserInfo;
?>
<div class="x-body">
    <xblock style="height: 29px;">
        <span class="x-right" style="line-height:30px">共有数据：<span class="count_num"><?= $count ?></span> 条 </span>
    </xblock>
    <table class="layui-table">
        <thead>
        <tr>
            <th>序号</th>
            <th>标题</th>
            <th>交接类型</th>
            <th>物料类型</th>
            <th>批次类型</th>
            <th>设备类型</th>
            <th>备注类型</th>
            <th>发布人</th>
            <th>区域</th>
            <th>班别</th>
            <th>白夜班</th>
            <th>交接人</th>
            <th>交接人区域</th>
            <th>交接人班别</th>
            <th>交接人白夜班</th>
            <th>日期</th>
        </thead>
        <tbody>
        <?php foreach ($models as $list): ?>
            <tr  <?php if ($list['is_sensitive_words'] == 1): ?> style="font-weight:bold;color: red;" <?php endif; ?>>
                <td><?= $list['id'] ?></td>
                <td><?= $list['content_audit'] ?></td>
                <td><?= $list['type_name'] ?></td>
                <td><?= ArrayHelper::getValue($logHandoverTypeArr, $list['type_id'],'') ?></td>
                <td><?= ArrayHelper::getValue($logHandoverBatchArr, $list['batch_id'],'') ?></td>
                <td><?= ArrayHelper::getValue($logHandoverDeviceTypeArr, $list['device_type_id'],'') ?></td>
                <td><?= ArrayHelper::getValue($logHandoverCommentTypeArr, $list['comment_id'],'') ?></td>
                <td><?= $list['chinese_name'] ?></td>
                <td><?= ArrayHelper::getValue(UserInfo::$sectionArr, $list['section'],'') ?></td>
                <td><?= ArrayHelper::getValue(UserInfo::$shiftArr, $list['shift'],'') ?></td>
                <td><?= ArrayHelper::getValue(LogHandover::$classesArr, $list['classes'],'') ?></td>
                <td><?= $list['copulate_chinese_name'] ?></td>
                <td><?= ArrayHelper::getValue(UserInfo::$sectionArr, $list['copulate_section'],'') ?></td>
                <td><?= ArrayHelper::getValue(UserInfo::$shiftArr, $list['copulate_shift'],'') ?></td>
                <td><?= ArrayHelper::getValue(LogHandover::$classesArr, $list['copulate_classes'],'') ?></td>
                <td><?= date('Y-m-d H:i:s',strtotime($list['create_time'])) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>