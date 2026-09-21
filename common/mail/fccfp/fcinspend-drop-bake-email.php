<?php
use backend\models\Maintain;
use  backend\models\UserInfo;
use backend\models\MaintainType;
?>

<html>
<body>
<h1>Hi FE team:</h1>
<div></div>
<div>Total  <?=$data['number']?> 批物料需要从凌晨4点开始Baking，请及时完成。</div>
<div>具体批号如下: </div>
<div><?=$data['str']?></div>
<div>预计Bake 时间:  <?=$data['bake_time']?>  </div>
<div>如有疑问，请联系BE带班处理</div>
<div>Thanks</div>
</body>
</html>