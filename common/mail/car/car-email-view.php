<?php
use backend\models\Car;
use backend\models\UserInfo;
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
<div>E-CAR 待审批</div>
<div>
    <table class="layui-table">
        <tr>
            <td >DPR NO ： </td><td ><?= $data['trace_no'] ?></td>
            <td >日期 ： </td><td ><?= $data['date'] ?></td>
        </tr>
        <tr>
            <td >Lot ： </td><td ><?= $data['batch_number'] ?></td>
            <td >DPR 缺陷类型: </td><td ><?= ArrayHelper::getValue($data['dafectArr'], $data['defective_items'], '') ?></td>
        </tr>
        <tr>
            <td >PkgType: </td><td ><?= $data['pkg_type'] ?></td>
            <td >次品缺陷数量: </td><td ><?= $data['defective_number'] ?></td>
        </tr>
        <tr>
            <td >PkgCode: </td><td ><?= $data['package'] ?></td>
            <td >责任工序: </td><td ><?= $data['station'] ?></td>
        </tr>
        <tr>
            <td >物料状态: </td><td ><?= ArrayHelper::getValue(Car::$statusArr, $data['status'],'') ?></td>
            <td >责任班别: </td><td ><?= ArrayHelper::getValue(UserInfo::$shiftArr, $data['shift'], '') ?></td>
        </tr>
        <tr>
            <td >异常图片: </td><td >
            <?php foreach ($data['files_mapping_arr'] as $urlimg): ?>
                <a href="<?= $urlimg; ?>">URL</a> &nbsp;&nbsp;&nbsp;&nbsp;
            <?php endforeach; ?>
            </td>
            <td >责任人员: </td><td ><?= $data['chinese_name'] ?></td>
        </tr>
        <tr>
            <td >业务Link: </td><td ><a href="<?=\Yii::$app->request->hostInfo.'/carlink/view?id='.$data['id'] ?>">Link</a></td>
            <td >物料产品工程师: </td><td ><?= (new UserInfo())->getUserName($data['type_user_id']) ?></td>
        </tr>
    </table>
</div>
</body>
</html>