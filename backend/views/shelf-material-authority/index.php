<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\ShelfMaterialAuthoritySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">菜单路由管理</a>
        <a href="javascript:;">
          <cite>物料下架权限</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <xblock>
        <button class="layui-btn" onclick="x_admin_show('添加','<?=  Url::to([$this->context->id . '/create']) ?>',500,400)"><i class="layui-icon"></i>添加</button>
    </xblock>
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>操作员</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php foreach ($dataProvider as $list): ?>
            <tr>
                <td><?= $list['id'] ?></td>
                <td><?= $list['userInfo']['chinese_name'] ?></td>
                <td class="td-manage">
                    <button class="layui-btn layui-btn-xs"   onclick="del(this,'<?= $list['id'] ?>', '<?= Url::to([$this->context->id . '/del']) ?>')"><i class="layui-icon">&#xe640;</i>删除</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?= $this->render('@app/views/layouts/pagination', compact('pagination')) ?>

</div>
