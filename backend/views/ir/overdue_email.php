<?php
use backend\models\UserInfo;
use backend\models\IrData;
    if($model->steps < \backend\models\Ir::REWARD_STEPS){
        $steps = $model->steps + 1;
    }else{
        $steps = $model->steps;
    }
?>
<div>Hello <?= $name ?> </div>
<div>您对用户:<?= (new UserInfo())->getUserName($model->create_user)?> 创建的IRID<?=$model->id?></div>
<div><b style="color: red;">所进行的对应改善措施已超过预定的完成时,请加速处理并及时关闭此条IR</b></div>
<div>实施人建议内容:<?=$model->work_content?></div>
<div>预计完成日期:<?=date('Y-m-d',$model->end_time)?></div>

<div>备注:</div>
<div>IR标题:<?=$model->subject?></div>
<div>IR状态:<?= IrData::$statusArr[$model->status] ?></div>

<div>具体内容如下：</div>
<div><?=$model->advice_content?></div>

<div>Link to this IR:<a href="<?=\Yii::$app->request->hostInfo.'/ir/update?id='.$model->id.'&steps='.$steps?>"><?=$model->id?></a></div>

<div>请使用Google浏览器打开上述链接</div>