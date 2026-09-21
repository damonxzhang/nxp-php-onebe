<?php
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use backend\models\Shelf;
use yii\helpers\Html;
use backend\models\ShelfScattered;

?>

<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">操作管理</a>
        <a href="javascript:;">
          <cite>零散报废</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so">
            <input class="layui-input" id="autofocus" value="<?php echo ArrayHelper::getValue($request, 'part_no') ?>" autocomplete="off" placeholder="型号" name="part_no">
            
            <button class="layui-btn"  lay-submit="" lay-filter="sreach"><i class="layui-icon">&#xe615;</i>查询</button>
            <button class="layui-btn" type="button" onclick="location.href= '<?= Url::to([$this->context->id . '/index'])?>'" >
                <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>刷新
            </button>
        </form>
    </div>
    <xblock>
        <button class="layui-btn" onclick="x_admin_show('新增零散下架','<?= Url::to(['shelf-scattered/create']) ?>', 800,580)"><i class="layui-icon"></i>添加</button>
        <span class="x-right" style="line-height:30px">共有数据：<span class="count_num"><?= $pagination->totalCount ?></span> 条 ( <?= $pagination->getPageCount() ?> 页 )</span>
    </xblock>
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>下架数量（磅）</th>
            <th>报废时间</th>
            <th>创建时间</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php foreach ($dataProvider as $list): ?>
            <tr>
                <td><?= $list['id'] ?></td>
                <td><?= $list['count'] ?></td>
                <td><?= $list['scattered_time'] ?></td>
                <td><?= $list['create_time'] ?></td>
                <td>
                    <button class="layui-btn layui-btn-xs" onclick="x_admin_show('编辑','<?=  Url::to(['shelf-scattered/update', 'id' => $list['id']]) ?>', 800,580)">编辑</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?= $this->render('@app/views/layouts/pagination', compact('pagination')) ?>
</div>

<?php $this->beginBlock('footer') ?>
<?php $this->endBlock() ?>
