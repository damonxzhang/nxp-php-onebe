<?php
use backend\models\Blog;
?>
<div>Hello <?=$userName?></div>
<div>用户: <?=$userName?>创建的日志已过期，请及时更新/完成。</div>
<div>日志ID:<?=$params['id']?></div>
<div>日志标题: <?=$params['title']?></div>
<div>日志类型: <?= !empty(Blog::$typeList[$params['type']])?Blog::$typeList[$params['type']] :'';?></div>
<div>日志具体内容如下：<?=$params['content_data']?></div>
<div>日志状态：<?= "<strong style='color: red; '>". Blog::$statusArr[$params['status']] ."</strong>"  ?></div>
<div>Link to this Handover Record:<a href="<?=\Yii::$app->request->hostInfo.'/blog/update?id='.$params['id'] ?>"><?=$params['id']?></a></div>
<div>请使用Google浏览器打开上述链接</div>