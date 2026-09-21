<?php


use backend\models\UserInfo;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\models\Gemba;
?>

<div class="x-body">

    <xblock>
        <button class="layui-btn" onclick="x_admin_show('添加异常','<?= Url::to([$this->context->id . '/create']) ?>')"><i class="layui-icon"></i>添加
        </button>
        <span class="x-right" style="line-height:30px">共有数据：<span class="count_num"><?= $pagination->totalCount ?></span> 条 ( <?= $pagination->getPageCount() ?> 页 )</span>
    </xblock>
    <div>
        <table class="layui-table">
            <thead>
            <tr>
                <th>ID</th>
                <th>编号</th>
                <th>异常信息</th>
                <th>类型</th>
                <th>状态</th>
                <th>提交人</th>
                <th>处理人</th>
                <th>处理信息</th>
                <th>完成人</th>
                <th>完成信息</th>
                <th>处理时间</th>
                <th>完成时间</th>
                <th>操作</th>
            </thead>
            <tbody>
            <?php foreach ($models as $model): ?>
                <tr>
                    <td><?= $model['id'] ?></td>
                    <td><?= !empty($model->warehouse->number) ?$model->warehouse->number:'' ?></td>
                    <td><?= $model['abnormal_msg'] ?></td>
                    <td><?= $model::$typeArr[$model['type']] ?></td>
                    <td><?= $model::$statusArr[$model['status']]  ?></td>
                    <td><?= (new UserInfo)->getUserName($model['operation_user_id']) ?></td>
                    <td><?= $model['deal_with_user_id'] ?></td>
                    <td><?= $model['deal_with_msg'] ?></td>
                    <td><?= (new UserInfo)->getUserName($model['admin_user_id']) ?></td>
                    <td><?= $model['complete_msg'] ?></td>
                    <td><?= $model['deal_with_time'] ?></td>
                    <td><?= $model['complete_time'] ?></td>
                    <td class="td-manage">
                        <?php if($model['status'] !== $model::OK_STATUS):?>
                        <button type="button" class="layui-btn layui-btn-xs" onclick="x_admin_show('处理异常','<?= Url::to([$this->context->id . '/handle', 'id' => $model['id']]) ?>')"> 处理</button>
                        <?php endif;?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if (empty($models)): ?>
        <p class="text-empty">-- 暂无数据 --</p>
    <?php endif; ?>
    <?= $this->render('@app/views/layouts/pagination', compact('pagination')) ?>
</div>
<?php $this->beginBlock('footer') ?>
<?php $this->endBlock() ?>

