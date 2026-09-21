<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use backend\models\UserInfo;
use yii\helpers\Url;
use backend\models\ShelfMaterialsBill;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\MachineOptionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
      </span>
</div>

<div class="x-body">

    <xblock style="height:30px">
        <span class="x-right" style="line-height:30px">共有数据：<span class="count_num"><?= $pagination->totalCount ?></span> 条 ( <?= $pagination->getPageCount() ?> 页 )</span>
    </xblock>
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>单据号</th>
            <th>提交人</th>
            <th>制单时间</th>
            <th>异常信息</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php if (!empty($dataProvider)): ?>
            <?php foreach ($dataProvider as $value): ?>
                <tr>
                    <td><?= $value['id'] ?></td>
                    <td><?= (new ShelfMaterialsBill())->getDocumentNo($value['bill_id']) ?></td>
                    <td><?= (new UserInfo())->getUserName($value['user_id']) ?></td>
                    <td><?= date('Y-m-d H:i:s',strtotime($value['create_time'])) ?></td>
                    <td><?= $value['abnormal'] ?></td>

                    <td class="td-manage">
                        <?php if ($value['status'] == 0): ?>
                            <button class="layui-btn"  onclick="x_admin_show('填写','<?= Url::to([$this->context->id . '/bill-abnormal-create', 'bill_id' => $value['bill_id']]) ?>', 560,350)">填写</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
    <?= $this->render('@app/views/layouts/pagination', compact('pagination')) ?>
</div>
