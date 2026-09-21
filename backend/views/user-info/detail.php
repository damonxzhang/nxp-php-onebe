<?php

use backend\models\UserInfo;
use backend\models\Role;
use yii\helpers\ArrayHelper;
use backend\models\UserStation;
?>
<div class="x-body layui-anim layui-anim-up">
    <form class="layui-form">
        <div class="layui-row">
            <div class="layui-col-sm6">
                <div class="layui-form-item">
                    <label class="layui-form-label">
                        中文名:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text"  value="<?= $model['chinese_name'] ?>" readonly="readonly" lay-verify="required" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">
                        所属分部:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text" value="<?= ArrayHelper::getValue(UserInfo::$sectionArr, $model['section'], '') ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">
                        所属分部IR:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text" value="<?= ArrayHelper::getValue(UserInfo::$sectionIrArr, $model['section_ir'], '') ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">
                        餐卡后五位:</label>
                    <div class="layui-input-inline">
                        <input type="text" value="<?= $model['meal_card_code'] ?>" readonly="readonly" lay-verify="required" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">
                        所属角色:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text" value="<?= (new Role())->getRoleName($model['role_id']) ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item" pane="">
                    <label class="layui-form-label">
                        性别:
                    </label>
                    <div class="layui-input-block">
                        <input type="radio" title="男" <?= $model['sex'] == 1 ? 'checked' : '' ?>>
                        <input type="radio" title="女" <?= $model['sex'] == 2 ? 'checked' : '' ?>>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">状态</label>
                    <div class="layui-input-block">
                        <input type="radio" lay-skin="primary" title="启用" <?= $model['status'] == 1 ? 'checked' : '' ?>>
                        <input type="radio" lay-skin="warning" title="禁用" <?= $model['status'] == 2 ? 'checked' : '' ?>>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">
                        直属领导:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text" value="<?= (new UserInfo())->getUserName($model['leader_uid']) ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">班别:</label>
                    <div class="layui-input-inline">
                        <input type="text" value="<?= ArrayHelper::getValue(UserInfo::$shiftArr, $model['shift'], '') ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">区域:</label>
                    <div class="layui-input-inline">
                        <input type="text" value="<?= ArrayHelper::getValue(UserInfo::$areaArr, $model['area'], '') ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">gemba权限:</label>
                    <div class="layui-input-inline">
                        <input type="text" value="<?= !empty($model['gemba_role'])? \backend\models\Gemba::$gembaRole[$model['gemba_role']]:'' ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">站别:</label>
                    <div class="layui-input-inline">
                        <input type="text" value="<?= (new UserStation())->getStationName($model['station_id'])  ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">多技能工位:</label>
                    <div class="layui-input-inline">
                        <input type="text" value="<?= (new UserStation())->getStationName($model['skill']) ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
            </div>
            <div class="layui-col-sm6">
                <div class="layui-form-item">
                    <label class="layui-form-label">英文名:</label>
                    <div class="layui-input-inline">
                        <input type="text"  autocomplete="off"  value="<?= $model['english_name'] ?>" readonly="readonly" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">手机号码:</label>
                    <div class="layui-input-inline">
                        <input type="text" value="<?= $model['tel'] ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">邮箱:</label>
                    <div class="layui-input-inline">
                        <input type="text"  value="<?= $model['email'] ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">PID:</label>
                    <div class="layui-input-inline">
                        <input type="text" autocomplete="off" value="<?= $model['pid'] ?>" readonly="readonly" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">SFC账号:</label>
                    <div class="layui-input-inline">
                        <input type="text" value="<?= $model['sfc_number'] ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">FSL工号:</label>
                    <div class="layui-input-inline">
                        <input type="text" value="<?= $model['fsl_number'] ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">WBI CODE:</label>
                    <div class="layui-input-inline">
                        <input type="text"  value="<?= $model['wbi_code'] ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">识别证码:</label>
                    <div class="layui-input-inline">
                        <input type="text"  readonly="readonly" value="<?= $model['badge_code'] ?>" autocomplete="off" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">部门号:</label>
                    <div class="layui-input-inline">
                        <input type="text" value="<?= $model['department_number'] ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">gemba小组:</label>
                    <div class="layui-input-inline">
                        <input type="text"  readonly="readonly" value="<?= !empty($team['teamName'])?$team['teamName'] :'' ?>" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">职务:</label>
                    <div class="layui-input-inline">
                        <input type="text" value="<?= ArrayHelper::getValue(UserInfo::$positionArr, $model['position'], '') ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
                
                <div class="layui-form-item">
                        <label class="layui-form-label">是否校验洗鞋机:</label>
                        <div class="layui-input-block">
                            <input type="radio" value="1" name="shoe_type" lay-skin="primary" title="需要" <?= $model['shoe_type'] == 1 ? 'checked' : '' ?>>
                            <input type="radio" value="2" name="shoe_type" lay-skin="warning" title="不需要" <?= $model['shoe_type'] == 2 ? 'checked' : '' ?>>
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

