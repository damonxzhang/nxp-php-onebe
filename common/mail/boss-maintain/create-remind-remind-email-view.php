<?php
use backend\models\Maintain;
use  backend\models\UserInfo;
use backend\models\MaintainType;
?>

<html>
<body>
<h1>New Boss Audit Record</h1>
<div>Hello </div>
<div>用户: <?= (new UserInfo())->getUserName($params['create_user'])?>创建了一条Boss 直检。</div>
<div>Anomaly ID: <?=$params['number']?></div>
<div>标题:
    <?=$params['things']?>
</div>
<div>具体内容如下： <?=$params['things']?> </div>
<div>Link to this Boss Audit Record:<a href="<?=\Yii::$app->request->hostInfo.'/boss-feedback/create?id='.$params['id']?>"> <?=$params['number']?></a> </div>
<div>请使用Google浏览器打开上述链接</div>
</body>
</html>








