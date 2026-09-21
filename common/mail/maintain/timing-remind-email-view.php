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
<h1>Daily Anomaly Summary</h1>
<div>Hello All</div>
<div>Daily Anomaly Summary 如下</div>

<h3>进行中</h3>
<div>
    <table>
        <tr class="th">
            <th>编号 </th>
            <th>问题类型</th>
            <th>异常类型</th>
            <th>问题物料批号</th>
            <th>工序 </th>
            <th>创建人 </th>
            <th>班别 </th>
            <th>带班主管</th>
            <th>需反馈人员 </th>
            <th>其它需反馈人员 </th>
            <th>机台号 </th>
        </tr>
            <?php foreach ($params as $val): if($val['status'] != \backend\models\Maintain::STATUS_MARCH) continue; ?>
                <?php
                if(!is_int($val['create_user'])){
                    continue;
                }
                    
                $createUser = \backend\models\UserInfo::findOne($val['create_user']);
                 
                if(!empty($val['f_id'])){
                    $facility = \backend\models\Facility::findOne($val['f_id']);
                }
                ?>
            <tr class="content">
                <td><a href="<?=\Yii::$app->request->hostInfo.'/maintain/view?id='.$val['id']?>"><?=$val['number']?></a> </td>
                <td><?= \yii\helpers\ArrayHelper::getValue(\backend\models\Maintain::$problem, $val['type'] ,'')?></td>
                <td><?= \yii\helpers\ArrayHelper::getValue(\backend\models\MaintainType::getTypeList(), $val['things'] ,'')?></td>
                <td><?=$val['material_number']?></td>
                <td><?= \yii\helpers\ArrayHelper::getValue(\backend\models\Facility::$processList, $val['process'] ,'')?></td>
                <td><?= $createUser->chinese_name?> </td>
                <td><?= \yii\helpers\ArrayHelper::getValue(\backend\models\UserInfo::$shiftArr, $createUser->shift ,'')?> </td>
                  <td><?= (new \backend\models\UserInfo())->getUserName($createUser->leader_uid)?></td>
                <td><?=(new \backend\models\Facility())->getFacilityUserName($val['f_id'])?> </td>
                <td><?=(new \backend\models\UserInfo())->getUserName($val['other_feedback_user'])?> </td>
                <td><?=$facility->number?> </td>
            </tr>
            <?php endforeach;?>
    </table>
</div>
<h3>已完成</h3>
<div>
    <table>
        <tr class="th">
            <th>编号 </th>
            <th>问题类型</th>
            <th>异常类型</th>
            <th>问题物料批号</th>
            <th>工序 </th>
            <th>创建人 </th>
            <th>班别 </th>
            <th>带班主管</th>
            <th>需反馈人员 </th>
            <th>其它需反馈人员 </th>
            <th>机台号 </th>
        </tr>
           <?php foreach ($params as $val): if($val['status'] == \backend\models\Maintain::STATUS_MARCH) continue; ?>
                <?php
                    if(!is_int($val['create_user'])){
                        continue;
                    }
                    $createUser = \backend\models\UserInfo::findOne($val['create_user']);
                    if(!empty($val['f_id'])){
                        $facility = \backend\models\Facility::findOne($val['f_id']);
                    }
                ?>
            <tr class="content">

                <td><a href="<?=\Yii::$app->request->hostInfo.'/maintain/view?id='.$val['id']?>"><?=$val['number']?></a> </td>
                <td><?= \yii\helpers\ArrayHelper::getValue(\backend\models\Maintain::$problem, $val['type'] ,'')?></td>
                <td><?= \yii\helpers\ArrayHelper::getValue(\backend\models\MaintainType::getTypeList(), $val['things'] ,'')?></td>
                <td><?=$val['material_number']?></td>
                <td><?= \yii\helpers\ArrayHelper::getValue(\backend\models\Facility::$processList, $val['process'] ,'')?></td>
                <td><?= $createUser->chinese_name?> </td>
                <td><?= \yii\helpers\ArrayHelper::getValue(\backend\models\UserInfo::$shiftArr, $createUser->shift ,'')?> </td>
                  <td><?= (new \backend\models\UserInfo())->getUserName($createUser->leader_uid)?></td>
                <td><?=(new \backend\models\Facility())->getFacilityUserName($val['f_id'])?> </td>
                <td><?=(new \backend\models\UserInfo())->getUserName($val['other_feedback_user'])?> </td>
                <td><?=$facility->number?> </td>
            </tr>
            <?php endforeach;?>
    </table>
</div>
<div>请使用Google浏览器打开上述链接</div>
</body>
</html>
