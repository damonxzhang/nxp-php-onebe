<?php

use yii\helpers\Url;
use backend\models\UserInfo;
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">审批通知</a>
        <a href="javascript:;">
          <cite>待我审批</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>标题</th>
            <th>内容</th>
            <th>发布人</th>
            <th>生效日期</th>
            <th>结束日期</th>
            <th>首次延期日期</th>
            <th>二次延期日期</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php if (!empty($dataProvider)): ?>
            <?php foreach ($dataProvider as $value): ?>
                <tr>
                    <td><?= $value['id'] ?></td>
                    <td><?= $value['title'] ?></td>
                    <td style="max-width: 110px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;"><?= $value['content_data'] ?></td>
                    <td><?= (new UserInfo())->getUserName($value['user_id']) ?></td>
                    <td><?= $value['effective_time'] ?></td>
                    <td><?= $value['alarm_time'] ?></td>
                    <td><?= $value['first_delay_time'] ?></td>
                    <td><?= $value['second_delay_time'] ?></td>
                    <td class="td-manage">
                        <button type="button" class="layui-btn layui-btn-xs" onclick="x_admin_show('审批','<?= Url::to([$this->context->id . '/self', 'id' => $value['id']]) ?>')">审批</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
