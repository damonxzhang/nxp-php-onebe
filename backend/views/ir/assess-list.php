<?php
use backend\models\Ir;
use yii\helpers\ArrayHelper;
?>
<style>
    .label th,tr{
        text-align: center;
    }
</style>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">IR</a>
        <a href="javascript:;">
          <cite>评估人专属</cite>
        </a>
      </span>
</div>
<div style="width: 80%;margin: 0 auto;">
    <table class="layui-table">
        <tr class="label">
            <th> </th>
            <th colspan="2">主管审核</th>
            <th colspan="2">负责人意见</th>
            <th colspan="2">实施人意见</th>
            <th colspan="2">结果确认</th>
            <th colspan="2">总数统计</th>
            <th colspan="2">实施活动</th>
        </tr>
        <tr class="label">
            <td>人员名单</td>
            <td>进行中</td>
            <td>已逾期</td>
            <td>进行中</td>
            <td>已逾期</td>
            <td>进行中</td>
            <td>已逾期</td>
            <td>进行中</td>
            <td>已逾期</td>
            <td>进行中</td>
            <td>已逾期</td>
            <td>未开始</td>
            <td>已过期</td>
        </tr>
    
        <?php foreach ($result as $ke => $value): ?>
            <tr>
                <td><?= ArrayHelper::getValue($value, 'name', '0')?></td>
                <td><?= ArrayHelper::getValue($value['leader'], 'go', '0')?></td>
                <td><?= ArrayHelper::getValue($value['leader'], 'overdue', '0')?></td>
                <td><?= ArrayHelper::getValue($value['head'], 'go', '0')?></td>
                <td><?= ArrayHelper::getValue($value['head'], 'overdue', '0')?></td>
                <td><?= ArrayHelper::getValue($value['work_new'], 'go', '0')?></td>
                <td><?= ArrayHelper::getValue($value['work'], 'overdue', '0')?></td>
                <td><?= ArrayHelper::getValue($value['confirm'], 'go', '0')?></td>
                <td><?= ArrayHelper::getValue($value['confirm'], 'overdue', '0')?></td>
                <td><?= ArrayHelper::getValue($value['all'], 'go', '0')?></td>
                <td><?= ArrayHelper::getValue($value['all'], 'overdue', '0')?></td>
                <td><?= ArrayHelper::getValue($value['work'], 'go', '0')?></td>
                <td><?= ArrayHelper::getValue($value['work_new'], 'overdue', '0')?></td>
            </tr>
        <?php endforeach;?>
    </table>

</div>
