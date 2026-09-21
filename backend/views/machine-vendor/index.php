<?php

use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\MachineModelSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
      </span>
</div>

<div class="x-body">
    <xblock>
        <button class="layui-btn" onclick="x_admin_show('添加','<?=  Url::to(['machine-vendor/create']) ?>',700,400)"><i class="layui-icon"></i>添加</button>
        <span class="x-right" style="line-height:30px">共有数据：<span class="count_num"><?= $pagination->totalCount ?></span> 条 ( <?= $pagination->getPageCount() ?> 页 )</span>
    </xblock>
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>VENDOR</th>
            <th>状态</th>
            <th>创建时间</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php foreach ($dataProvider as $list): ?>
            <tr>
                <td><?= $list['id'] ?></td>
                <td><?= $list['name'] ?></td>
                <td><?= $list['status']?'启用':'禁用' ?></td>
                <td><?= $list['create_time'] ?></td>
                <td class="td-manage">
                    <button class="layui-btn layui-btn-xs"  onclick="x_admin_show('编辑','<?= Url::to(['machine-vendor/update', 'id' => $list['id']]) ?>',700,400)"><i class="layui-icon">&#xe642;</i>编辑</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?= $this->render('@app/views/layouts/pagination', compact('pagination')) ?>
</div>
