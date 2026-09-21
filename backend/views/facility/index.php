<?php

use backend\models\MachineModel;
use backend\models\MachineOption;
use backend\models\MachineVendor;
use backend\models\UserInfo;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use backend\models\Facility;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\FacilitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">设备管理</a>
        <a href="javascript:;">
          <cite>设备列表</cite>
        </a>
      </span>
</div>

<div class="x-body">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so">
            <input class="layui-input" value="<?php echo ArrayHelper::getValue($request, 'name') ?>" autocomplete="off" placeholder="设备名称" name="name">
            <input class="layui-input" value="<?php echo ArrayHelper::getValue($request, 'number') ?>" autocomplete="off" placeholder="机器编号" name="number">
            <input class="layui-input" value="<?php echo ArrayHelper::getValue($request, 'model') ?>" autocomplete="off" placeholder="设备型号" name="model">
            <div class="layui-input-inline">
                <?= Html::dropDownList('status', ArrayHelper::getValue($request, 'status'), ArrayHelper::merge(['' => '状态选择'], \backend\models\MachineModel::$machineStatus), ['name' => 'position']); ?>
            </div>
            <div class="layui-input-inline">
                <?= Html::dropDownList('area', ArrayHelper::getValue($request, 'area'), ArrayHelper::merge(['' => '区域选择'], UserInfo::$areaArr), ['name' => 'area']); ?>
            </div>
            <div class="layui-input-inline">
                <?= Html::dropDownList('process', ArrayHelper::getValue($request, 'process'), ArrayHelper::merge(['' => '工序'], Facility::$processList), ['name' => 'process']); ?>
            </div>
            <button class="layui-btn" type="submit"><i class="layui-icon">&#xe615;</i></button>
            <button class="layui-btn" type="button" onclick="location.href= '<?= Url::to([$this->context->id . '/index'])?>'" >
                <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>刷新
            </button>
        </form>
    </div>
    <xblock>
        <button class="layui-btn" onclick="x_admin_show('添加设备','<?=  Url::to(['facility/create']) ?>')"><i class="layui-icon"></i>添加</button>
        <button class="layui-btn layui-bg-black" onclick="x_admin_show('VENDOR','<?=  Url::to(['machine-vendor/index']) ?>')"><i class="layui-icon"></i>VENDOR</button>
        <button class="layui-btn layui-bg-black" onclick="export_excel()"><i class="layui-icon">&#xe62f;</i>导出</button>
        <span class="x-right" style="line-height:30px">共有数据：<span class="count_num"><?= $pagination->totalCount ?></span> 条 ( <?= $pagination->getPageCount() ?> 页 )</span>
    </xblock>
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>设备名称</th>
            <th>机器编号</th>
            <th>工序</th>
            <th>区域</th>
            <th>设备型号</th>
            <th>SERIAL NO</th>
            <th>VENDOR</th>
            <th>ASSET NO</th>
            <th>负责人</th>
            <th>状态</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php foreach ($dataProvider as $list): ?>
            <tr>
                <td><?= $list['id'] ?></td>
                <td><?= $list['name'] ?></td>
                <td><?= $list['number'] ?></td>
                <td><?= !empty($list['process']) ? Facility::$processList[$list['process']]:'' ?></td>
                <td><?= !empty($list['area']) ? UserInfo::$areaArr[$list['area']]:'' ?></td>
                <td><?= MachineModel::getNameById($list['model']) ?></td>
                <td><?= $list['serial_no'] ?></td>
                <td><?= MachineVendor::getNameById($list['vendor_id']) ?></td>
                <td><?= $list['ASSET_NO'] ?></td>
                <td><?= $list['chinese_name'] ?></td>
                <td><?= $list['status']?'启用':'禁用' ?></td>
                <td class="td-manage">
                    <button type="button" class="layui-btn layui-btn-xs" onclick="x_admin_show('详情','<?= Url::to([$this->context->id . '/detail', 'id' => $list['id']]) ?>')"><i class="layui-icon">&#xe63c;</i>详情</button>
                    <button class="layui-btn layui-btn-xs"  onclick="x_admin_show('编辑','<?= Url::to(['facility/update', 'id' => $list['id']]) ?>')"><i class="layui-icon">&#xe642;</i>编辑</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?= $this->render('@app/views/layouts/pagination', compact('pagination')) ?>
</div>

<?php $this->beginBlock('footer') ?>
<script>
    function export_excel(){
        var idlist   = [];
        $('.layui-form-checked').each(function () {
            if (typeof($(this).data('id')) !="undefined" && $(this).data('id')!=0){
                idlist.push($(this).data('id'));
            }
        });
        layui.use(['jquery', 'layer'], function() {
            var $ = layui.jquery;
            $.ajax({
                type: 'GET',
                data: {idlist: idlist},
                dataType: 'json',
                url: '<?= Url::to([$this->context->id . '/export'])?>',
                success: function(res) {
                    LAY_EXCEL.exportExcel(res.data, res.msg, 'xlsx');
                }
            });
        });
    }
</script>
<?php $this->endBlock() ?>