<?php
use backend\models\UserInfo;
use backend\models\IrData;
use backend\models\Appeal;
?>
<div>Hello <?= $name ?> </div>
<div><?=(new UserInfo())->getUserName($model['appeal']['appeal_user'])?>对用户(<?= (new UserInfo())->getUserName($model->create_user)?>)所创建的IRID<?=$model->id?><b style="color: red;">-提出申诉</b> </div>
<div style="color: red;">
    申诉内容：<?=$model['appeal']['content']?>
</div>


<div>IR标题:<?=$model->subject?></div>
<div>IR状态:<?= IrData::$statusArr[$model->status] ?></div>
<div>具体内容如下：</div>
<div>评估人建议内容：<?=$model->assess_advice ?></div>
<div>负责人建议内容：<?=$model->head_advice ?></div>
<div><?=$model->advice_content?></div>

<div>Link to this IR:<a href="<?=\Yii::$app->request->hostInfo.'/ir/update?id='.$model->id.'&steps='.($model->steps + 1)?>"><?=$model->id?></a></div>

<div>请使用Google浏览器打开上述链接</div>