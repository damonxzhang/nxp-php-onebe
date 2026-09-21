<?php

use backend\models\ApproveConfirm;
use backend\models\UserInfo;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

?>
<div class="x-body layui-anim layui-anim-up">
    <div class="layui-row">
        <div class="layui-input-inline" style="margin-left: 80px; font-size: x-large;">
           <p>已确认:<?=$confirmYes ?>人</p>
        </div>
        <div class="layui-input-inline" style="margin-left: 20px; font-size: x-large;">
            <p>未确认:<?=$confirmNo ?>人</p>
        </div>
        <div class="layui-input-inline" style="margin-left: 20px; font-size: x-large;">
            <p>确认率:<?= ArrayHelper::getValue($approveModel, 'progress','0') . '%'  ?></p>
        </div>
    </div>
    <div class="layui-row layui-col-space10">
        <div class="layui-col-md4">
            <table class="layui-table">
                <thead>
                <tr>
                    <th colspan="4" class="layui-bg-green" style="text-align: center">已确认</th>
                </tr>
                <tr>
                    <th>ID</th>
                    <th>姓名</th>
                    <th>班别</th>
                    <th>主管</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($userListYes as $key => $list): ?>
                    <tr>
                        <td><?= $key+1 ?></td>
                        <td><?= $list['chinese_name'] ?></td>
                        <td><?= ArrayHelper::getValue(UserInfo::$shiftArr, $list['shift'],'') ?></td>
                        <td><?= (new UserInfo())->getUserName($list['leader_uid']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="layui-col-md4">
            <table class="layui-table">
                <thead>
                <tr>
                    <th colspan="5" class="layui-bg-orange" style="text-align: center">未确认</th>
                </tr>
                <tr>
                    <th>ID</th>
                    <th>姓名</th>
                    <th>班别</th>
                    <th>主管</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($userListNo as $key => $list): ?>
                    <tr>
                        <td><?= $key+1 ?></td>
                        <td><?= $list['chinese_name'] ?></td>
                        <td><?= ArrayHelper::getValue(UserInfo::$shiftArr, $list['shift'],'') ?></td>
                        <td><?= (new UserInfo())->getUserName($list['leader_uid']) ?></td>
                        <td>
                            <select  data-id="<?=$list['id']?>" onchange="selectProvince(this)" style="height: 23px">
                                <?php foreach (ApproveConfirm::$reasonArr as $reasonkey => $value): ?>
                                    <option value="<?= $reasonkey ?>"><?= $value ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="layui-col-md4">
            <table class="layui-table">
                <thead>
                <tr>
                    <th colspan="4" class="layui-bg-red" style="text-align: center">异常</th>
                </tr>
                <tr>
                    <th>ID</th>
                    <th>姓名</th>
                    <th>班别</th>
                    <th>主管</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($userListUnusual as $key => $list): ?>
                    <tr>
                        <td><?= $key+1 ?></td>
                        <td><?= $list['chinese_name'] ?></td>
                        <td><?= ArrayHelper::getValue(UserInfo::$shiftArr, $list['shift'],'') ?></td>
                        <td><?= (new UserInfo())->getUserName($list['leader_uid']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
$this->beginBlock('footer');
?>
<script>
    function selectProvince(obj){
        var reason = $(obj).val();
        var id = $(obj).data('id');
        $.post('<?= Url::to(['add-unusual']) ?>', {id: id, reason: reason}, function (res) {
            if (res.status === 200) {
                layer.msg(res.msg, {icon: 1,time: 1500}, function () {
                    var index = parent.layer.getFrameIndex(window.name);
                   // parent.layer.close(index);
                    window.location.reload();
                })
            } else {
                layer.msg(res.msg, {icon: 2, time: 3000})
            }
        }, 'json');
        return false;
    }
</script>
<?php $this->endBlock() ?>


