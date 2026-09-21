
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
          <cite>大材料统计</cite>
        </a>
      </span>
</div>
<div style="width: 80%;margin: 0 auto;">
    <table class="layui-table">
        <tr class="label">
            <th> </th>
            <th  style="font-weight: bold" colspan="1">醒料中</th>
            <th  style="font-weight: bold" colspan="1">可领取</th>
            <th  style="font-weight: bold" colspan="1">预警中</th>
            <th  style="font-weight: bold" colspan="1">总计</th>
        </tr>
        <?php
            $total_1 = 0;
            $total_2 = 0;
            $total_3 = 0;
        ?>
        <?php foreach ($result as $key => $value): ?>
            <tr>
                <td style="font-weight: bold"><?= $key ?></td>
                <td style="color: #0000FF"><?= $value[1] / 2 ?></td>
                <td style="color: #00FF00"><?= $value[2] / 2 ?></td>
                <td ><?= $value[3] / 2 ?></td>
                <td><?= ($value[1] / 2) +($value[2] / 2)  ?></td>
            </tr>
            <?php
            $total_1 += $value[1] / 2;
            $total_2 += $value[2] / 2;
            $total_3 += $value[3] / 2;
            ?>
        <?php endforeach;?>
        <tr>
            <td style="font-weight: bold">总计</td>
            <td style="color: #0000FF;font-weight: bold"><?= $total_1 ?></td>
            <td style="color: #00FF00;font-weight: bold"><?= $total_2 ?></td>
            <td style="font-weight: bold"><?= $total_3 ?></td>
            <td style="font-weight: bold"><?= $total_1+$total_2 ?></td>
        </tr>
    </table>

</div>
