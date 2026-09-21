<div>Hello <?= $name ?> </div>

<div>用户:提示您有一条编号<a href="<?=\Yii::$app->request->hostInfo.'/new-gemba/update?id='.$data['id'].'&steps='.$data['steps']?>"><?= $data['number']?></a>的GEMBA待处理</div>

<div>请使用Google浏览器打开上述链接</div>