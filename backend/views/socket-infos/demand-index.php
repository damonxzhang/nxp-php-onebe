<?php

use backend\models\UserInfo;
use yii\helpers\Url;
use backend\models\Blog;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">操作日志管理</a>
        <a href="javascript:;">
          <cite>盘点日志</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so">
            <input class="layui-input" id="autofocus" value="<?php echo ArrayHelper::getValue($request, 'part_no') ?>" autocomplete="off" placeholder="part_no" name="part_no">
            <input class="layui-input" placeholder="开始日" name="start_time" id="start_time" value="<?= !empty($request['start_time']) ? date('Y-m-d H:i:s',strtotime($request['start_Ptime'])) : '' ?>" autocomplete="off">
            <input class="layui-input" placeholder="截止日" name="end_time" id="end_time" value="<?= !empty($request['end_time']) ? date('Y-m-d H:i:s',strtotime($request['end_time'])) : '' ?>" autocomplete="off">
            <button class="layui-btn"  lay-submit="" lay-filter="sreach"><i class="layui-icon">&#xe615;</i>查询</button>
            <button class="layui-btn" type="button" onclick="location.href= '<?= Url::to([$this->context->id . '/demand-index'])?>'" >
                <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>刷新
            </button>
        </form>
    </div>       
    <table class="layui-table"> 
        <thead>
        <tr>
            <th>物料型号</th>
            <th>醒料中（公斤）</th>
            <th>可领取（公斤）</th>
            <th>领取中（公斤）</th>
            <th>预警中（公斤）</th>
            <th>已过期（公斤）</th>
            <th>总计（公斤）</th>
            <th>盘点时间</th>
        </thead>
        <tbody>
        <?php if (!empty($dataProvider)): ?>
            <?php foreach ($dataProvider as $value): ?>
                <tr >
                    <td><?= $value['part_no'] ?></td>
                    <td><?= $value['PROGRESS'] ?></td>
                    <td><?= $value['RECEIVE'] ?></td>
                    <td><?= $value['RECEIVED'] ?></td>
                    <td><?= $value['WARNING'] ?></td>
                    <td><?= $value['EXPIRE'] ?></td>
                    <td><?= $value['total'] ?></td>
                    <td><?= $value['create_time'] ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
    <?= $this->render('@app/views/layouts/pagination', compact('pagination')) ?>
</div>

<?php
$sUrl = Url::to('synchronization');
$export_url = Url::to([$this->context->id . '/export']) ;
$keys = ['start_time','end_time'];
$show_time = [];

$js =<<<JS
 
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
                url: '{$export_url}',
                success: function(res) {
                    if(res.status == 200){
                        LAY_EXCEL.exportExcel(res.data, res.msg, 'xlsx');
                    }else{
                        layer.msg(res.msg);
                    }
                }
            });
        });
    }

JS;
$this->registerJs($js,3);
?>

