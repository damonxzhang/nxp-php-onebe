<?php
use backend\models\Blog;
?>
<div>Hello <?=$userName?></div>
<div>用户: <?=$createUser?>创建/更新了日志。</div>
<div>日志ID:<?=$params['id']?></div>
<div>日志标题: <?=$params['title']?></div>
<div>日志类型: <?=Blog::$typeList[$params['type']]?></div>
<div>日志具体内容如下：<?=$params['content_data']?></div>
<div>日志状态：

<?php
    if($params['status'] == Blog::STATUS_DEFERRED){
        echo "<strong style='color: red; '>". Blog::$statusArr[$params['status']] ."</strong>";
    }else{
        echo  Blog::$statusArr[$params['status']];
    }
?>
</div>
<div>Link to this Handover Record:<a href="<?=\Yii::$app->request->hostInfo.'/blog/update?id='.$params['id'] ?>"><?=$params['id']?></a></div>
<div>请使用Google浏览器打开上述链接</div>