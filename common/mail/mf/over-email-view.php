
<div>Hello <?=$userName?></div>
<div>用户:提示您有一条编号<?= $params['number'] ?> 的 MTFinding,超时待处理。</div>
<div>Link to this Handover Record:<a href="<?=\Yii::$app->request->hostInfo.'/boss-maintain/index?id='.$params['id'] ?>"><?=$params['id']?></a></div>
<div>请使用Google浏览器打开上述链接</div>