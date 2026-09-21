<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\models\Flow;
use backend\models\UserInfo;
use backend\models\FlowApproval;
?>
<div class="x-nav">
    <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">审批管理</a>
        <a href="javascript:;"><cite>我的审批</cite></a>
    </span>
</div>
<div class="x-body">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so">
            <div class="layui-input-inline">
                <?= Html::dropDownList('flow_id', ArrayHelper::getValue($request, 'flow_id'), ArrayHelper::merge(['' => '类型'], (new Flow())->getFlow()), ['name' => 'flow_id']); ?>
            </div>
            <div class="layui-input-inline">
                <?= Html::dropDownList('status', ArrayHelper::getValue($request, 'status'), ArrayHelper::merge(['' => '状态选择'], FlowApproval::$statusArr), ['name' => 'status']); ?>
            </div>
            <input class="layui-input" placeholder="开始日" name="start_time" id="start_time" value="<?= !empty($request['start_time']) ? date('Y-m-d H:i:s',strtotime($request['start_time'])) : '' ?>" autocomplete="off">
            <input class="layui-input" placeholder="截止日" name="end_time" id="end_time" value="<?= !empty($request['end_time']) ? date('Y-m-d H:i:s',strtotime($request['end_time'])) : '' ?>" autocomplete="off">
            <button class="layui-btn" type="submit"><i class="layui-icon">&#xe615;</i>查询</button>
            <button class="layui-btn" type="button" onclick="location.href= '<?= Url::to([$this->context->id . '/index'])?>'" >
                <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>刷新
            </button>
        </form>
    </div>
    <xblock>
        <button class="layui-btn" onclick="x_admin_show('添加审批','<?=  Url::to([$this->context->id . '/create']) ?>')"><i class="layui-icon"></i>添加</button>
        <span class="x-right" style="line-height:30px">共有数据：<span class="count_num"><?= $pagination->totalCount ?></span> 条 ( <?= $pagination->getPageCount() ?> 页 )</span>
    </xblock>
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>申请人</th>
            <th>标题</th>
            <th>内容</th>
            <th>类型</th>
            <th>状态</th>
            <th>创建时间</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php foreach ($dataProvider as $list): ?>
            <tr>
                <td><?= $list['id'] ?></td>
                <td><?= (new UserInfo())->getUserName($list['user_id']) ?></td>
                <td><?= $list['title'] ?></td>
                <td style="max-width: 110px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;"><?= $list['content_data'] ?></td>
                <td><?= (new Flow())->getNameById($list['flow_id']) ?></td>
                <td><?= FlowApproval::$statusArr[$list['status']] ?></td>
                <td><?= $list['create_time'] ?></td>
                <td class="td-manage">
                    <?php if ($list['status'] == FlowApproval::STATUS_DRAFT): ?>
                        <button class="layui-btn layui-btn-xs layui-btn-normal" onclick="x_admin_show('审批','<?= Url::to([$this->context->id . '/update', 'id' => $list['id']])?>')"><i class="layui-icon">&#xe642;</i>编辑</button>
                    <?php else: ?>
                        <button class="layui-btn layui-btn-xs" onclick="x_admin_show('审批详情','<?= Url::to([$this->context->id . '/view', 'id' => $list['id']])?>')"><i class="layui-icon">&#xe705;</i>查看</button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?= $this->render('@app/views/layouts/pagination', compact('pagination')) ?>
</div>
<?php $this->beginBlock('footer') ?>
<?php $this->endBlock() ?>
