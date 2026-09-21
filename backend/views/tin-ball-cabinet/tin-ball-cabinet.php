<?php

use backend\models\ShelfMaterialsBill;
use backend\models\UserInfo;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">锡球柜管理</a>
        <a href="javascript:;">
          <cite>锡球柜列表</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so" method="get" action="bill">
            <input type="text" name="document_no" id="document_no" value="<?= ArrayHelper::getValue($request, 'document_no') ?>" onkeypress="if(event.keyCode==13) subform();"  placeholder="单据号" lay-verify="required" autocomplete="off" class="layui-input" >
            <button class="layui-btn" lay-filter="search" lay-submit="" id="submitform" onclick="subform()" type="button">查询</button>
        </form>
    </div>
    <xblock>
    <button class="layui-btn" onclick="x_admin_show('新增','<?=  Url::to([$this->context->id . '/bill-create']) ?>',700,500)"><i class="layui-icon"></i>新增</button>
        <span class="x-right" style="line-height:30px">共有数据：<span class="count_num"><?= $pagination->totalCount ?></span> 条 ( <?= $pagination->getPageCount() ?> 页 )</span>
    </xblock>
    <table class="layui-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>单据号</th>
            <th>制单人</th>
            <th>制单时间</th>
            <th>状态</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php if (!empty($dataProvider)): ?>
            <?php foreach ($dataProvider as $value): ?>
                <tr>
                    <td><?= $value['id'] ?></td>
                    <td><?= $value['document_no'] ?></td>
                    <td><?= (new UserInfo())->getUserName($value['user_id']) ?></td>
                    <td><?= date('Y-m-d H:i:s',strtotime($value['create_time'])) ?></td>
                    <td><?= ArrayHelper::getValue(ShelfMaterialsBill::$statusArr, $value['status'],'') ?></td>
                    <td class="td-manage">
                        <button type="button" class="layui-btn" onclick="x_admin_show('物料单','<?= Url::to([$this->context->id . '/print', 'id' => $value['id']]) ?>')">物料单</button>
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
