<?php

use backend\models\UserInfo;
use backend\models\SocketInfos;
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
          <cite>socket日志</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>行为</th>
            <th>socket消息</th>
            <th>状态</th>
            <th>时间</th>
        </thead>
        <tbody>
        <?php if (!empty($dataProvider)): ?>
            <?php foreach ($dataProvider as $value): ?>
                <tr >
                    <td><?= $value['id'] ?></td>
                    <td><?= SocketInfos::$actionList[$value['action']]  ?></td>
                    <td><?= $value['value']  ?></td>
                    <td><?= SocketInfos::$statusList[$value['status']]  ?></td>
                    <td style="max-width: 150px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;"><?= $value['create_time'] ?></td>
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

