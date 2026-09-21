<?php
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use backend\models\Shelf;
use yii\helpers\Html;

?>

<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">物料管理</a>
        <a href="javascript:;">
          <cite>所有物料</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so">
            <div class="layui-input-inline">
                <?= Html::dropDownList('floor', ArrayHelper::getValue($request, 'floor'), ArrayHelper::merge(['' => '楼层选择'], Shelf::$floorList), ['name' => 'floor']); ?>
            </div>
            <div class="layui-input-inline">
                <?= Html::dropDownList('part_no', ArrayHelper::getValue($request, 'part_no'), ArrayHelper::merge(['' => '型号查询'], Shelf::$partnoList), ['name' => 'part_no']); ?>
            </div>
            <div class="layui-input-inline">
                <?= Html::dropDownList('status', ArrayHelper::getValue($request, 'status'), ArrayHelper::merge(['' => '状态选择'], Shelf::$searchStatusList), ['name' => 'status']); ?>
            </div>
            <div class="layui-input-inline">
                <?= Html::dropDownList('protime', ArrayHelper::getValue($request, 'protime'), ArrayHelper::merge(['' => '解冻完成时间'], Shelf::$searchProgressList), ['name' => 'protime']); ?>
            </div>
            <button class="layui-btn"  lay-submit="" lay-filter="sreach"><i class="layui-icon">&#xe615;</i>查询</button>
            <button class="layui-btn" type="button" onclick="location.href= '<?= Url::to([$this->context->id . '/show-all'])?>'" >
                <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>刷新
            </button>
        </form>
    </div>
    <xblock>
        <span class="x-right" style="line-height:80px; font-size: 60px;" >共有：<span class="count_num"><?= $pagination->totalCount ?></span> 袋 </span>
    </xblock>
    <table class="layui-table">
        <thead>
        <tr>
            <th>区域编号</th>
            <th>Compound型号</th>
            <th>Cont NO</th>
            <th>所在楼层</th>
            <th>所在区域</th>
            <th>车辆编号</th>
            <th>状态</th>
            <th>解冻结束时间</th>
            <th>过期时间</th>
        </thead>
        <tbody>
        <?php foreach ($dataProvider as $list): ?>
            <tr>
                <?php if($list['floor'] == 2): ?>
                <td><?= $list['shelf_number'] ?></td>
                <?php else: ?>
                <td><?= $list['region'] . $list['column'].'-'.$list['row'] ?></td>
                <?php endif; ?>

                <td><?= $list['part_no']?></td>
                <td><?= $list['cont_no'] ?></td>
                <td><?= $list['floor'] ?></td>
                <td><?= $list['region'] ?></td>

                <?php if($list['floor'] == 1): ?>
                <td><?= 'CP '.$list['car_number'] ?></td>
                <?php else: ?>
                <td></td>
                <?php endif; ?>

                <td><?= ArrayHelper::getValue(Shelf::$statusArr, $list['status'],'') ?></td>
                <td><?= $list['end_time'] ?></td>
                <td><?= $list['expire_time'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?= $this->render('@app/views/layouts/pagination', compact('pagination')) ?>
</div>

<?php $this->beginBlock('footer') ?>
<?php $this->endBlock() ?>
