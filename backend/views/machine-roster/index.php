<?php

use backend\models\MachineRoster;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\models\UserInfo;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\MachineModelSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class = "x-body">
    <div class = "layui-row">
        <form class = "layui-form layui-col-md12 x-so">
             <div class = "layui-input-inline">
                <?= Html::dropDownList('class', ArrayHelper::getValue($request, 'class'), ArrayHelper::merge(['' => '白夜班'], MachineRoster::$classList), ['name' => 'class']); ?>
            </div>
            <div class = "layui-input-inline">
                <?= Html::dropDownList('shift', ArrayHelper::getValue($request, 'shift'), ArrayHelper::merge(['' => '班别'], MachineRoster::$shiftListArr), ['name' => 'class']); ?>
            </div>
            <input class="layui-input" placeholder="起始日期" name="start_time" id="start_time" value="<?= !empty($request['start_time']) ? $request['start_time'] : '' ?>" autocomplete="off">
            <input class="layui-input" placeholder="截止日期" name="end_time" id="end_time" value="<?= !empty($request['end_time']) ? $request['end_time'] : '' ?>" autocomplete="off">
            <button class = "layui-btn" lay-submit = "" lay-filter = "sreach"><i class = "layui-icon">&#xe615;</i>查询</button>
            <button class = "layui-btn" type = "button" onclick = "location.href= '<?= Url::to([$this->context->id . '/index']) ?>'">
                <i class = "layui-icon layui-icon-refresh" style = "line-height:30px"></i>刷新
            </button>
        </form>
    </div>
    <xblock>
        <button class = "layui-btn layui-bg-black" onclick = "x_admin_show('导入数据','<?= Url::to(['machine-roster/import']) ?>',800,600)"><i class = "layui-icon">&#xe656;</i>导入数据</button>
        <span class = "x-right" style = "line-height:30px">共有数据：<span class = "count_num"><?= $pagination->totalCount ?></span> 条 ( <?= $pagination->getPageCount() ?> 页 )</span>
    </xblock>
    <table class = "layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>工作日期</th>
            <th>白夜班</th>
            <th>班别</th>
            <th>星期</th>
        </thead>
        <tbody>
         <?php if (!empty($dataProvider)): ?>
             <?php foreach ($dataProvider as $list): ?>
                <tr>
                    <td><?= $list['id'] ?></td>
                    <td><?= $list['work_date'] ?></td>
                    <td><?= ArrayHelper::getValue(MachineRoster::$classList, $list['class'], '') ?></td>
                    <td><?= ArrayHelper::getValue(MachineRoster::$shiftListArr, trim($list['shift']), '') ?></td>
                    <td><?= ArrayHelper::getValue(MachineRoster::$weekList, $list['week'], '') ?></td>
                </tr>
             <?php endforeach; ?>
         <?php endif; ?>
        </tbody>
    </table>
    <?= $this->render('@app/views/layouts/pagination', compact('pagination')) ?>
</div>
