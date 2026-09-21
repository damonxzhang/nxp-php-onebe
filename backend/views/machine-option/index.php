<?php

use yii\helpers\Html;
use backend\widgets\LayuiGridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\MachineOptionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
      </span>
</div>

<div class="x-body">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so">
            <input type="hidden" name="model_id" value="<?= $request['model_id'] ?>">
            <div class="layui-input-inline">
                <?= Html::dropDownList('status', ArrayHelper::getValue($request, 'status'), ArrayHelper::merge(['' => '状态选择'], \backend\models\MachineModel::$machineStatus), ['name' => 'position']); ?>
            </div>
            <button class="layui-btn" type="submit"><i class="layui-icon">&#xe615;</i></button>
        </form>
    </div>
    <xblock>
        <button class="layui-btn" onclick="x_admin_show('添加','<?= Url::to(['machine-option/create', 'model_id' => $request['model_id']]) ?>')"><i class="layui-icon"></i>添加</button>
        <span class="x-right" style="line-height:30px">共有数据：<span class="count_num"><?= $pagination->totalCount ?></span> 条 ( <?= $pagination->getPageCount() ?> 页 )</span>
    </xblock>
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>位置编号</th>
            <th>状态</th>
            <th>创建时间</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php foreach ($dataProvider as $list): ?>
            <tr>
                <td><?= $list['id'] ?></td>
                <td><?= $list['position'] ?></td>
                <td><?= $list['status']?'启用':'禁用' ?></td>
                <td><?= $list['create_time'] ?></td>
                <td class="td-manage">
                    <button type="button" class="layui-btn layui-btn-xs" onclick="x_admin_show('详情','<?= Url::to([$this->context->id . '/detail', 'id' => $list['id']]) ?>')"><i class="layui-icon">&#xe63c;</i>详情</button>
                    <button class="layui-btn layui-btn-xs"  onclick="x_admin_show('编辑','<?= Url::to(['machine-option/update', 'id' => $list['id']]) ?>')"><i class="layui-icon">&#xe642;</i>编辑</button>
                    <button class="layui-btn layui-btn-xs layui-btn-danger"  onclick="del(this, '<?= $list['id'] ?>', '<?= Url::to(['machine-option/del']) ?>')" ><i class="layui-icon">&#xe640;</i>删除</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?= $this->render('@app/views/layouts/pagination', compact('pagination')) ?>
</div>
