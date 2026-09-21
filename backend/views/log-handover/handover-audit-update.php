<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Url;
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
            <th>发布人</th>
            <th>区域</th>
            <th>班别</th>
            <th>日期</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php foreach ($models as $list): ?>
            <tr <?php if ($list['is_sensitive_words'] == 1): ?> style="font-weight:bold;color: red;" <?php endif; ?>>
                <td><?= $list['id'] ?></td>
                <td><?= $list['content_audit'] ?></td>
                <td><?= $list['type_name'] ?></td>
                <td><?= $list['chinese_name'] ?></td>
                <td><?= ArrayHelper::getValue(UserInfo::$sectionArr, $list['section'],'') ?></td>
                <td><?= ArrayHelper::getValue(UserInfo::$shiftArr, $list['shift'],'') ?></td>
                <td><?= date('Y-m-d H:i:s',strtotime($list['create_time'])) ?></td>
                <td class="td-manage">
                    <button class="layui-btn layui-btn-xs"  onclick="x_admin_show('编辑','<?= Url::to([$this->context->id . '/handover-audit-update-content', 'id' => $list['id']])?>',800,400)"><i class="layui-icon">&#xe642;</i>编辑</button>
                    <button class="layui-btn layui-btn-xs"  onclick="del(this,'<?= $list['id'] ?>', '<?= Url::to([$this->context->id . '/handover-audit-del']) ?>')" ><i class="layui-icon">&#xe640;</i>删除</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>