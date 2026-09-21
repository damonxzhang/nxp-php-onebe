<?php

use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use backend\models\ShelfMaterials;
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">Compound管理</a>
        <a href="javascript:;">
          <cite>未确认二次申请</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>领取人</th>
            <th>物料批次</th>
            <th>申请时间</th>
            <th>申请数量</th>
            <th>设备ID</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php if (!empty($data)): ?>
            <?php foreach ($data as $value): ?>
                <?php $userinfo = $value->userinfo ?>
                <tr>
                    <td><?= $value['id'] ?></td>
                    <td><?= $userinfo['chinese_name']?></td>
                    <td><?= $value['part_no'] ?></td>
                    <td><?= date('Y-m-d H:i:s',strtotime($value['application_time'])) ?></td>
                    <td><?= $value['amount'] ?></td>
                    <td><?= $value['machine_id'] ?></td>
                    <td class="td-manage" data-id="<?= $value['id'] ?>">
                        <button type="button" class="layui-btn layui-bg-red receive">完成</button>
                        <button type="button" class="layui-btn layui-bg-black call-back">撤回</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php $this->beginBlock('footer') ?>
<script>
    $('.receive').click(function () {
        var id = $(this).parent().data('id');
        $.post('<?= Url::to('/shelf-apply/receive-apply') ?>',{id:id},function (res) {
            if(res.status == 200){
                window.location.reload();
            }else{
                layer.msg(res.msg)
            }
        },'json');


    });
    $('.call-back').click(function () {
        var id = $(this).parent().data('id');
        $.post('<?= Url::to('/shelf-apply/call-back-confirm') ?>',{id:id},function (res) {
            if(res.status == 200){
                window.location.reload();
            }else{
                layer.msg(res.msg)
            }
        },'json');


    });
</script>
<?php $this->endBlock() ?>

