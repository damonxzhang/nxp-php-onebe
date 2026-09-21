<?php

use backend\models\UserInfo;
use yii\helpers\ArrayHelper;
use backend\models\MachineRegular;
use backend\models\Facility;
use backend\models\MachineCheck;
use yii\helpers\Json;
use backend\models\MachineRoster;

?>
<div class = "x-body layui-anim layui-anim-up">
    <form class = "layui-form">
         <div class = "layui-form-item">
            <label class = "layui-form-label">区域:</label>
            <div class = "layui-input-inline" style = "width: 600px;">
                <input type="text" value="<?= ArrayHelper::getValue(UserInfo::$areaArr, $model['area'],'') ?>"  autocomplete="off" class="layui-input" readonly="readonly">
            </div>
        </div>
        <div class = "layui-form-item">
            <label class = "layui-form-label">工序:</label>
            <div class = "layui-input-inline" style = "width: 600px;">
                <input type="text" value="<?= ArrayHelper::getValue(Facility::$processList, $model['process'],'') ?>"  autocomplete="off" class="layui-input" readonly="readonly">
            </div>
        </div>
        <div class = "layui-form-item">
            <label class = "layui-form-label">星期:</label>
            <div class = "layui-input-inline" style = "width: 600px;">
            <input type="text" value="<?=(new MachineRoster())->getWeekList(Json::decode($model['week'])) ?>" readonly="readonly" autocomplete="off" lay-verify="required" class="layui-input">
            </div>
        </div>
        <div class = "layui-form-item">
            <label class = "layui-form-label">设备:</label>
            <div class = "layui-input-inline" style = "width: 600px;">
                <input type="text" value="<?=(new Facility())->getNameDataById(Json::decode($model['facility_id'])) ?>" readonly="readonly" autocomplete="off" lay-verify="required" class="layui-input">
            </div>
        </div>
        <div class = "layui-form-item">
            <label class = "layui-form-label">
                白夜班:
            </label>
            <div class="layui-input-inline" style="width: 600px;">
                <input type="text" value="<?= ArrayHelper::getValue(MachineCheck::$classList, $model['class'],'') ?>"  autocomplete="off" class="layui-input" readonly="readonly">
            </div>
        </div>
        <div class = "layui-form-item">
            <label class = "layui-form-label">状态:</label>
            <div class = "layui-input-inline" style = "width: 600px;">
                <input type="text" value="<?= ArrayHelper::getValue(MachineRegular::$statusList, $model['status'],'') ?>"  autocomplete="off" class="layui-input" readonly="readonly">
            </div>
        </div>
        <div class="layui-form-item  count">
            <label for="tel" class="layui-form-label">
                起始时间:
            </label>
            <div class="layui-input-inline" style = "width: 600px;">
                <input type="text" value="<?= $model['start_time'] ?>" readonly="readonly" autocomplete="off" lay-verify="required" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item  count">
            <label for="tel" class="layui-form-label">
            截止时间:
            </label>
            <div class="layui-input-inline" style = "width: 600px;">
                <input type="text" value="<?= $model['update_time'] ?>" readonly="readonly" autocomplete="off" lay-verify="required" class="layui-input">
            </div>
        </div>
    </form>
</div>
