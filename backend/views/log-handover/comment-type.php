<?php

use backend\models\LogHandoverCommentType;
use backend\models\UserInfo;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
?>

<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">白夜班日志交接</a>
        <a href="javascript:;">
          <cite>备注类型</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so">
            <input name="title" id="autofocus" class="layui-input" value="<?php echo ArrayHelper::getValue($request, 'title') ?>" autocomplete="off" placeholder="备注类型">
            <button class="layui-btn" lay-submit="" lay-filter="sreach"><i class="layui-icon">&#xe615;</i>查询</button>
            <button class="layui-btn" type="button" onclick="location.href= '<?= Url::to([$this->context->id . '/comment-type'])?>'" >
                <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>刷新
            </button>
        </form>
    </div>
    <xblock>
        <button class="layui-btn" onclick="x_admin_show('添加','<?= Url::to([$this->context->id . '/comment-type-create']) ?>',600,500)"><i class="layui-icon"></i>添加</button>
        <span class="x-right" style="line-height:30px">共有数据：<span class="count_num"><?= $pagination->totalCount ?></span> 条 ( <?= $pagination->getPageCount() ?> 页 )</span>
    </xblock>
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>备注类型名称</th>
            <th>所属分部</th>
            <th>状态</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php foreach ($dataProvider as $list): ?>
            <tr>
                <td><?= $list['id'] ?></td>
                <td><?= $list['title'] ?></td>
                <td><?= ArrayHelper::getValue($sectionArr, $list['section'], '') ?></td>
                <td><?= ArrayHelper::getValue(LogHandoverCommentType::$statusArr, $list['status'],'') ?></td>
                <td class="td-manage">
                    <button class="layui-btn layui-btn-xs layui-btn-normal" onclick="x_admin_show('编辑','<?= Url::to([$this->context->id . '/comment-type-update', 'id' => $list['id']]) ?>',600,500)"><i class="layui-icon">&#xe642;</i>编辑</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php if (empty($dataProvider)): ?>
        <p class="text-empty">-- 暂无数据 --</p>
    <?php endif; ?>
    <?= $this->render('@app/views/layouts/pagination', compact('pagination')) ?>
</div>

<?php $this->beginBlock('footer') ?>
<?php $this->endBlock() ?>
