<?php
use backend\models\Car;
use backend\models\UserInfo;
use backend\models\UserStation;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use backend\models\CarJudge;
?>
<html>
<style>
    body{
    }
    table{
        width: 50%;
        text-align: center;
    }
    th{
        background: #D2D2D2;
    }
    td,th{
        border: 1px solid #F0E5F1;
        height: 30px;
    }
</style>
<body>
<h1>Hi All :</h1>
<div></div>
<div></div>
<div>待产品工程师审批</div>
<div>
    <table class="layui-table">
        <tr>
            <td >下一步 ： </td><td ><?= ArrayHelper::getValue(Car::$statusArr, $data['status'],'') ?></td>
        </tr>
        <tr>
            <td >物料处理 ： </td><td ><?php echo isset($carJudge['method']) ? $carJudge['method'] : ''; ?></td>
        </tr>

    </table>
</div>

<div style="margin-top:30px;">
    <table class="layui-table">
        <tr>
            <td >日期 ： </td><td ><?= $data['date'] ?></td>
            <td >工序 ： </td><td ><?php echo (new UserStation())->getStationName($data['station_id']); ?></td>
        </tr>
        <tr>
            <td >机台: </td><td ><?= $data['machine_number'] ?></td>
            <td >PkgType: </td><td ><?= $data['pkg_type'] ?></td>
        </tr>
        <tr>
            <td >批号: </td><td ><?= $data['batch_number'] ?></td>
            <td >PkgCode: </td><td ><?= $data['package'] ?></td>
        </tr>
        <tr>
            <td >缺陷项目: </td><td ><?= ArrayHelper::getValue($dafectArr, $data['defective_items'], '') ?></td>
            <td >次品缺陷数量: </td><td ><?= $data['defective_number'] ?></td>
        </tr>
        <tr>
            <td >缺陷项目描述 ： </td><td  colspan="3" ><?= $data['description'] ?></td>
        </tr>
        <tr>
            <td>异常图片: </td><td colspan="3" >
            <?php foreach ($data['files_mapping_arr'] as $urlimg): ?>
                <a href="<?= $urlimg; ?>">URL</a> &nbsp;&nbsp;&nbsp;&nbsp;
            <?php endforeach; ?>
            </td>
        </tr>
        <tr>
            <td >业务Link: </td><td  colspan="3" ><a href="<?=\Yii::$app->request->hostInfo.'/carlink/view?id='.$data['id'] ?>">Link</a></td>
        </tr>
    </table>
</div>
</div>
</body>
</html>