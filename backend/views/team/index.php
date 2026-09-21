<?php

use backend\models\Team;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
?>

<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">geamba小组</a>
      </span>
</div>
<div class="x-body">
    <div class="layui-row">

    </div>
    <xblock>
        <button class="layui-btn" onclick="x_admin_show('添加geamba小组','<?= Url::to([$this->context->id . '/create']) ?>')"><i class="layui-icon"></i>添加</button>
        <span class="x-right" style="line-height:30px">共有数据：<span class="count_num"><?= $pagination->totalCount ?></span> 条 ( <?= $pagination->getPageCount() ?> 页 )</span>
    </xblock>
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>小组组名称</th>
            <th>状态</th>
            <th>创建时间</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php foreach ($dataProvider as $list): ?>
            <tr>
                <td><?= $list['id'] ?></td>
                <td><?= $list['teamName'] ?></td>
                <td><?= ArrayHelper::getValue(Team::$statusArr, $list['status'],'') ?></td>
                <td><?= date('Y-m-d H:i:s',strtotime($list['created_at'])) ?></td>
                <td class="td-manage">
                    <button class="layui-btn layui-btn-xs layui-btn-normal" onclick="x_admin_show('编辑','<?= Url::to([$this->context->id . '/update', 'id' => $list['id']]) ?>')"><i class="layui-icon">&#xe642;</i>编辑</button>
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
