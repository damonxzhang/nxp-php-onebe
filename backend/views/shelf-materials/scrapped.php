<?php

use yii\helpers\Url;
use yii\helpers\ArrayHelper;
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">Compound管理</a>
        <a href="javascript:;">
          <cite>报废物料</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>物料编号</th>
            <th>货架编号</th>
            <th>出库时间</th>
            <th>醒料起始时间</th>
            <th>醒料截止时间</th>
            <th>过期时间</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php if (!empty($dataProvider)): ?>
            <?php foreach ($dataProvider as $value): ?>
                <tr>
                    <td><?= $value['id'] ?></td>
                    <td><?= $value['cont_no'] ?></td>
                    <td><?= ArrayHelper::getValue($value->shelf, 'shelf_number','') ?></td>
                    <td><?= empty($value['library_time']) ? '' :date('Y-m-d H:i:s',strtotime($value['library_time'])) ?></td>
                    <td><?= date('Y-m-d H:i:s',strtotime($value['start_time'])) ?></td>
                    <td><?= date('Y-m-d H:i:s',strtotime($value['end_time'])) ?></td>
                    <td><?= date('Y-m-d H:i:s',strtotime($value['expire_time'])) ?></td>
                    <td class="td-manage">
                        <button type="button" class="layui-btn" onclick="x_admin_show('下架报废','<?= Url::to([$this->context->id . '/reject', 'id' => $value['id']]) ?>',460,300)">下架报废</button>
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


