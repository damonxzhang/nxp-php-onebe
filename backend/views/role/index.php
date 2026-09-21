<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Url;
?>

<div class="x-nav">
   <span class="layui-breadcrumb">
       <a href="javascript:;">首页</a>
       <a href="javascript:;">管理员管理</a>
       <a href="javascript:;"><cite>管理员列表</cite></a>
   </span>
</div>
<div class="x-body">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so">
            <input class="layui-input" value="<?php echo ArrayHelper::getValue($request, 'name') ?>" autocomplete="off" placeholder="角色名称" name="name">
            <button class="layui-btn" type="submit"><i class="layui-icon">&#xe615;</i></button>
            <button class="layui-btn" type="button" onclick="location.href= '<?= Url::to([$this->context->id . '/index'])?>'" >
                <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>刷新
            </button>
        </form>
    </div>
    <xblock>
        <button class="layui-btn" onclick="x_admin_show('添加','<?=  Url::to(['role/create']) ?>')"><i class="layui-icon"></i>添加</button>
        <span class="x-right" style="line-height:30px">共有数据：<span class="count_num"><?= $pagination->totalCount ?></span> 条 ( <?= $pagination->getPageCount() ?> 页 )</span>
    </xblock>
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>角色名</th>
            <th>描述</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php foreach ($dataProvider as $list): ?>
        <tr>
            <td><?= $list['id'] ?></td>
            <td><?= $list['name'] ?></td>
            <td><?= $list['description'] ?></td>
            <td class="td-manage">
                <button class="layui-btn layui-btn-xs"  onclick="x_admin_show('编辑','<?= Url::to(['role/update', 'id' => $list['id']]) ?>')"><i class="layui-icon">&#xe642;</i>编辑</button>
                <button class="layui-btn layui-btn-xs"  onclick="del(this, '<?= $list['id'] ?>', '<?= Url::to(['role/del']) ?>')" ><i class="layui-icon">&#xe640;</i>删除</button>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?= $this->render('@app/views/layouts/pagination', compact('pagination')) ?>
</div>
