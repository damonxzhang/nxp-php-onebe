<?php
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use backend\models\BillShelf;
use yii\helpers\Html;

?>

<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">流程单管理</a>
        <a href="javascript:;">
          <cite>存储单元</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so">
            <input class="layui-input" id="autofocus" value="<?php echo ArrayHelper::getValue($request, 'shelf_number') ?>" autocomplete="off" placeholder="货架编号" name="shelf_number">
            <div class="layui-input-inline">
                <?= Html::dropDownList('status', ArrayHelper::getValue($request, 'status'), ArrayHelper::merge(['' => '状态选择'], BillShelf::$statusArr), ['name' => 'status']); ?>
            </div>
            <button class="layui-btn"  lay-submit="" lay-filter="sreach"><i class="layui-icon">&#xe615;</i>查询</button>
            <button class="layui-btn" type="button" onclick="location.href= '<?= Url::to([$this->context->id . '/index'])?>'" >
                <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>刷新
            </button>
        </form>
    </div>
    <xblock>
        <button class="layui-btn" onclick="x_admin_show('添加编号','<?= Url::to(['bill-shelf/create']) ?>', 460,350)"><i class="layui-icon"></i>添加</button>
        <span class="x-right" style="line-height:30px">共有数据：<span class="count_num"><?= $pagination->totalCount ?></span> 条 ( <?= $pagination->getPageCount() ?> 页 )</span>
    </xblock>
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>货架</th>
            <th>货架编号</th>
            <th>格子状态</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php foreach ($dataProvider as $list): ?>
            <tr>
                <td><?= $list['id'] ?></td>
                <td><?= '第' . $list['mark'] . '个货架' ?></td>
                <td><?= $list['shelf_number'] ?></td>
                <td><?= ArrayHelper::getValue(BillShelf::$statusArr, $list['status'],'') ?></td>
                <td>
                    <button class="layui-btn layui-btn-xs" onclick="x_admin_show('编辑','<?=  Url::to([$this->context->id . '/update', 'id' => $list['id']]) ?>', 460,350)">编辑</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?= $this->render('@app/views/layouts/pagination', compact('pagination')) ?>
</div>

<?php $this->beginBlock('footer') ?>
<?php $this->endBlock() ?>
