<?php

?>
<html>
<head>
    <meta http-equiv=Content-Type content="text/html; charset=utf-8">
</head>
<style>

    .advice{
        background:#d8dde3;
    }
    .director{
        background:#bbeac5;
    }
    .assess{
        background:#e5f044;
    }
    .head {
        background:#d8dde3;
    }
    .work{
        background:#bbeac5;
    }
    .confirm{
        background:#e5f044;
    }
    .reward{
        background:#d8dde3;
    }
    .appeal{
        background:#bbeac5;
    }
</style>
<body>
<table border=1 cellpadding=0 cellspacing=0 width="100%" >
    <tr>
        <td align="center"><h2>编号</h2></td>
        <td class="advice" align="center"><h2>问题描述</h2></td>
        <td class="advice" align="center"><h2>工序</h2></td>
        <td class="advice" align="center"><h2>设备编号</h2></td>
        <td class="advice" align="center"><h2>需反馈人员</h2></td>
        <td class="advice" align="center"><h2>区域</h2></td>
        <td class="advice" align="center"><h2>解决状态</h2></td>
        <td class="advice" align="center"><h2>类型</h2></td>
        <td class="advice" align="center"><h2>创建日期</h2></td>
        <td class="advice" align="center"><h2>创建人</h2></td>

    </tr>
    <?php foreach ($data as $val){ ?>
        <tr>
            <td align="center"><?=$val['id']?></td>
            <td class="advice" align="center"><?=$val['things']?></td>
            <td class="advice" align="center"><?=$val['process']?></td>
            <td class="advice" align="center"><?=$val['number']?></td>
            <td class="advice" align="center"><?=$val['feedback_user']?></td>
            <td class="advice" align="center"><?=$val['area']?></td>
            <td class="advice" align="center"><?=$val['status']?></td>
            <td class="advice" align="center"><?=$val['type']?></td>
            <td class="advice" align="center"><?=$val['create_time']?></td>
            <td class="advice" align="center"><?=$val['chinese_name']?></td>
        </tr>
    <?php }?>
</table>
</body>
</html>
