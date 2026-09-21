<?php
use backend\models\Maintain;
use  backend\models\UserInfo;
use backend\models\MaintainType;
if($params['things'] == MaintainType::OTHER_TYPE){
    $things = $params['other_things'];
}else{
    $things = MaintainType::getTypeList()[$params['things']];
}
?>

<html>
<body>
    <h1>New Anomaly Record</h1>
    <div>Hello <?=$params['name']?></div>
    <div>用户: <?= (new UserInfo())->getUserName($params['create_user'])?>创建了一条产线异常。</div>
    <div>Anomaly ID: <?=$params['id']?></div>
    <div>产线异常标题:
        <?=$things?>
    </div>
    <div>
        问题类型:
        <?php
            if($params['type'] == Maintain::OTHER_PROBLEM_TYPE){
                echo $params['other_type'];
            }else{
                echo Maintain::$problem[$params['type']];
            }
        ?>
    </div>
    <div>产线异常具体内容如下： <?=$things?> </div>
    <div>产线异常状态： <?= Maintain::$statusList[$params['status']]?></div>
    <div>次品数量： <?=$params['rejects']?></div>
    <div>Link to this Anomaly Record:<a href="<?=\Yii::$app->request->hostInfo.'/maintain/access?id='.$params['id']?>"> <?=$params['id']?></a> </div>
    <div>请使用Google浏览器打开上述链接</div>
</body>
</html>








