<?php

use backend\models\UserInfo;
use yii\helpers\Url;
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
                            <input type="hidden" name="id" value="<?= $model['id'] ?>">
                            <input type="text" name="chinese_name" value="<?= $model['chinese_name'] ?>" lay-verify="required" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid layui-word-aux">
                            <span class="x-red">*</span> 请输入中文名称
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">
                            所属分部:
                        </label>
                        <div class="layui-input-inline">
                            <select name="section" lay-verify="required">
                                <?php foreach (UserInfo::$sectionArr as $key => $value): ?>
                                    <option value="<?= $key ?>" <?= $key == $model['section'] ? 'selected' : '' ?>><?= $value ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="layui-form-mid layui-word-aux">
                            <span class="x-red">*</span> 请输入所属分部
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">
                            所属分部IR:
                        </label>
                        <div class="layui-input-inline">
                            <select name="section_ir" lay-verify="required">
                                <option value="0">所属分部IR</option>
                                <?php foreach (UserInfo::$sectionIrArr as $key => $value): ?>
                                    <option value="<?= $key ?>" <?= $key == $model['section_ir'] ? 'selected' : '' ?>><?= $value ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="layui-form-mid layui-word-aux">
                            <span class="x-red">*</span> 所属分部IR
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">
                            餐卡后五位:</label>
                        <div class="layui-input-inline">
                            <input type="text" name="meal_card_code" maxlength="5" value="<?= $model['meal_card_code'] ?>" lay-verify="required" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid layui-word-aux">
                            <span class="x-red">*</span> 请输入餐卡后五位
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">
                            所属角色:
                        </label>
                        <div class="layui-input-inline">
                            <select name="role_id" id="role_id" lay-verify="required">
                                <?php foreach ($roles as $list): ?>
                                    <option value="<?= $list['id'] ?>" <?= $list['id'] == $model['role_id'] ? 'selected' : '' ?>><?= $list['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item" pane="">
                        <label class="layui-form-label">
                           性别:
                        </label>
                        <div class="layui-input-block">
                            <input type="radio" name="sex" value="1" title="男" <?= $model['sex'] == 1 ? 'checked' : '' ?>>
                            <input type="radio" name="sex" value="2" title="女" <?= $model['sex'] == 2 ? 'checked' : '' ?>>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">状态</label>
                        <div class="layui-input-block">
                            <input type="radio" value="1" name="status" lay-skin="primary" title="启用" <?= $model['status'] == 1 ? 'checked' : '' ?>>
                            <input type="radio" value="2" name="status" lay-skin="warning" title="禁用" <?= $model['status'] == 2 ? 'checked' : '' ?>>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">
                            直属领导:
                        </label>
                        <div class="layui-input-inline">
                            <select name="leader_uid" lay-search="">
                                <option value="">搜索或直接选择</option>
                                <?php foreach ($connectUser as $value): ?>
                                    <option value="<?= $value['id'] ?>" <?= $value['id'] == $model['leader_uid'] ? 'selected' : '' ?>><?= $value['chinese_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">班别:</label>
                        <div class="layui-input-inline">
                            <select name="shift"  lay-verify="required">
                                <option value="">请选择班别</option>
                                <?php foreach (UserInfo::$shiftArr as $key => $value): ?>
                                    <option value="<?= $key ?>" <?= $key == $model['shift'] ? 'selected' : '' ?>><?= $value ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="layui-form-mid layui-word-aux">
                            <span class="x-red">*</span> 请选择班别
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">区域:</label>
                        <div class="layui-input-inline">
                            <select name="area">
                                <?php foreach (UserInfo::$areaArr as $key => $value): ?>
                                    <option value="<?= $key ?>" <?= $key == $model['area'] ? 'selected' : '' ?>><?= $value ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="layui-form-mid layui-word-aux">
                            <span class="x-red">*</span> 请选择区域
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">gemba权限:</label>
                        <div class="layui-input-inline">
                            <select name="gemba_role">
                                <option value="">请选择gemba权限</option>
                                <?php foreach ( \backend\models\Gemba::$gembaRole as $key =>  $value): ?>
                                    <option value="<?= $key ?>" <?= $key== $model->gemba_role ? "selected" :''?>><?= $value ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>


                    <div class="layui-form-item">
                        <label class="layui-form-label">站别:</label>
                        <div class="layui-input-inline">
                            <select name="station_id">
                                <option value="">请选择站别</option>
                                <?php foreach ($station as $key => $value): ?>
                                    <option value="<?= $value['id'] ?>" <?= $value['id'] == $model['station_id'] ? 'selected' : '' ?>><?= $value['station'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">多技能工位:</label>
                        <div class="layui-input-inline">
                            <select name="skill">
                                <option value="">请选择多技能工位</option>
                                <?php foreach ($station as $value): ?>
                                    <option value="<?= $value['id'] ?>" <?= $key == $model['skill'] ? 'selected' : '' ?>><?= $value['station'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="layui-col-sm6">
                    <div class="layui-form-item">
                        <label class="layui-form-label">英文名:</label>
                        <div class="layui-input-inline">
                            <input type="text" name="english_name" autocomplete="off"  value="<?= $model['english_name'] ?>" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">手机号码:</label>
                        <div class="layui-input-inline">
                            <input type="text" id="tel" name="tel" maxlength="11" value="<?= $model['tel'] ?>" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">邮箱:</label>
                        <div class="layui-input-inline">
                            <input type="text" id="email" name="email" value="<?= $model['email'] ?>" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">PID:</label>
                        <div class="layui-input-inline">
                            <input type="text" name="pid" autocomplete="off" maxlength="8" value="<?= $model['pid'] ?>" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">SFC账号:</label>
                        <div class="layui-input-inline">
                            <input type="text" name="sfc_number" maxlength="10" value="<?= $model['sfc_number'] ?>" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">FSL工号:</label>
                        <div class="layui-input-inline">
                            <input type="text" name="fsl_number" maxlength="6" value="<?= $model['fsl_number'] ?>" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">WBI CODE:</label>
                        <div class="layui-input-inline">
                            <input type="text" name="wbi_code" maxlength="20" value="<?= $model['wbi_code'] ?>" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">识别证码:</label>
                        <div class="layui-input-inline">
                            <input type="text" name="badge_code" maxlength="14" lay-verify="required" value="<?= $model['badge_code'] ?>" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-form-mid layui-word-aux">
                            <span class="x-red">*</span> 请输入识别证码
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">部门号:</label>
                        <div class="layui-input-inline">
                            <input type="text" name="department_number" value="<?= $model['department_number'] ?>" maxlength="5" autocomplete="off" class="layui-input">
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">gemba小组:</label>
                        <div class="layui-input-inline">
                            <select name="team">
                                <option value="">请选择gemba小组</option>
                                <?php foreach ($team as $key =>  $value): ?>
                                    <option value="<?= $key ?>" <?= $model['team'] == $key ? 'selected':''?> ><?= $value ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">职务:</label>
                        <div class="layui-input-inline">
                            <select name="position">
                                <option value="">请选择职务</option>
                                <?php foreach (UserInfo::$positionArr as $key => $value): ?>
                                    <option value="<?= $key ?>" <?= $key == $model['position'] ? 'selected' : '' ?>><?= $value ?></option>
                                <?php endforeach; ?>
                            </select>
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
            <div class="layui-form-item">
                <label for="" class="layui-form-label"></label>
                <button class="layui-btn" lay-filter="update" lay-submit="">
                    修改
                </button>
            </div>
        </form>
    </div>

<?php $this->beginBlock('footer') ?>
<script>
    layui.use(['form','layer'], function(){
        $ = layui.jquery;
        var form = layui.form ,layer = layui.layer;
        //监听提交
        form.on('submit(update)', function(data){
            layer.load(3);
            $.post('<?= Url::to(['user-info/update']) ?>', data.field, function (res) {
                layer.closeAll();
                if (res.status === 200) {
                    layer.msg(res.msg, {icon: 1,time: 1500}, function () {
                        var index = parent.layer.getFrameIndex(window.name);
                        parent.layer.close(index);
                        parent.location.reload();
                    })
                } else {
                    layer.msg(res.msg, {icon: 2, time: 1500});
                }
            }, 'json');
            return false;
        });
    });
</script>
<?php $this->endBlock() ?>
