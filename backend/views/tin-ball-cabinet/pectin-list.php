<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Url;
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">锡球柜管理</a>
        <a href="javascript:;">
          <cite>锡球柜胶管理</cite>
        </a>
      </span>
</div>
<div class="x-body">

    <xblock>
    <button class="layui-btn" onclick="x_admin_show('新增','<?=  Url::to([$this->context->id . '/pectin-create']) ?>', 460,400)"><i class="layui-icon"></i>新增</button>
        <span class="x-right" style="line-height:30px">共有数据：<span class="count_num"><?= $pagination->totalCount ?></span> 条 ( <?= $pagination->getPageCount() ?> 页 )</span>
    </xblock>
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>单号</th>
            <th>胶</th>
            <th>产线库存</th>
            <th>申请数量</th>
            <th>申请时间</th>
        </thead>
        <tbody>
        <?php if (!empty($dataProvider)): ?>
            <?php foreach ($dataProvider as $value): ?>
                <tr>
                    <td><?= $value['id'] ?></td>
                    <td><?= $value['document_no'] ?></td>
                    <td><?= $value['pectin_no'] ?></td>
                    <td><?= $value['product_number'] ?></td>
                    <td><?= $value['apply_number'] ?></td>
                    <td><?= date('Y-m-d H:i:s',strtotime($value['create_time'])) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
    <?= $this->render('@app/views/layouts/pagination', compact('pagination')) ?>
</div>
<?php $this->beginBlock('footer') ?>
<?php $this->endBlock() ?>
