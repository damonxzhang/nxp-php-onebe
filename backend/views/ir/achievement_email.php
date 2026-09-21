<?php
use backend\models\UserInfo;
use backend\models\IrData;

?>

<div>Hello <?=$name?> </div>

<div>用户:<?= (new UserInfo())->getUserName($model->create_user)?>创建的IRID<?=$model->id?>已被评估人完成设定加分。</div>
<div style="color: #00FF00;">建议奖项：<?=IrData::$prizes[$model->prize]?></div>
<div style="color: #00FF00;">建议类型：<?=IrData::$types[$model->type]?></div>

<div>备注：</div>
<div>IR标题:<?=$model->subject?></div>
<div>IR状态:<b style="color: #00FF00;"><?= IrData::$statusArr[$model->status] ?></b></div>
<div>具体内容如下：</div>
<div><?=$model->advice_content?></div>

<div>Link to this IR:<a href="<?=\Yii::$app->request->hostInfo.'/ir/update?id='.$model->id.'&steps='. $model->steps?>"><?=$model->id?></a></div>

<div>请使用Google浏览器打开上述链接</div>