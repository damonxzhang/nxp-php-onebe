<?php

use backend\models\Role;
use backend\models\User;
use backend\models\UserInfo;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">人员临时卡管理</a>
        <a href="javascript:;">
          <cite>借还记录</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so">
            <input class="layui-input" name="number" id="autofocus"  autocomplete="off" placeholder="临时卡号">

            <button class="layui-btn"  lay-submit="" lay-filter="sreach"><i class="layui-icon">&#xe615;</i>查询</button>
            <button class="layui-btn" type="button" onclick="location.href= '<?= Url::to([$this->context->id . '/record'])?>'" >
                <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>刷新
            </button>
        </form>
    </div>
    <div>
        <table class="layui-table">
            <thead>
            <tr>
                <th>ID</th>
                <th>临时卡号</th>
                <th>借还状态</th>
                <th>当前使用人</th>
                <th>操作时间</th>
            </thead>
            <tbody>
            <?php foreach ($dataProvider as $list): ?>
                <tr>
                    <td><?= $list['id'] ?></td>
                    <td><?= (new \backend\models\UserTempCard())->getCardNumber($list['temp_card_id']) ?></td>
                    <td><?= ArrayHelper::getValue(\backend\models\UserTempCardRecord::$statusArr,$list['status'],'') ?></td>
                    <td><?= (new \backend\models\UserInfo())->getUserName($list['user_id']) ?></td>
                    <td><?= $list['created_at'] ?></td>

                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if (empty($dataProvider)): ?>
        <p class="text-empty">-- 暂无数据 --</p>
    <?php endif; ?>
    <?= $this->render('@app/views/layouts/pagination', compact('pagination')) ?>
</div>
<?php $this->beginBlock('footer') ?>
<?php $this->endBlock() ?>

