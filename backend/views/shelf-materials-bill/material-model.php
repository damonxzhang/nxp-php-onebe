<?php

use backend\models\ShelfMaterialsModel;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">Compound管理</a>
        <a href="javascript:;">
          <cite>Compound上架</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so" method="get" action="material-model">
            <input class="layui-input" id="autofocus" value="<?php echo ArrayHelper::getValue($request, 'part_no') ?>" autocomplete="off" placeholder="PART NO" name="part_no">
            <button class="layui-btn"  lay-submit="" lay-filter="sreach"><i class="layui-icon">&#xe615;</i>查询</button>
        </form>
    </div>
    <xblock>
    <button class="layui-btn" onclick="x_admin_show('新增','<?=  Url::to([$this->context->id . '/material-model-create']) ?>', 660,550)"><i class="layui-icon"></i>新增</button>
        <span class="x-right" style="line-height:30px">共有数据：<span class="count_num"><?= $pagination->totalCount ?></span> 条 ( <?= $pagination->getPageCount() ?> 页 )</span>
    </xblock>
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>PART NO</th>
            <th>DM 12NC</th>
            <th>解冻时间</th>
            <th>过期时间</th>
            <th>材料类型</th>
            <th>NO BOX</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php if (!empty($dataProvider)): ?>
            <?php foreach ($dataProvider as $value): ?>
                <tr>
                    <td><?= $value['id'] ?></td>
                    <td><?= $value['part_no'] ?></td>
                    <td><?= $value['dm_nc'] ?></td>
                    <td><?= $value['thawing_time'] ?>小时</td>
                    <td><?= $value['expiry_time'] ?>小时</td>
                    <td><?= $value['type'] == 1 ? '小材料' : '大材料'?></td>
                    <td><?= ArrayHelper::getValue(ShelfMaterialsModel::$noboxArr, $value['no_box'],'') ?></td>
                    <td class="td-manage">
                       <button class="layui-btn layui-btn-xs" onclick="x_admin_show('编辑','<?=  Url::to([$this->context->id . '/material-model-update', 'id' => $value['id']]) ?>', 560,450)">编辑</button>
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
