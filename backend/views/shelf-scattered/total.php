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
        <a href="javascript:;">物料管理</a>
        <a href="javascript:;">
          <cite>货架统计</cite>
        </a>
      </span>
</div>
<div style="width: 80%;margin: 0 auto;">
    <table class="layui-table">
        <tr class="label">
            <th> </th>
            <th  style="font-weight: bold" colspan="1">醒料中</th>
            <th  style="font-weight: bold" colspan="1">可领取</th>
            <th  style="font-weight: bold" colspan="1">领取中</th>
            <th  style="font-weight: bold" colspan="1">预警中</th>
            <th  style="font-weight: bold" colspan="1">已过期</th>
            <th  style="font-weight: bold" colspan="1">总计</th>
        </tr>

        <?php
            $total_2 = 0;
            $total_1 = 0;
            $total_7 = 0;
            $total_5 = 0;
            $total_6 = 0;
        ?>
        <?php foreach ($result as $ke => $value): ?>
            <tr>
                <td style="font-weight: bold"><?= $ke ?></td>
                <td style="color: #0000FF"><?= $value[2] ?></td>
                <td style="color: #00FF00"><?= $value[1] ?></td>
                <td><?= $value[7] ?></td>
                <td><?= $value[5] ?></td>
                <td style="color: red"><?= $value[6] ?></td>
                <td><?= $value[2]+$value[1]+$value[7]+$value[5]+$value[6] ?></td>
            </tr>
            <?php
            $total_2 += $value[2];
            $total_1 += $value[1];
            $total_7 += $value[7];
            $total_5 += $value[5];
            $total_6 += $value[6];
            ?>
        <?php endforeach;?>
        <tr>
            <td style="font-weight: bold">总计</td>
            <td style="color: #0000FF;font-weight: bold"><?= $total_2 ?></td>
            <td style="color: #00FF00;font-weight: bold"><?= $total_1 ?></td>
            <td style="font-weight: bold"><?= $total_7 ?></td>
            <td style="font-weight: bold"><?= $total_5 ?></td>
            <td style="color: red;font-weight: bold"><?= $total_6 ?></td>
            <td style="font-weight: bold"><?= $total_2+$total_1+$total_7+$total_5+$total_6 ?></td>
        </tr>
    </table>

</div>
