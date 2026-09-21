<?php

use backend\models\MachineModel;
use backend\models\MachineVendor;
use yii\helpers\ArrayHelper;
use backend\models\UserInfo;
use backend\models\Facility;
?>
<div class="x-body layui-anim layui-anim-up">
    <form class="layui-form">
        <div class="layui-row">
            <div class="layui-col-sm6">
                <div class="layui-form-item">
                    <label class="layui-form-label">
                        设备名称:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text"  value="<?= $model['name'] ?>" readonly="readonly" lay-verify="required" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">
                        设备编号:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text"  value="<?= $model['number'] ?>" readonly="readonly" lay-verify="required" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">
                        机型:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text" value="<?= MachineModel::getNameById($model['model']) ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">
                        区域:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text" value="<?= ArrayHelper::getValue(UserInfo::$areaArr, $model['area'], '') ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">
                        serial_no:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text" value="<?= $model['serial_no'] ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">
                        VENDOR:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text" value="<?= MachineVendor::getNameById($model['vendor_id']) ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">
                        ASSET_NO:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text" value="<?= MachineVendor::getNameById($model['vendor_id']) ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">
                        BUY_OFF_DATE:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text" value="<?= date('Y-m-d H:i:s',$model['BUY_OFF_DATE']) ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">
                        CBA_pass_date:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text" value="<?= date('Y-m-d H:i:s',$model['CBA_pass_date']) ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">
                        IP:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text"  value="<?= $model['ip'] ?>" readonly="readonly" lay-verify="required" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">
                        备注:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text"  value="<?= $model['remark'] ?>" readonly="readonly" lay-verify="required" autocomplete="off" class="layui-input">
                    </div>
                </div>
            </div>
            <div class="layui-col-sm6">
                <div class="layui-form-item">
                    <label class="layui-form-label">
                        负责人:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text" value="<?= (new UserInfo())->getUserName($model['head_user']) ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">
                        状态:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text" value="<?= ArrayHelper::getValue(Facility::$statusList, $model['status'], '') ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">
                        是否可用:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text" value="<?= ArrayHelper::getValue(Facility::$selectList, $model['is_ok'], '') ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">
                        更换角色:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text" value="<?= ArrayHelper::getValue(Facility::$replaceList, $model['is_replace'], '') ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php $this->beginBlock('footer') ?>
<script>
</script>
<?php $this->endBlock() ?>

