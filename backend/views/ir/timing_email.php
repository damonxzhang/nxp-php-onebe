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

<div>用户:<?= (new UserInfo())->getUserName($model->create_user)?> 创建的IRID<?=$model->id?>已<b style="color: red;">逾期-<?= IrData::$statusArr[$model->status] ?>状态超期</b>请加速处理</div>

<div>IR标题:<?=$model->subject?></div>
<div>IR状态:<?= IrData::$statusArr[$model->status] ?></div>
<div>下一步操作:<?=IrData::$stepsArr[$model->steps]?></div>
<div>具体内容如下：</div>
<div><?=$model->advice_content?></div>

<div>Link to this IR:<a href="<?=\Yii::$app->request->hostInfo.'/ir/update?id='.$model->id.'&steps='.$steps?>"><?=$model->id?></a></div>

<div>请使用Google浏览器打开上述链接</div>