<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use backend\models\UserInfo;
use backend\models\CarDefect;
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">DPR</a>
        <a href="javascript:;">
          <cite>PkgCode</cite>
        </a>
      </span>
</div>

<div class="x-body">
    <xblock>
        <button class="layui-btn" onclick="x_admin_show('添加','<?=  Url::to(['car/pkgcode-create']) ?>',700,510)"><i class="layui-icon"></i>添加</button>
        <span class="x-right" style="line-height:30px">共有数据：<span class="count_num"><?= $pagination->totalCount ?></span> 条 ( <?= $pagination->getPageCount() ?> 页 )</span>
    </xblock>
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>大小材料</th>
            <th>CaseOutline</th>
            <th>PkgCode</th>
            <th>PkgType</th>
            <th>物料产品工程师</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php foreach ($dataProvider as $list): ?>
            <tr>
                <td><?= $list['id'] ?></td>
                <td><?= ArrayHelper::getValue(CarDefect::$types, $list['type'],'') ?></td>
                <td><?= $list['case_outline'] ?></td>
                <td><?= $list['pkg_code'] ?></td>
                <td><?= $list['pkg_type'] ?></td>
                <td><?= (new UserInfo())->getUserName($list['type_user_id']) ?></td>
                <td class="td-manage">
                    <button class="layui-btn layui-btn-xs"  onclick="x_admin_show('编辑','<?= Url::to(['car/pkgcode-update', 'id' => $list['id']]) ?>',700,510)"><i class="layui-icon">&#xe642;</i>编辑</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?= $this->render('@app/views/layouts/pagination', compact('pagination')) ?>
</div>

