<?php
use backend\models\UserInfo;
use backend\models\IrData;
$headUser =(new UserInfo())->getUserName($model->assess_user);

?>

<?php foreach ($name as $k => $val):?>
<div>Hello <?= !empty($val) ? $val :'' ?> </div>

<div>用户:
    <?= (new UserInfo())->getUserName($model->create_user)?>创建的IRID<?=$model->id?>已被评估人- <?=$headUser?><b style="color: red;">拒绝接受</b>,
    <?=   $k == 1 ? "但被评估人- ".$headUser."指派给您进行后续改善，请协助完成。":'' ?>
</div>
<div>评估人拒绝原因：<?=$model->assess_advice?></div>

<?php endforeach;?>
<div>备注：</div>
<div>IR标题:<?=$model->subject?></div>
<div>IR状态:<b style="color: red;"><?= IrData::$statusArr[$model->status] ?></b></div>
<div>具体内容如下：</div>
<div><?=$model->advice_content?></div>

<div>Link to this IR:<a href="<?=\Yii::$app->request->hostInfo.'/ir/update?id='.$model->id.'&steps='.($model->steps + 1)?>"><?=$model->id?></a></div>

<div>请使用Google浏览器打开上述链接</div>