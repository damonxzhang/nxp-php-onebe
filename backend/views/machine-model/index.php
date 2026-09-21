<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\MachineModelSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">型号管理</a>
        <a href="javascript:;">
          <cite>设备型号</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so">
            <input class="layui-input" value="<?php echo ArrayHelper::getValue($request, 'name') ?>" autocomplete="off" placeholder="设备型号" name="name">
            <button class="layui-btn"  lay-submit="" lay-filter="sreach"><i class="layui-icon">&#xe615;</i>查询</button>
            <button class="layui-btn" type="button" onclick="location.href= '<?= Url::to([$this->context->id . '/index'])?>'" >
                <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>刷新
            </button>
        </form>
    </div>
    <xblock>
        <button class="layui-btn" onclick="x_admin_show('添加','<?=  Url::to(['machine-model/create']) ?>',700,400    )"><i class="layui-icon"></i>添加</button>
        <button class="layui-btn layui-bg-black" onclick="export_excel()"><i class="layui-icon">&#xe62f;</i>导出</button>
        <span class="x-right" style="line-height:30px">共有数据：<span class="count_num"><?= $pagination->totalCount ?></span> 条 ( <?= $pagination->getPageCount() ?> 页 )</span>
    </xblock>
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>设备型号</th>
            <th>所属vendor</th>
            <th>状态</th>
            <th>创建时间</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php foreach ($dataProvider as $list): ?>
            <tr>
                <td><?= $list['id'] ?></td>
                <td><?= $list['name'] ?></td>
                <td><?= $list['vendor_name'] ?></td>
                <td><?= $list['status']?'启用':'禁用' ?></td>
                <td><?= date('Y-m-d H:i:s',$list['createdt']) ?></td>
                <td class="td-manage">
                    <button class="layui-btn layui-btn-xs"  onclick="x_admin_show('编辑','<?= Url::to(['machine-model/update', 'id' => $list['id']]) ?>',700,400)"><i class="layui-icon">&#xe642;</i>编辑</button>
                    <button class="layui-btn  layui-btn-xs layui-bg-cyan"  onclick="x_admin_show('检查项','<?= Url::to(['machine-option/index', 'model_id' => $list['id']]) ?>')"><i class="layui-icon">&#xe656;</i>检查项</button>
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