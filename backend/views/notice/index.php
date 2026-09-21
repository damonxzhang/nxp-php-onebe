<?php

use backend\models\UserInfo;
use backend\models\Notice;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">消息管理</a>
        <a href="javascript:;">
          <cite>通知管理</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so">
            <input name="title" class="layui-input" id="autofocus" value="<?php echo ArrayHelper::getValue($request, 'title') ?>" autocomplete="off" placeholder="通知标题">
            <input name="name" class="layui-input" value="<?php echo ArrayHelper::getValue($request, 'name') ?>" autocomplete="off" placeholder="发起人">
            <input class="layui-input" placeholder="开始日" name="start_time" id="start_time" value="<?= !empty($request['start_time']) ? date('Y-m-d H:i:s',strtotime($request['start_time'])) : '' ?>" autocomplete="off">
            <input class="layui-input" placeholder="截止日" name="end_time" id="end_time" value="<?= !empty($request['end_time']) ? date('Y-m-d H:i:s',strtotime($request['end_time'])) : '' ?>" autocomplete="off">
            <button class="layui-btn" lay-submit="" lay-filter="sreach"><i class="layui-icon">&#xe615;</i>查询</button>
            <button class="layui-btn" type="button" onclick="location.href= '<?= Url::to([$this->context->id . '/index'])?>'" >
                <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>刷新
            </button>
        </form>
    </div>
    <xblock>
        <button class="layui-btn" onclick="x_admin_show('添加通知','<?= Url::to([$this->context->id . '/create']) ?>')"><i class="layui-icon"></i>添加</button>
        <span class="x-right" style="line-height:30px">共有数据：<span class="count_num"><?= $pagination->totalCount ?></span> 条 ( <?= $pagination->getPageCount() ?> 页 )</span>
    </xblock>
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>标题</th>
            <th>内容</th>
            <th>发起人</th>
            <th>类型</th>
            <th>状态</th>
            <th>报警时间</th>
            <th>创建时间</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php foreach ($dataProvider as $list): ?>
            <tr>
                <td><?= $list['id'] ?></td>
                <td><?= $list['title'] ?></td>
                <td style="max-width: 110px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;"><?= $list['content_data'] ?></td>
                <td><?= (new UserInfo())->getUserName($list['create_user_id']) ?></td>
                <td><?= ArrayHelper::getValue(Notice::$typeArr, $list['type'],'') ?></td>
                <td><?= ArrayHelper::getValue(Notice::$statusArr, $list['status'],'') ?></td>
                <td><?= date('Y-m-d H:i:s',strtotime($list['alarm_time'])) ?></td>
                <td><?= date('Y-m-d H:i:s',strtotime($list['create_time'])) ?></td>
                <td class="td-manage">
                    <button class="layui-btn layui-btn-xs" onclick="x_admin_show('通知详情','<?= Url::to([$this->context->id . '/detail', 'id' => $list['id']])?>')"><i class="layui-icon">&#xe705;</i>查看</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php if (empty($dataProvider)): ?>
        <p class="text-empty">-- 暂无数据 --</p>
    <?php endif; ?>
    <?= $this->render('@app/views/layouts/pagination', compact('pagination')) ?>
</div>
<?php $this->beginBlock('footer') ?>
<?php $this->endBlock() ?>

