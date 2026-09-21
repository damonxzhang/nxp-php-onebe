<?php
use yii\helpers\Url;
?>

<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">菜单路由管理</a>
        <a href="javascript:;">
          <cite>菜单列表</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so">
            <input type="text" name="search[name]"  placeholder="菜单名" value="<?= isset($search['name']) ? $search['name'] : '' ?>" autocomplete="off" class="layui-input">
            <button class="layui-btn" type="submit"><i class="layui-icon">&#xe615;</i></button>
            <button class="layui-btn" type="button" onclick="location.href= '<?= Url::to([$this->context->id . '/index'])?>'" >
                <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>刷新
            </button>
        </form>
    </div>
    <xblock>
        <button class="layui-btn" onclick="x_admin_show('添加','<?=  Url::to([$this->context->id . '/create']) ?>',500,400)"><i class="layui-icon"></i>添加</button>
        <span class="x-right" style="line-height:30px">共有数据：<span class="count_num"><?= $pagination->totalCount ?></span> 条 ( <?= $pagination->getPageCount() ?> 页 )</span>
    </xblock>
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>菜单名</th>
            <th>图标</th>
            <th>排序</th>
            <th>状态</th>
            <th>创建时间</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php foreach ($models as $list): ?>
            <tr>
                <td><?= $list['id'] ?></td>
                <td><?= $list['name'] ?></td>
                <td><i class="iconfont"><?= $list['icon'] ?></i></td>
                <td><?= $list['sort'] ?></td>
                <td><?= $list['is_show'] == 1 ? '显示' : '隐藏' ?></td>
                <td><?= date('Y-m-d H:i:s',strtotime($list['create_time'])) ?></td>
                <td class="td-manage">
                    <button class="layui-btn layui-btn-xs"  onclick="x_admin_show('子菜单','<?= Url::to([$this->context->id . '/submenu', 'id' => $list['id']])?>')"><i class="iconfont iconzicaidan"></i>子菜单</button>
                    <button class="layui-btn layui-btn-xs"  onclick="x_admin_show('编辑','<?= Url::to([$this->context->id . '/update', 'id' => $list['id']])?>',500,400)"><i class="layui-icon">&#xe642;</i>编辑</button>
                    <button class="layui-btn layui-btn-xs"   onclick="del(this,'<?= $list['id'] ?>', '<?= Url::to([$this->context->id . '/del']) ?>')"><i class="layui-icon">&#xe640;</i>删除</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?= $this->render('@app/views/layouts/pagination', compact('pagination')) ?>
</div>

<?php $this->beginBlock('footer') ?>
<?php $this->endBlock() ?>
