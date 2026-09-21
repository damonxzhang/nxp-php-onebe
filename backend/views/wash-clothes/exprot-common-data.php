<?php
use backend\models\NewGemba;
use backend\models\Appeal;
use backend\models\UserInfo;
$user = new UserInfo();
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
        <td class="advice" align="center"><h2>姓名</h2></td>
        <td class="advice" align="center"><h2>PID</h2></td>
        <td class="advice" align="center"><h2>静电衣名称</h2></td>
        <td class="advice" align="center"><h2>RFID</h2></td>
        <td class="advice" align="center"><h2>清洗时间</h2></td>
        <td class="advice" align="center"><h2>领取时间</h2></td>
        <td class="advice" align="center"><h2>刷卡时间</h2></td>
    </tr>
    <?php if (!empty($data)): ?>
        <?php foreach ($data as $val){ ?>
            <tr>
                <td class="advice" align="center"><?=$val['chinese_name']?></td>
                <td class="advice" align="center"><?=!empty($val['pid'])?$val['pid']:''?></td>
                <td class="advice" align="center"></td>
                <td class="advice" align="center"></td>
                <td class="advice" align="center"></td>
                <td class="advice" align="center"></td>
                <td class="advice" align="center"></td>
            </tr>
        <?php }?>
    <?php endif; ?>
</table>
</body>
</html>