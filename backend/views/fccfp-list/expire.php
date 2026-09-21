<?php

use yii\helpers\ArrayHelper;
use backend\models\FccfpExpireMethod;
use yii\helpers\Url;
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">物料工序管理</a>
        <a href="javascript:;">
          <cite>过期处理</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <xblock>
        <button class="layui-btn" onclick="x_admin_show('新增','<?=  Url::to([$this->context->id . '/expire-create']) ?>', 660,550)"><i class="layui-icon"></i>新增</button>
        <span class="x-right" style="line-height:30px">共有数据：<span class="count_num"><?= $pagination->totalCount ?></span> 条 ( <?= $pagination->getPageCount() ?> 页 )</span>
    </xblock>
    <table class="layui-table" style = "width: 100%;">
        <thead>
        <tr>
            <th>ID</th>
            <th>工序</th>
            <th>处理方法</th>
            <th>状态</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php if (!empty($dataProvider)): ?>
            <?php foreach ($dataProvider as $key => $value): ?>
                <tr >
                    <td><?= $value['id'] ?></td>
                    <td><?= $value['nowspecname'] ?></td>
                    <td><?= $value['method'] ?></td>
                    <td><?= ArrayHelper::getValue(FccfpExpireMethod::$statusList, $value['status'],'') ?></td>
                    <td class="td-manage">
                       <button class="layui-btn layui-btn-xs" onclick="x_admin_show('编辑','<?=  Url::to([$this->context->id . '/expire-update', 'id' => $value['id']]) ?>', 560,450)">编辑</button>
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
