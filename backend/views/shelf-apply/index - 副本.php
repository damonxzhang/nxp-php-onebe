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
          <cite>领取Compound</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so">
            <input class="layui-input" id="autofocus" value="<?= ArrayHelper::getValue($request, 'badge_code') ?>" autocomplete="off" placeholder="识别证码" name="badge_code">
            <button class="layui-btn" lay-submit="" lay-filter="sreach"><i class="layui-icon">&#xe615;</i>查询</button>
            <button class="layui-btn" type="button" onclick="location.href= '<?= Url::to([$this->context->id . '/index'])?>'" >
                <i class="layui-icon layui-icon-refresh" onclick="javascript:location.replace(location.href);"  style="line-height:30px"></i>刷新
            </button>
        </form>
    </div>
    <xblock style="height: 29px;">
        <span class="x-right">共有数据：<span class="count_num"><?= $pagination->totalCount ?></span> 条 ( <?= $pagination->getPageCount() ?> 页 )</span>
    </xblock>
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
        <?php if (!empty($dataProvider)): ?>
            <?php foreach ($dataProvider as $value): ?>
                <tr>
                    <td><?= $value['id'] ?></td>
                    <td><?= ArrayHelper::getValue($value->userinfo, 'chinese_name','') ?></td>
                    <td><?= $value['part_no'] ?></td>
                    <td><?= date('Y-m-d H:i:s',strtotime($value['application_time'])) ?></td>
                    <td><?= $value['amount'] ?></td>
                    <td><?= $value['machine_id'] ?></td>
                    <td class="td-manage">
                        <?php if ((new ShelfMaterials())->getCountMaterials(['part_no' => $value['part_no']]) >=$value['amount']): ?>
                        	<button type="button" class="layui-btn" onclick="compareData(this)" data-id="<?=$value['id']?>" data-part="<?=$value['part_no']?>">领取</button>
                        <?php else: ?>
                             <button type="button" class="layui-btn layui-bg-red">库存不足</button>
                         <?php endif; ?>
                        <button type="button" class="layui-btn layui-bg-black" onclick="withdrawApply(this)" data-id="<?=$value['id']?>">撤回</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
    <?= $this->render('@app/views/layouts/pagination', compact('pagination')) ?>
</div>
<?php $this->beginBlock('footer') ?>
<script>
    function compareData(obj) {
        var id = $(obj).data('id');
        var part_no = $(obj).data('part');
        $.post('<?= Url::to([$this->context->id . '/check-materials']) ?>', {id: id, part_no: part_no}, function (res) {
            if (res.status == 200){
                x_admin_show('物料批次进行二次确认','promote?id=' + id + '&part_no=' + part_no);
            }else{
                layer.msg(res.msg, {icon: 2, time: 1500});
            }
        }, 'json');
        return false;
    }

     //撤回物料
    function withdrawApply(obj) {
        var id = $(obj).data('id');
        $.post('<?= Url::to([$this->context->id . '/withdraw-materials']) ?>', {id: id}, function (res) {
            if (res.status === 200) {
                layer.msg(res.msg, {icon: 1, time: 1500}, function () {
                    $('.layui-icon-refresh').click();//刷新父页面
                });
            } else {
                layer.msg(res.msg, {icon: 2, time: 1500})
            }
        }, 'json');
        return false;
    }
</script>
<?php $this->endBlock() ?>

