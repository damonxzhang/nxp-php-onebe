<?php

use backend\models\LogHandoverUser;
use backend\models\UserInfo;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
?>

<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">白夜班日志交接</a>
        <a href="javascript:;">
          <cite>人员列表</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <xblock>
        <button class="layui-btn" onclick="x_admin_show('配置人员','<?= Url::to([$this->context->id . '/user-create']) ?>',450,300)"><i class="layui-icon"></i>添加</button>
        <span class="x-right" style="line-height:30px">共有数据：<span class="count_num"><?= $pagination->totalCount ?></span> 条 ( <?= $pagination->getPageCount() ?> 页 )</span>
    </xblock>
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>姓名</th>
            <th>PID</th>
            <th>班别</th>
            <th>区域</th>
            <th>状态</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php foreach ($dataProvider as $list): ?>
            <tr>
                <td><?= $list['id'] ?></td>
                <td><?= $list['chinese_name'] ?></td>
                <td><?= $list['pid'] ?></td>
                <td><?= ArrayHelper::getValue(UserInfo::$shiftArr, $list['shift'],'') ?></td>
                <td><?= ArrayHelper::getValue(UserInfo::$sectionArr, $list['section'], '') ?></td>
                <td><?= ArrayHelper::getValue(LogHandoverUser::$statusArr, $list['status'],'') ?></td>
                <td class="td-manage">
                    <button class="layui-btn layui-btn-xs layui-btn-normal" onclick="x_admin_show('编辑','<?= Url::to([$this->context->id . '/user-update', 'id' => $list['id']]) ?>',450,300)"><i class="layui-icon">&#xe642;</i>编辑</button>
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
