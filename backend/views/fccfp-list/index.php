<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use backend\models\UserInfo;

?>
<style>

.layui-table td{
   /* border: 1px solid black; */
   text-align: center;
   border-style: hidden;
 }
 .layui-table th{
   /* border: 1px solid black; */
   text-align: center;
   color: #FFF;
   font-size: 14px;
   font-weight: 100;
 }
</style>

<div class="x-body">
    <table class="layui-table" style = "width: 100%;color:#000000;border: 2px solid pink;">
        <thead>
        <tr style="height: 1vh;">
            <th colspan="6" style="background-color:#7D9EC0;border-style: hidden;"></th>
            <th colspan="3" style="background-color:#4682B4;text-align: center; vertical-align: middle;color:#FFFFFF;">40hrs</th>
            <th colspan="2" style="background-color:#7D9EC0;border-style: hidden;"></th>
        </tr>
        <!-- <tr style="height:10px">
            <th colspan="6" style="background-color:#FFFFFF;border-style: hidden;"></th>
            <th colspan="2" style="background-color:#FFFFFF;border-style: hidden;"></th>
            <th style="text-align: center;color:black; vertical-align: middle;">8hrs</th>
            <th style="text-align: center;color:black; vertical-align: middle;">12hrs</th>
            <th style="text-align: center;color:black; vertical-align: middle;">60hrs</th>
        </tr> -->
        <tr>
            <th style="background-color:#006699;">#</th>
            <th style="background-color:#006699;">出炉日期</th>
            <th style="background-color:#006699;">出炉时间</th>
            <th style="background-color:#006699;">剩余时间</th>
            <th style="background-color:#006699;">批号</th>
            <th style="background-color:#006699;">工序</th>
            <th style="width: 10%;background-color:#006699;">Marking</th>
            <th style="width: 10%;background-color:#006699;">FPF_Reflow</th>
            <th style="width: 10%;background-color:#006699;">FPF_Clean(8hrs)</th>
            <th style="width: 10%;background-color:#006699;">BR(12hrs)</th>
            <th style="width: 10%;background-color:#006699;">Flux_Clean(60hrs)</th>
        </thead>
        <tbody>
        <?php if (!empty($dataProvider)): ?>
            <?php foreach ($dataProvider as $key => $value): ?>
                <tr <?php if($key%2 == 0){ ?>style="background-color: #F5F5F5;" <?php } ?>>
                    <td><?= $key+1 ?></td>
                    <td style="color:#3300FF;font-size: 16px;font-weight: 100;"><?= date('Y-m-d', strtotime($value['nldpmc_trackout'])) ?></td>
                    <td style="color:#3300FF;font-size: 16px;font-weight: 100;"><?= date('H:i', strtotime($value['nldpmc_trackout'])) ?></td>
                    <td style="color:#FF0000;font-size: 16px;font-weight: 100;"><?= $value['calibration_time'] ?></td>
                    <td style="color:#000000;"><?= $value['containername'] ?></td>
                    <td style="color:#000000;"><?= $value['nowspecname'] ?></td>
                    <td style="border-style: dotted;background-color: <?= ArrayHelper::getValue($colorArr, $value['fcmark_clean_color'],'') ?>;"></td>
                    <td style="border-style: dotted;background-color: <?= ArrayHelper::getValue($colorArr, $value['fpfreflow_color'],'') ?>;"></td>
                    <td style="border-style: dotted;background-color: <?= ArrayHelper::getValue($colorArr, $value['fpfclean_color'],'') ?>;"></td>
                    <td style="border-style: dotted;background-color: <?= ArrayHelper::getValue($colorArr, $value['br_color'],'') ?>;"></td>
                    <td style="border-style: dotted;background-color: <?= ArrayHelper::getValue($colorArr, $value['fluxclean_color'],'') ?>;"></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php $this->beginBlock('footer') ?>
<?php $this->endBlock() ?>
