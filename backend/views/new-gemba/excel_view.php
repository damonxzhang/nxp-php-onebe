<?php
use backend\models\NewGemba;
use backend\models\Appeal;
use backend\models\UserInfo;
$user = new UserInfo();
$appealModel = new Appeal();
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
<!---->
<!--    <tr>-->
<!--        <td></td>-->
<!--        <td colspan="10" class="advice" align="center">-->
<!--            <h1>建议人填写</h1>-->
<!--        </td>-->
<!---->
<!---->
<!--        <td colspan="3" class="assess" align="center">-->
<!---->
<!--            <h1>审核人填写</h1>-->
<!---->
<!--        </td>-->
<!---->
<!--        <td colspan="4" class="work" align="center">-->
<!---->
<!--            <h1>实施人填写</h1>-->
<!---->
<!--        </td>-->
<!---->
<!--        <td colspan="2" class="reward" align="center">-->
<!---->
<!--            <h1>结果确认</h1>-->
<!---->
<!--        </td>-->
<!--    </tr>-->
    <tr>
<!--         建议人-->
        <td align="center"><h2>系统编号</h2></td>
        <td class="advice" align="center"><h2>主题</h2></td>
        <td class="advice" align="center"><h2>填表日期</h2></td>
        <td class="advice" align="center"><h2>填表周别</h2></td>
        <td class="advice" align="center"><h2>Gemba类别</h2></td>
        <td class="advice" align="center"><h2>姓名</h2></td>
        <td class="advice" align="center"><h2>PID</h2></td>
        <td class="advice" align="center"><h2>所属区域</h2></td>
        <td class="advice" align="center"><h2>gemba小组</h2></td>
        <td class="advice" align="center"><h2>班别</h2></td>
        <td class="advice" align="center"><h2>部门号</h2></td>
        <td class="advice" align="center"><h2>步骤</h2></td>
        <td class="advice" align="center"><h2>是否自主完成</h2></td>
<!--         建议人-->
<!--         评估-->
        <td class="assess" align="center"><h2>建议是否接受</h2></td>
        <td class="assess" align="center"><h2>审核人</h2></td>
        <td class="assess" align="center"><h2>完成审核日期</h2></td>
<!--         评估-->
<!--         实施人-->
        <td class="work" align="center"><h2>实施日期</h2></td>
        <td class="work" align="center"><h2>预计完成日期</h2></td>
        <td class="work" align="center"><h2>实施人</h2></td>
        <td class="work" align="center"><h2>完成状态</h2></td>
<!--         实施人-->
<!---->
<!--         确认-->
        <td class="assess" align="center"><h2>确认</h2></td>
        <td class="assess" align="center"><h2>建议人</h2></td>

    </tr>
    <?php foreach ($data as $val){ ?>
        <tr>
            <!--                建议人填写-->
            <td align="center"><?=$val['number']?></td>
            <td class="advice" align="center"><?=$val['title']?></td>
            <td class="advice" align="center"><?= (!empty($val['create_time'])? date('Y-m-d',$val['create_time']):'')?></td>
            <td class="advice" align="center"><?= (!empty($val['create_time'])? date('W',$val['create_time']):'')?></td>
            <td class="advice" align="center"><?=!empty(NewGemba::$typeList[$val['type']])?NewGemba::$typeList[$val['type']]:'';?></td>
            <td class="advice" align="center"><?=$val['chinese_name']?></td>
            <td class="advice" align="center"><?=!empty($val['pid'])?$val['pid']:''?></td>
            <td class="advice" align="center"><?=!empty($val['area'])?UserInfo::$areaArr[$val['area']] :''?></td>
            <td class="advice" align="center"><?=!empty($val['teamName'])?$val['teamName']:$user->getTeamName($val['create_user']) ?></td>
            <td class="advice" align="center"><?=!empty($val['shift']) ? UserInfo::$shiftArr[$val['shift']] : UserInfo::getshift($val['create_user'])?></td>
            <td class="advice" align="center"><?=$val['department_number']?></td>
            <td class="advice" align="center"><?=!empty($val['steps']) ? NewGemba::$stepsList[$val['steps']] :''?></td>
            <td class="advice" align="center"><?=!empty($val['auto_complete']) ? NewGemba::$selectList[$val['auto_complete']] :NewGemba::$selectList[0] ?></td>

            <!--                //评审团-->
            <td class="assess" align="center"><?=!empty($val['assess_status']) ? NewGemba::$selectList[$val['assess_status']] : '自主'?></td>
            <td class="assess" align="center"><?=$user->getUserName($val['assess_user'])?></td>
            <td class="assess" align="center"><?=(!empty($val['assess_time'])? date('Y-m-d',$val['assess_time']):'')?></td>

            <!--实施人-->
            <td class="work" align="center"><?= (!empty($val['start_time'])? date('Y-m-d',$val['start_time']):'')?></td>
            <td class="work" align="center"><?= (!empty($val['end_time'])? date('Y-m-d',$val['end_time']):'')?> </td>
            <td class="work" align="center">
                <?php
//                    if(!empty($val['confirm']) && $val['confirm'] == NewGemba::SELECT_YES){
                        echo !empty($val['work_user']) ? (new UserInfo())->getUserName($val['work_user']):'';

//                    }else{
//                        echo !empty($val['create_user']) ? (new UserInfo())->getUserName($val['create_user']):'';

//                    }
                ?>
            </td>
            <td class="work" align="center"><?=!empty(NewGemba::$statusList[$val['status']]) ? NewGemba::$statusList[$val['status']] : ''?></td>
            <!--建议人-->
            <td class="confirm" align="center"><?=$val['confirm_content']?></td>
            <td class="confirm" align="center"><?=$user->getUserName($val['confirm_user'])?></td>

        </tr>
    <?php }?>
</table>
</body>
</html>