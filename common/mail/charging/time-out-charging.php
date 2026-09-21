<?php
use backend\models\Blog;
?>
<html>
<style>
    body{
    }
    table{
        width: 50%;
        text-align: center;
    }
    th{
        background: #D2D2D2;
    }
    td,th{
        border: 1px solid #F0E5F1;
        height: 30px;
    }
</style>
<body>
<h1>Hi all :</h1>
<div></div>
<div></div>
<div>领用后未归还人员名单：</div>
<div>
    <table class="layui-table">
        <thead>
        <tr>
            <th width="100%">领用人姓名</th>
            <th width="100%">班别</th>
            <th width="100%">区域</th>
            <th width="100%">领用人工号</th>
            <th width="100%">设备类型</th>
            <th width="100%">柜子编号</th>
            <th width="100%">仓位编号</th>
            <th width="100%">领取时间</th>
        </thead>
        <tbody>
        <?php foreach ($dataProvider as $list): ?>
            <tr>
                <td width="100%"><?= $list['user_name'] ?></td>
                <td width="100%"><?= $list['shift'] ?></td>
                <td width="100%"><?= $list['area'] ?></td>
                <td width="100%"><?= $list['user_pid'] ?></td>
                <td width="100%"><?= $list['type'] ?></td>
                <td width="100%"><?= $list['tank'] ?></td>
                <td width="100%"><?= $list['number'] ?></td>
                <td width="100%"><?= $list['time'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>