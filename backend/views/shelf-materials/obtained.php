<?php

use backend\models\ShelfMaterials;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">Compound管理</a>
        <a href="javascript:;">
          <cite>Compound下架</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so">
            <input class="layui-input" id="autofocus" value="<?= ArrayHelper::getValue($request, 'cont_no') ?>" autocomplete="off" placeholder="物料编号" name="cont_no">
            <input class="layui-input" id="autofocus" value="<?= ArrayHelper::getValue($request, 'part_no') ?>" autocomplete="off" placeholder="Part No" name="part_no">
            <input class="layui-input" id="autofocus" value="<?= ArrayHelper::getValue($request, 'lot_no') ?>" autocomplete="off" placeholder="Lot No" name="lot_no">
            <button class="layui-btn"  lay-submit="" lay-filter="sreach"><i class="layui-icon">&#xe615;</i>查询</button>
            <button class="layui-btn" type="button" onclick="location.href= '<?= Url::to([$this->context->id . '/obtained'])?>'" >
                <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>刷新
            </button>
        </form>
    </div>
    <xblock style="height: 29px;">
        <span class="x-right">共有数据：<span class="count_num"><?= $pagination->totalCount ?></span> 条 ( <?= $pagination->getPageCount() ?> 页 )</span>
    </xblock>
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>物料编号</th>
            <th>货架编号</th>
            <th>Part No</th>
            <th>Lot No</th>
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
                    <td><?= $value['part_no'] ?></td>
                    <td><?= $value['lot_no'] ?></td>
                    <td><?= empty($value['library_time']) ? '' : date('Y-m-d H:i:s',strtotime($value['library_time'])) ?></td>
                    <td><?= date('Y-m-d H:i:s',strtotime($value['start_time'])) ?></td>
                    <td><?= date('Y-m-d H:i:s',strtotime($value['end_time'])) ?></td>
                    <td><?= date('Y-m-d H:i:s',strtotime($value['expire_time'])) ?></td>
                    <td class="td-manage">
                        <?php if ($value['fettle'] == ShelfMaterials::FETTLE_UP): ?>
                            <button type="button" class="layui-btn" onclick="x_admin_show('物料编号进行二次确认','<?= Url::to([$this->context->id . '/obtain', 'id' => $value['id'], 'cont_no' => $value['cont_no']]) ?>',350,180)">下架</button>
                        <?php endif; ?>
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

