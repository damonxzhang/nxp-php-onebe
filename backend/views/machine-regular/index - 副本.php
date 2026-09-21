<?php

use backend\models\UserInfo;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\models\Facility;
use backend\models\MachineRoster;
use backend\models\MachineRegular;
use backend\models\MachineCheck;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\MachineModelSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">5S</a>
        <a href="javascript:;">
          <cite>配置检查</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so">
            <div class="layui-input-inline">
                <?= Html::dropDownList('area', ArrayHelper::getValue($request, 'area'), ArrayHelper::merge(['' => '区域'], UserInfo::$areaArr), ['name' => 'area']); ?>
            </div>
            <div class="layui-input-inline">
                <?= Html::dropDownList('process', ArrayHelper::getValue($request, 'process'), ArrayHelper::merge(['' => '工序'], Facility::$processList), ['name' => 'process']); ?>
            </div>
            <div class="layui-input-inline">
                <?= Html::dropDownList('class', ArrayHelper::getValue($request, 'class'), ArrayHelper::merge(['' => '白夜班'], MachineCheck::$classList), ['name' => 'class']); ?>
            </div>
            <div class="layui-input-inline">
                <?= Html::dropDownList('status', ArrayHelper::getValue($request, 'status'), ArrayHelper::merge(['' => '状态'], MachineRegular::$statusList), ['name' => 'status']); ?>
            </div>
            <button class="layui-btn"  lay-submit="" lay-filter="sreach"><i class="layui-icon">&#xe615;</i>查询</button>
            <button class="layui-btn" type="button" onclick="location.href= '<?= Url::to([$this->context->id . '/index'])?>'" >
                <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>刷新
            </button>
        </form>
    </div>
    <xblock>
        <button class="layui-btn" onclick="x_admin_show('常规检查','<?=  Url::to(['machine-regular/day']) ?>')"><i class="layui-icon"></i>常规检查</button>
        <span class="x-right" style="line-height:30px">共有数据：<span class="count_num"><?= $pagination->totalCount ?></span> 条 ( <?= $pagination->getPageCount() ?> 页 )</span>
    </xblock>
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>区域</th>
            <th>工序</th>
            <th>白夜班</th>
            <th>状态</th>
<!--            <th>星期几</th>-->
            <th>创建人</th>
            <th>起始时间</th>
            <th>截止时间</th>
            <th>检查类型</th>
            <th>操作</th>
        </thead>
        <tbody>
         <?php if (!empty($dataProvider)): ?>
            <?php foreach ($dataProvider as $list): ?>
            <tr>
                <td><?= $list['id'] ?></td>
                <td><?= ArrayHelper::getValue(UserInfo::$areaArr, $list['area'],'') ?></td>
                <td><?= ArrayHelper::getValue(Facility::$processList, $list['process'],'') ?></td>
                <td><?= ArrayHelper::getValue(MachineRoster::$classList, $list['class'], '') ?></td>
                <td><?= ArrayHelper::getValue(MachineRegular::$statusList, $list['status'], '') ?></td>
<!--                <td>--><?//= ArrayHelper::getValue(MachineRoster::$weekList, $list['week'], '') ?><!--</td>-->
                <td><?= (new UserInfo())->getUserName($list['create_user']) ?></td>
                <td><?= $list['start_time'] ?></td>
                <td><?= $list['update_time'] ?></td>
                <td><?= ArrayHelper::getValue(MachineRegular::$typeList, $list['type'], '') ?></td>
                <td class="td-manage">
                    <button type="button" class="layui-btn layui-btn-xs" onclick="x_admin_show('详情','<?= Url::to([$this->context->id . '/view', 'id' => $list['id']]) ?>')">详情</button>
                    <button class="layui-btn layui-btn-xs layui-btn-normal" onclick="x_admin_show('编辑','<?= Url::to([$this->context->id . '/modify', 'id' => $list['id']]) ?>',350,200)">编辑</button>
                </td>
            </tr>
             <?php endforeach; ?>
         <?php endif; ?>
        </tbody>
    </table>
    <?= $this->render('@app/views/layouts/pagination', compact('pagination')) ?>
</div>
