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
        <a href="javascript:;">日志管理</a>
        <a href="javascript:;">
          <cite>操作日志</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so">
            <input class="layui-input" placeholder="批号查询" name="part_no" value="<?php echo ArrayHelper::getValue($request, 'part_no') ?>"  autocomplete="off">
            <input class="layui-input" placeholder="日期筛选" name="created_at" id="startTime" value="<?php echo ArrayHelper::getValue($request, 'created_at') ?>"  autocomplete="off">
            <button class="layui-btn"  lay-submit="" lay-filter="sreach"><i class="layui-icon">&#xe615;</i>查询</button>
        </form>
    </div>
    <div class="layui-row layui-form" >
        <button type="button" class="layui-btn layui-btn-xs" onclick="x_admin_show('编辑','<?= Url::to([$this->context->id . '/check-form']) ?>')">导入</button>
    </div>
    <table class="layui-table">
        <thead>
        <tr>
            <th>批号</th>
            <th>预计需求量</th>
            <th>修改后数量</th>
            <th>创建时间</th>
        </thead>
        <tbody>
        <?php if (!empty($data)): ?>
            <?php foreach ($data as $value): ?>
                <tr >
                    <td  style="max-width: 310px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;"><?= $value['part_no'] ?></td>
                    <td style="max-width: 80px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;"><?= $value['number'] ?></td>
                    <td style="max-width: 80px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;"><?= $value['check_number'] ?></td>
                    <td style="max-width: 150px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;"><?= $value['created_at'] ?></td>
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

