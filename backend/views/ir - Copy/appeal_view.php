<?php
use backend\models\Appeal;
use backend\models\UserInfo;

?>
<form>
<table class="layui-table">
    <tr>
        <th rowspan="3" class="leftTitle">申<br/>诉</th>
        <td colspan="12">
            <label class="layui-form-label leftLable">申诉：</label>
        </td>
    </tr>
    <tr>
        <td colspan="12">
            <textarea disabled lay-verify="required" placeholder="请输入"
                      class="layui-textarea"><?=$model->content?></textarea>
        </td>
    </tr>
    <tr>
        <td colspan="12">
            <label class="layui-form-label leftLable01">申诉人：</label>
            <div class="layui-input-block rightbox01">
                <input type="text" disabled value="<?=( new userInfo)->getUserName($model->appeal_user)?>" required lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
            </div>
        </td>
    </tr>
</table>
</form>
<?php if($model->status > Appeal::STATUS_APPLY) { ?>
    <form>
    <table class="layui-table">
        <tr>
            <th rowspan="3" class="leftTitle">申<br/>诉<br/>结<br/>果</th>
            <td colspan="12">
                <label class="layui-form-label leftLable01">申诉结果：</label>
                <div class="layui-input-block rightbox01">
                    <select disabled lay-verify="required">
                        <?php foreach (Appeal::$selectList as $key => $val): ?>
                            <option value="<?= $key ?>" <?= $model->status == $key? 'selected':''?>><?= $val ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="12">
                    <textarea disabled lay-verify="required" placeholder="请输入"
                              class="layui-textarea"><?=$model->appeal_message?></textarea>
            </td>
        </tr>
        <tr>
            <td colspan="6" class="noborderRight">
                <label class="layui-form-label leftLable02">委员会签字：</label>
                <div class="layui-input-block rightbox02">
                    <input type="text" name="complaintPerson" disabled value="<?= (new UserInfo())->getUserName($model->audit_user) ?>"
                           required lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
                </div>
            </td>
            <td colspan="6" class="noborderLeft">
                <label class="layui-form-label leftLable02">完成评估日期：</label>
                <div class="layui-input-block rightbox02">
                    <input type="text" disabled value="<?= date('Y-m-d') ?>" required lay-verify="required"
                           placeholder=""
                           autocomplete="off" class="layui-input">
                </div>
            </td>
        </tr>
    </table>
    </form>
    <?php }?>