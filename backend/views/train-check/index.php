<?php
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use backend\models\TrainCheck;
use backend\models\search\TrainCheckSearch;
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">培训考核</a>
        <a href="javascript:;">
          <cite>签到列表</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so">
            <input type="text" name="title" id="autofocus" value="<?= isset($request['title']) ? $request['title'] : '' ?>" autocomplete="off" placeholder="培训标题" class="layui-input" style="width: 200px;">
            <input type="text" name="name" value="<?= isset($request['name']) ? $request['name'] : '' ?>" autocomplete="off" placeholder="发布人" class="layui-input">
            <div class="layui-input-inline">
                <?= Html::dropDownList('status', ArrayHelper::getValue($request, 'status'), ArrayHelper::merge(['' => '签到状态'], TrainCheck::$statusArr), ['name' => 'status']); ?>
            </div>
            <button class="layui-btn" type="submit"><i class="layui-icon">&#xe615;</i>查询</button>
            <button class="layui-btn" type="button" onclick="location.href= '<?= Url::to([$this->context->id . '/index'])?>'" >
                <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>刷新
            </button>
        </form>
    </div>
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>培训标题</th>
            <th>发布人</th>
            <th>创建时间</th>
            <th>状态</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php foreach ($dataProvider as $list): ?>
            <tr>
                <td><?= $list['id'] ?></td>
                <td><?= $list['title'] ?></td>
                <td><?= $list['chinese_name'] ?></td>
                <td><?= date('Y-m-d H:i:s',strtotime($list['create_time'])) ?></td>
                <td><?= TrainCheck::$statusArr[$list['status']] ?></td>
                <td>
                    <?php if ($list['status'] == TrainCheck::STATUS_NO_SIGNED): ?>
                        <button class="layui-btn layui-btn-xs layui-btn-normal" onclick="x_admin_show('签到','<?= Url::to([$this->context->id . '/detail', 'id' => $list['train_id']]) ?>')"><i class="layui-icon">&#xe642;</i>签到</button>
                    <?php else: ?>
                        <button class="layui-btn layui-btn-xs" onclick="x_admin_show('详情','<?= Url::to([$this->context->id . '/view', 'id' => $list['train_id']])?>')"><i class="layui-icon">&#xe705;</i>详情</button>
                    <?php endif; ?>
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

