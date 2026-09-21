<?php

use backend\models\UserInfo;
use yii\helpers\Url;
use backend\models\NoticeUser;
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">
          <cite>我的通知</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so">
            <input type="text" name="title" value="<?= isset($request['title']) ? $request['title'] : '' ?>" autocomplete="off" placeholder="标题" class="layui-input" style="width: 200px;">
            <button class="layui-btn" type="submit"><i class="layui-icon">&#xe615;</i>查询</button>
        </form>
    </div>
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>标题</th>
            <th>发布人</th>
            <th>创建时间</th>
            <th>状态</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php foreach ($dataProvider as $list): ?>
            <tr>
                <td><?= $list['id'] ?></td>
                <td><?= $list['title'] ?></td>
                <td><?= (new UserInfo())->getUserName($list['create_user_id']) ?></td>
                <td><?= $list['create_time'] ?></td>
                <td><?= NoticeUser::$statusArr[$list['status']] ?></td>
                <td>
                    <?php if ($list['status'] == NoticeUser::STATUS_UNREAD): ?>
                        <button class="layui-btn layui-btn-xs layui-btn-normal" onclick="x_admin_show('查阅','<?= Url::to(['notice-user/detail', 'id' => $list['id']]) ?>')"><i class="layui-icon">&#xe642;</i>查阅</button>
                    <?php else: ?>
                        <button class="layui-btn layui-btn-xs" onclick="x_admin_show('通知详情','<?= Url::to(['notice-user/view', 'id' => $list['notice_id']])?>')"><i class="layui-icon">&#xe705;</i>详情</button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?= $this->render('@app/views/layouts/pagination', compact('pagination')) ?>
</div>

