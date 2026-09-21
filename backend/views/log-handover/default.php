<?php

use backend\models\LogHandoverBatch;
use backend\models\LogHandoverDefault;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
?>

<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">白夜班日志交接</a>
        <a href="javascript:;">
          <cite>默认交接内容</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so">
            <input name="title" id="autofocus" class="layui-input" value="<?php echo ArrayHelper::getValue($request, 'title') ?>" autocomplete="off" placeholder="批次种类名称">
            <button class="layui-btn" lay-submit="" lay-filter="sreach"><i class="layui-icon">&#xe615;</i>查询</button>
            <button class="layui-btn" type="button" onclick="location.href= '<?= Url::to([$this->context->id . '/default'])?>'" >
                <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>刷新
            </button>
        </form>
    </div>
    <xblock>
        <button class="layui-btn" onclick="x_admin_show('添加','<?= Url::to([$this->context->id . '/default-create']) ?>',600,600)"><i class="layui-icon"></i>添加</button>
        <span class="x-right" style="line-height:30px">共有数据：<span class="count_num"><?= $pagination->totalCount ?></span> 条 ( <?= $pagination->getPageCount() ?> 页 )</span>
    </xblock>
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>默认内容</th>
            <th>类型</th>
            <th>所属分部</th>
            <th>物料类型</th>
            <th>批次类型</th>
            <th>设备类型</th>
            <th>备注类型</th>
            <th>状态</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php foreach ($dataProvider as $list): ?>
            <tr>
                <td><?= $list['id'] ?></td>
                <td><?= $list['title'] ?></td>
                <td><?= ArrayHelper::getValue($sectionArr, $list['section'], '') ?></td>
                <td><?= ArrayHelper::getValue(LogHandoverDefault::$typeArr, $list['type'],'') ?></td>
                <td><?= ArrayHelper::getValue($logHandoverTypeArr, $list['type_id'],'') ?></td>
                <td><?= ArrayHelper::getValue($logHandoverBatchArr, $list['batch_id'],'') ?></td>
                <td><?= ArrayHelper::getValue($logHandoverDeviceTypeArr, $list['device_type_id'],'') ?></td>
                <td><?= ArrayHelper::getValue($logHandoverCommentTypeArr, $list['comment_id'],'') ?></td>
                <td><?= ArrayHelper::getValue(LogHandoverBatch::$statusArr, $list['status'],'') ?></td>
                <td class="td-manage">
                    <button class="layui-btn layui-btn-xs layui-btn-normal" onclick="x_admin_show('编辑','<?= Url::to([$this->context->id . '/default-update', 'id' => $list['id']]) ?>',600,600)"><i class="layui-icon">&#xe642;</i>编辑</button>
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
