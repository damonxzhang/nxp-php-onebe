<?php
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use backend\models\Flow;
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">流程管理</a>
        <a href="javascript:;">
          <cite>流程列表</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <xblock>
        <button class="layui-btn" onclick="x_admin_show('添加流程','<?= Url::to(['flow/create']) ?>')"><i class="layui-icon"></i>添加</button>
        <span class="x-right" style="line-height:30px">共有数据：<span class="count_num"><?= $pagination->totalCount ?></span> 条 ( <?= $pagination->getPageCount() ?> 页 )</span>
    </xblock>
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>流程名称</th>
            <th>流程备注</th>
            <th>创建时间</th>
            <th>流程状态</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php foreach ($dataProvider as $list): ?>
            <tr>
                <td><?= $list['id'] ?></td>
                <td><?= $list['name'] ?></td>
                <td><?= $list['remark'] ?></td>
                <td><?= date('Y-m-d H:i:s',strtotime($list['create_time'])) ?></td>
                <td><?= ArrayHelper::getValue(Flow::$statusArr, $list['status'],'') ?></td>
                <td>
                    <button class="layui-btn layui-btn-xs" onclick="x_admin_show('编辑','<?=  Url::to([$this->context->id . '/update', 'id' => $list['id']]) ?>')">编辑</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?= $this->render('@app/views/layouts/pagination', compact('pagination')) ?>
</div>

<?php $this->beginBlock('footer') ?>
<?php $this->endBlock() ?>
