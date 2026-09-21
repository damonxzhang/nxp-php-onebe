<?php

use backend\models\search\FlowAuditSearch;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use backend\models\Flow;
use backend\models\User;
use yii\helpers\Html;
use backend\models\FlowAudit;
?>
<div class="x-nav">
    <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">审批管理</a>
        <a href="javascript:;"><cite>待我审批</cite></a>
   </span>
</div>
<div class="x-body">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so">
            <input type="text" name="title" id="autofocus" value="<?= isset($request['title']) ? $request['title'] : '' ?>" autocomplete="off" placeholder="标题" class="layui-input" style="width: 200px;">
            <input type="text" name="name" value="<?= isset($request['name']) ? $request['name'] : '' ?>" autocomplete="off" placeholder="发起人姓名" class="layui-input">
            <div class="layui-input-inline">
                <?= Html::dropDownList('flow_id', ArrayHelper::getValue($request, 'flow_id'), ArrayHelper::merge(['' => '类型'], (new Flow())->getFlow()), ['name' => 'flow_id']); ?>
            </div>
            <div class="layui-input-inline">
                <?= Html::dropDownList('status', ArrayHelper::getValue($request, 'status'), ArrayHelper::merge(['' => '状态选择'], FlowAudit::$statusArr), ['name' => 'status']); ?>
            </div>
            <button class="layui-btn" type="submit"><i class="layui-icon">&#xe615;</i>查询</button>
            <button class="layui-btn" type="button" onclick="location.href= '<?= Url::to([$this->context->id . '/index'])?>'" >
                <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>刷新
            </button>
        </form>
    </div>
    <xblock style="height: 29px;">
        <span class="x-right">共有数据：<span class="count_num"><?= $pagination->totalCount ?></span> 条 ( <?= $pagination->getPageCount() ?> 页 )</span>
    </xblock>
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>发起人</th>
            <th>标题</th>
            <th>内容</th>
            <th>类型</th>
            <th>状态</th>
            <th>发起审批时间</th>
            <th>审批时间</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php if (!empty($dataProvider)): ?>
            <?php foreach ($dataProvider as $list): ?>
                <tr>
                    <td><?= $list['id'] ?></td>
                    <td><?= $list['chinese_name'] ?></td>
                    <td><?= $list['title'] ?></td>
                    <td style="max-width: 110px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;"><?= $list['content_data'] ?></td>
                    <td><?= $list['f_time'] ?></td>
                    <td><?= FlowAudit::$statusArr[$list['status']]?></td>
                    <td><?= date('Y-m-d H:i:s',strtotime($list['create_time'])) ?></td>
                    <td><?= date('Y-m-d H:i:s',strtotime($list['audit_time'])) ?></td>
                    <td class="td-manage">
                        <?php if ($list['status'] == FlowAudit::STATUS_WAIT): ?>
                            <button class="layui-btn layui-btn-xs" onclick="x_admin_show('审批','<?= Url::to([$this->context->id . '/view', 'id' => $list['id']])?>')"><i class="layui-icon">&#xe642;</i>审批</button>
                        <?php else: ?>
                            <button class="layui-btn layui-btn-xs" onclick="x_admin_show('审批详情','<?= Url::to([$this->context->id . '/detail', 'id' => $list['approval_id']])?>')"><i class="layui-icon">&#xe705;</i>查看</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
    <?= $this->render('@app/views/layouts/pagination', compact('pagination')) ?>
</div>
<?php $this->beginBlock('footer') ?>
<?php $this->endBlock() ?>
