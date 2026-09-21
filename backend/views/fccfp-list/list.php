<?php

use yii\helpers\ArrayHelper;
use backend\models\FccfpList;
use yii\helpers\Url;
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">物料工序管理</a>
        <a href="javascript:;">
          <cite>有效时效</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so" method="get" action="list">
            <input class="layui-input" id="autofocus" value="<?php echo ArrayHelper::getValue($request, 'containername') ?>" autocomplete="off" placeholder="型号" name="containername">
            <button class="layui-btn"  lay-submit="" lay-filter="sreach"><i class="layui-icon">&#xe615;</i>查询</button>
        </form>
    </div>
    <xblock>
        <span class="x-right" style="line-height:30px">共有数据：<span class="count_num"><?= $pagination->totalCount ?></span> 条 ( <?= $pagination->getPageCount() ?> 页 )</span>
    </xblock>
    <table class="layui-table" style = "width: 100%;">
        <thead>
        <tr>
            <th>ID</th>
            <th>型号</th>
            <th>开始工序</th>
            <th>结束工序</th>
            <th>状态</th>
            <th>出炉日期</th>
            <th>开始时间</th>
            <th>结束时间</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php if (!empty($dataProvider)): ?>
            <?php foreach ($dataProvider as $key => $value): ?>
                <tr >
                    <td><?= $value['id'] ?></td>
                    <td><?= $value['containername'] ?></td>
                    <td>Marking</td>
                    <td>Fluxclean</td>
                    <td><?= ArrayHelper::getValue(FccfpList::$statusList, $value['status'],'') ?></td>
                    <td><?= date('Y-m-d H:i:s', strtotime($value['nldpmc_trackout'])) ?></td>
                    <td><?= date('Y-m-d H:i:s', strtotime($value['fcmark_clean_start_time'])) ?></td>
                    <td><?= !empty($value['fluxclean_end_time']) ? date('Y-m-d H:i:s', strtotime($value['fluxclean_end_time'])) : '';  ?></td>
                    <td class="td-manage">
                        <button type="button" class="layui-btn layui-btn-xs" onclick="x_admin_show('详情','<?= Url::to([$this->context->id . '/list-detail', 'id' => $value['id']]) ?>')"><i class="layui-icon">&#xe63c;</i>详情</button>
                        <button class="layui-btn layui-btn-xs"  onclick="x_admin_show('编辑','<?= Url::to([$this->context->id . '/list-update', 'id' => $value['id']]) ?>')"><i class="layui-icon">&#xe642;</i>编辑</button>
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
