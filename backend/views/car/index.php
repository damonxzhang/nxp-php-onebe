<?php

use backend\models\Role;
use backend\models\Car;
use backend\models\CarPkgCode;
use backend\models\UserInfo;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">DPR</a>
        <a href="javascript:;">
          <cite>列表</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so" id="search-form">
            <input class="layui-input" name="trace_no" value="<?php echo ArrayHelper::getValue($request, 'trace_no') ?>" autocomplete="off" placeholder="TRACE NO">
            <div class="layui-input-inline">
                <?= Html::dropDownList('shift', ArrayHelper::getValue($request, 'shift'), ArrayHelper::merge(['' => '班别'], UserInfo::$shiftArr), ['name' => 'shift']); ?>
            </div>
            <input class="layui-input" name="machine_number" value="<?php echo ArrayHelper::getValue($request, 'machine_number') ?>" autocomplete="off" placeholder="机器号">
            <div class="layui-input-inline">
                <?= Html::dropDownList('status', ArrayHelper::getValue($request, 'status'), ArrayHelper::merge(['' => '状态'], Car::$statusArr), ['name' => 'status']); ?>
            </div>
            <button class="layui-btn"  lay-submit="" lay-filter="sreach"><i class="layui-icon">&#xe615;</i>查询</button>
            <button class="layui-btn" type="button" onclick="location.href= '<?= Url::to([$this->context->id . '/index'])?>'" >
                <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>刷新
            </button>
        </form>
    </div>
    <xblock>
        <?php if($check == 1){ ?>
            <button class="layui-btn" onclick="x_admin_show('新增','<?= Url::to([$this->context->id . '/create']) ?>')"><i class="layui-icon"></i>添加</button>
        <?php } ?>
        <button class="layui-btn layui-bg-black" onclick="export_excel()"><i class="layui-icon">&#xe62f;</i>导出</button>
        <span class="x-right" style="line-height:30px">共有数据：<span class="count_num"><?= $pagination->totalCount ?></span> 条 ( <?= $pagination->getPageCount() ?> 页 )</span>
    </xblock>
    <div>
        <table class="layui-table">
            <thead>
            <tr>
                <th>ID</th>
                <th>TRACE NO</th>
                <th>班别</th>
                <th>检查员</th>
                <th>带班</th>
                <th>操作员</th>
                <th>创建时间</th>
                <th>第一步状态</th>
                <th>物料产品工程师</th>
                <th>第二步状态</th>
                <th>操作</th>
            </thead>
            <tbody>
            <?php foreach ($dataProvider as $list): ?>
                <tr>
                    <td><?= $list['id'] ?></td>
                    <td><?= $list['trace_no'] ?></td>
                    <td><?= ArrayHelper::getValue(UserInfo::$shiftArr, $list['shift'],'') ?></td>
                    <td><?= (new UserInfo())->getUserName($list['create_id']) ?></td>
                    <td><?= (new Car())->getLeaderName($list['id']) ?></td>
                    <td><?= (new Car())->getOperatorChineseName($list['id']);  ?></td>
                    <td><?= date('Y-m-d H:i:s',strtotime($list['create_time'])) ?></td>
                    <td><?= ArrayHelper::getValue(Car::$statusArr, $list['status'],'') ?></td>
                    <td><?= (new CarPkgCode())->getEngineerName($list['package']) ?></td>
                    <td><?= ArrayHelper::getValue(Car::$statusSecondArr, $list['second_status'],'N/A') ?></td>
                    <td class="td-manage">
                        <?php if((new Car())->validateUser($list['id'])) {?>
                            <button class="layui-btn layui-btn-xs layui-btn-normal" onclick="x_admin_show('操作','<?= Url::to([$this->context->id . '/operation', 'id' => $list['id']]) ?>')"><i class="layui-icon">&#xe642;</i>操作</button>
                        <?php } ?>
                        <button type="button" class="layui-btn layui-btn-xs" onclick="x_admin_show('详情','<?= Url::to([$this->context->id . '/view', 'id' => $list['id']]) ?>')"><i class="layui-icon">&#xe63c;</i>详情</button>
                        <?php if(((new Car())->validateSecondUser($list['id'])) && $list['second_status'] == Car::STSTUS_SECOND_OPERATOR) {?>
                            <button class="layui-btn layui-btn-xs layui-btn-normal" onclick="x_admin_show('操作员填写','<?= Url::to([$this->context->id . '/operator', 'id' => $list['id']]) ?>')"><i class="layui-icon">&#xe642;</i>操作员填写</button>
                        <?php }elseif(((new Car())->validateSecondUser($list['id'])) && $list['second_status'] == Car::STSTUS_SECOND_TAKE) {?>
                            <button class="layui-btn layui-btn-xs layui-btn-normal" onclick="x_admin_show('带班确认','<?= Url::to([$this->context->id . '/take', 'id' => $list['id']]) ?>')"><i class="layui-icon">&#xe642;</i>带班确认</button>
                        <?php }elseif(((new Car())->validateSecondUser($list['id'])) && $list['second_status'] == Car::STSTUS_SECOND_HIGH_LEVEL) {?>
                            <button class="layui-btn layui-btn-xs layui-btn-normal" onclick="x_admin_show('High Level 确认','<?= Url::to([$this->context->id . '/judge-high-level', 'id' => $list['id']]) ?>')"><i class="layui-icon">&#xe642;</i>HighLevel确认</button>
                        <?php } ?>
                    </td>
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
<script>
    function export_excel(){
        layui.use(['jquery', 'layer'], function() {
            var fromData  = $('#search-form').serialize();
            $.ajax({
                type: 'GET',
                dataType: 'json',
                url: '<?= Url::to([$this->context->id . '/exportindex'])?>?'+fromData,
                success: function(res) {
                    LAY_EXCEL.exportExcel(res.data, res.msg, 'xlsx');
                }
            });
        });
    }
</script>
<?php $this->endBlock() ?>

