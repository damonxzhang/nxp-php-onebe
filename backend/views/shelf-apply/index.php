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
                <?php $userinfo = $value->userinfo ?>
                <tr>
                    <td><?= $value['id'] ?></td>
                    <td><?= $userinfo['chinese_name']?></td>
                    <td><?= $value['part_no'] ?></td>
                    <td><?= date('Y-m-d H:i:s',strtotime($value['application_time'])) ?></td>
                    <td><?= $value['amount'] ?></td>
                    <td><?= $value['machine_id'] ?></td>
                    <td class="td-manage">
                        <?php if ((new ShelfMaterials())->getCountMaterials(['part_no' => $value['part_no']]) >=$value['amount']): ?>
                        	<button type="button" class="layui-btn" onclick="compareData(this)" data-id="<?=$value['id']?>" data-name="<?= $userinfo['chinese_name']?>"  data-uid="<?= $userinfo['id']?>" data-part="<?=$value['part_no']?>">领取</button>
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

    layui.use('form', function() {
        var form = layui.form;
        form.on('submit(sreach)', function (data) {
            $.post('<?= Url::to([$this->context->id . '/two-confirm']) ?>','',function (res) {
                if(res.status == 200){

                    $('form').submit();
                }else{

                    window.open( '/shelf-apply/promote?id=' + res.data.id + '&part_no=' + res.data.part_no + '&name='+res.data.chinese_name);
                }

            },'json');
            return false;
        });
    });

    function compareData(obj) {
        layer.load(3);
        var id = $(obj).data('id');
        var uid = $(obj).data('uid');
        var part_no = $(obj).data('part');
        var chinese_name = $(obj).data('name');
        $.post('<?= Url::to([$this->context->id . '/check-materials']) ?>', {id: id,uid:uid, part_no: part_no}, function (res) {
            if (res.status == 200){
                window.location.href= '<?=Url::to([$this->context->id . '/index'])?>'

                window.open( '/shelf-apply/promote?id=' + id + '&part_no=' + part_no + '&name='+chinese_name);

                // admin_show('物料批次进行二次确认','promote?id=' + id + '&part_no=' + part_no);
            }else{
                layer.msg(res.msg, {icon: 2, time: 1500});
            }
        }, 'json');
        return false;
    }
    /*
        参数解释：
        title   标题
        url     请求的url
        id      需要操作的数据id
        w       弹出层宽度（缺省调默认值）
        h       弹出层高度（缺省调默认值）
    */
    function admin_show(title, url, w, h) {
        if (title == null || title == '') {
            title = false;
        }
        if (url == null || url == '') {
            url = "404.html";
        }
        if (w == null || w == '') {
            w = ($(window).width() * 0.9);
        }
        if (h == null || h == '') {
            h = ($(window).height() - 50);
        }
        layer.open({
            type: 2,
            area: [w + 'px', h + 'px'],
            fix: false, //不固定
            shadeClose: true,
            shade: 0.4,
            closeBtn :0,
            title: title,
            content: url
        });
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

