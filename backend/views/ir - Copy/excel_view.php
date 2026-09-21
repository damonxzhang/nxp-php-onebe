<?php
use backend\models\Ir;
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

            <tr>
               <td></td>
                <td colspan="10" class="advice" align="center">
                    <h1>建议人填写</h1>
                </td>

                <td colspan="3" class="director" align="center">

                    <h1>主管填写</h1>

                </td>

                <td colspan="3" class="assess" align="center">

                    <h1>评估人填写</h1>

                </td>

                <td colspan="1" class="head" align="center">
                    <h1>负责人填写</h1>
                </td>
                <td colspan="4" class="work" align="center">

                    <h1>实施人填写</h1>

                </td>

                <td colspan="2" class="confirm" align="center">

                    <h1>主管填写</h1>

                </td>
                <td colspan="2" class="reward" align="center">

                    <h1>结果确认</h1>

                </td>
                <td colspan="3" class="appeal" align="center">

                    <h1>申诉</h1>

                </td>
            </tr>
            <tr>
                <!--创建人-->
                <td align="center"><h2>系统编号</h2></td>
                <td class="advice" align="center"><h2>主题</h2></td>
                <td class="advice" align="center"><h2>填表日期</h2></td>
                <td class="advice" align="center"><h2>填表周别</h2></td>
                <td class="advice" align="center"><h2>建议类别</h2></td>
                <td class="advice" align="center"><h2>姓名</h2></td>
                <td class="advice" align="center"><h2>PID</h2></td>
                <td class="advice" align="center"><h2>所属区域</h2></td>
                <td class="advice" align="center"><h2>gemba小组</h2></td>
                <td class="advice" align="center"><h2>班别</h2></td>
                <td class="advice" align="center"><h2>部门号</h2></td>
                <!--创建人-->
                <!--主管评审-->
                <td class="director" align="center"><h2>初评</h2></td>
                <td class="director" align="center"><h2>主管名称</h2></td>
                <td class="director" align="center"><h2>评估日期</h2></td>
                <!--主管评审-->
                <!--评估-->
                <td class="assess" align="center"><h2>建议是否接受</h2></td>
                <td class="assess" align="center"><h2>评估人</h2></td>
                <td class="assess" align="center"><h2>完成评估日期</h2></td>
                <!--评估-->
                <!--负责人填写-->
                <td class="head" align="center"><h2>负责人</h2></td>
                <!--负责人填写-->
                <!--实施人-->
                <td class="work" align="center"><h2>实施日期</h2></td>
                <td class="work" align="center"><h2>预计完成日期</h2></td>
                <td class="work" align="center"><h2>负责人</h2></td>
                <td class="work" align="center"><h2>完成状态</h2></td>
                <!--实施人-->
                <!--主管-->
                <td class="confirm" align="center"><h2>实施结果评估</h2></td>
                <td class="confirm" align="center"><h2>实施评估人</h2></td>
                <!--<td align="center">评估日期</td>  -->
                <!--主管-->
                <!--奖品-->
                <td class="reward" align="center"><h2>奖项</h2></td>
                <td class="reward" align="center"><h2>建议类型</h2></td>
                <!--奖品-->
                <!--申诉-->
                <td class="appeal" align="center"><h2>申诉结果</h2></td>
                <td class="appeal" align="center"><h2>评估人</h2></td>
                <td class="appeal" align="center"><h2>完成评估日期</h2></td>
                <!--申诉-->
             </tr>
            <?php foreach ($data as $val){ ?>
            <tr>
<!--                创建人填写-->
                <td align="center"><?=$val['id']?></td>
                <td class="advice" align="center"><?=$val['subject']?></td>
                <td class="advice" align="center"><?= (!empty($val['create_time'])? date('Y-m-d',$val['create_time']):'')?></td>
                <td class="advice" align="center"><?= (!empty($val['create_time'])? date('W',$val['create_time']):'')?></td>
                <td class="advice" align="center"><?=!empty(Ir::$adviceTypes[$val['advice_type']])?Ir::$adviceTypes[$val['advice_type']]:'';?></td>
                <td class="advice" align="center"><?=$val['chinese_name']?></td>
                <td class="advice" align="center"><?=!empty($val['pid'])?$val['pid']:''?></td>
                <td class="advice" align="center"><?=!empty($val['area'])?UserInfo::$areaArr[$val['area']] :''?></td>
                <td class="advice" align="center"><?=!empty($val['teamName'])?$val['teamName']:'' ?></td>
                <td class="advice" align="center"><?=UserInfo::$shiftArr[$val['shift']]?></td>
                <td class="advice" align="center"><?=$val['department_number']?></td>
<!--                主管填写-->
                <td class="director" align="center"><?=$val['director_advice']?></td>
                <td class="director" align="center"><?=$user->getUserName($val['director_user'])?></td>
                <td class="director" align="center"><?= (!empty($val['director_submit_time'])? date('Y-m-d',$val['director_submit_time']):'')?></td>
<!--                //评审团-->
                <td class="assess" align="center"><?=!empty($val['assess_status']) ? Ir::$assessStatusArr[$val['assess_status']] : ''?></td>
                <td class="assess" align="center"><?=$user->getUserName($val['assess_user'])?></td>
                <td class="assess" align="center"><?=(!empty($val['assess_time'])? date('Y-m-d',$val['assess_time']):'')?></td>
                <!--负责人-->
                <td class="head" align="center"><?=$user->getUserName($val['assess_user'])?></td>

                <!--实施人-->
                <td class="work" align="center"><?= (!empty($val['start_time'])? date('Y-m-d',$val['start_time']):'')?></td>
                <td class="work" align="center"><?= (!empty($val['end_time'])? date('Y-m-d',$val['end_time']):'')?> </td>
                <td class="work" align="center"><?=$user->getUserName($val['assess_user'])?></td>
                <td class="work" align="center"><?=Ir::$statusArr[$val['status']]?></td>
                <!--确认人-->
                <td class="confirm" align="center"><?=$val['complete_confirm']?></td>
                <td class="confirm" align="center"><?=$user->getUserName($val['confirm_user'])?></td>
<!--                奖励-->
                <td class="reward" align="center"><?=!empty($val['prize']) ?  Ir::$prizes[$val['prize']] :'' ?></td>
                <td class="reward" align="center"><?=!empty($val['type']) ?  Ir::$types[$val['type']] : ''?></td>

                <?php
                    $appeal = $appealModel->getAppealAndUserName($val['id']);
                ?>
                <td class="appeal" align="center"><?= !empty(Appeal::$selectList[$appeal['status']])? Appeal::$selectList[$appeal['status']] : ''?></td>
                <td class="appeal" align="center"><?=$appeal['chinese_name']?></td>
                <td class="appeal" align="center"><?=(!empty($appeal['complete_date'])? date('Y-m-d',$appeal['complete_date']):'')?></td>
            </tr>
    <?php }?>
    </table>
    </body>
</html>