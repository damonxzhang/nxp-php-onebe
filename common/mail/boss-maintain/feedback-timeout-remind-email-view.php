<?php
use backend\models\Maintain;
use  backend\models\UserInfo;
use backend\models\MaintainType;
?>

<html>
<body>
<h1>Anomaly Overdue Reminder</h1>
<div>Hello <?=$params['userName']?></div>
<h2 style="color: red;">Anomaly ID为<?=$params['number']?>的预计完成时间已过期，请及时完成并更新状态</h2>
<div>产线异常标题:
    <?=$params['things']?>
</div>
<div>产线异常具体内容如下： <?=$params['things']?> </div>
<div>Link to this Anomaly Record:<a href="<?=\Yii::$app->request->hostInfo.'/boss-feedback/create?id='.$params['id']?>"> <?=$params['number']?></a> </div>
<div>请使用Google浏览器打开上述链接</div>
</body>
</html>








