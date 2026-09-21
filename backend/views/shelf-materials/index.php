<?php

use backend\models\UserInfo;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">Compound管理</a>
        <a href="javascript:;">
          <cite>Compound上架</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <div class="layui-row">
        <form class="layui-form">
            <div class="layui-form-item">
                <label for="name" class="layui-form-label">
                    <span class="x-red">*</span>货架编号:
                </label>
                <div class="layui-input-inline">
                    <input type="text" name="shelf_number" id="autofocus" value="<?= ArrayHelper::getValue($request, 'shelf_number') ?>" onkeypress="if(event.keyCode==13) focusNextInput(this);" lay-verify="required" autocomplete="off" class="layui-input" >
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">
                    <span class="x-red">*</span>物料编号:
                </label>
                <div class="layui-input-inline">
                    <input type="text" name="cont_no" id="cont_no" value="<?= ArrayHelper::getValue($request, 'cont_no') ?>" onkeypress="if(event.keyCode==13) subform();" lay-verify="required" autocomplete="off" class="layui-input" >
                </div>
            </div>
            <div class="layui-form-item">
                <label for="" class="layui-form-label"></label>
                <button class="layui-btn" lay-filter="add" lay-submit="" id="submitform" onclick="subform()" type="button">
                    增加
                </button>
            </div>
        </form>
    </div>
    <xblock>
        <button class="layui-btn" onclick="save_all()">一键保存</button>
        <span class="x-right" style="line-height:30px">共有数据：<span class="count_num"><?= $pagination->totalCount ?></span> 条 ( <?= $pagination->getPageCount() ?> 页 )</span>
    </xblock>
    <table class="layui-table">
        <thead>
        <tr>
            <th>
                <div class="layui-unselect header layui-form-checkbox" lay-skin="primary"><i class="layui-icon">&#xe605;</i></div>
            </th>
            <th>ID</th>
            <th>录入人</th>
            <th>物料编号</th>
            <th>货架编号</th>
            <th>出库时间</th>
            <th>醒料起始时间</th>
            <th>醒料截止时间</th>
            <th>过期时间</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php if (!empty($dataProvider)): ?>
            <?php foreach ($dataProvider as $value): ?>
                <tr>
                    <td>
                        <div class="layui-unselect layui-form-checkbox" lay-skin="primary" data-id=<?= $value['id'] ?>><i class="layui-icon">&#xe605;</i></div>
                    </td>
                    <td><?= $value['id'] ?></td>
                    <td><?= (new UserInfo())->getUserName($value['entry_id']) ?></td>
                    <td><?= $value['cont_no'] ?></td>
                    <td><?= ArrayHelper::getValue($value->shelf, 'shelf_number','') ?></td>
                    <td><?= empty($value['library_time']) ? '' : date('Y-m-d H:i:s',strtotime($value['library_time'])) ?></td>
                    <td><?= date('Y-m-d H:i:s',strtotime($value['start_time'])) ?></td>
                    <td><?= date('Y-m-d H:i:s',strtotime($value['end_time'])) ?></td>
                    <td><?= date('Y-m-d H:i:s',strtotime($value['expire_time'])) ?></td>
                    <td class="td-manage">
                        <button type="button" class="layui-btn" onclick="save_materials(this)" data-id="<?= $value['id'] ?>">保存</button>
                        <button type="button" class="layui-btn layui-bg-red" onclick="member_del(this,<?= $value['id'] ?>)">撤回</button>
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
    $(document).ready(function () {
            $("#autofocus").focus();
        });   
    layui.use(['form','layer'], function(){
        $ = layui.jquery;
        var form = layui.form ,layer = layui.layer;
        //监听提交
        form.on('submit(add)', function(data){
            layer.load(3);
            $.post('<?= Url::to(['shelf-materials/create']) ?>', data.field, function (res) {
                if (res.status === 200) {
                    layer.closeAll();
                    location.href= 'index?shelf_number=' + res.data;
                } else {
                    layer.msg(res.msg, {icon: 1,time: 3000},function(){
                        layer.closeAll();
                        location.href= 'index?shelf_number=' + res.data;
                    });
                }
                
            }, 'json');
            return false;
        });
    });

    //保存物料
    function save_materials(obj) {
        var data_id    = $(obj).data('id');
        $.ajax({
            url: '<?= Url::to(['shelf-materials/save']) ?>',
            type: 'GET',
            data: {id: data_id},
            dataType: 'json',
            success: function (res) {
                if (res.status === 200) {
                    layer.msg(res.msg, {icon: 1,time: 1500}, function () {
                        location.href= 'index';
                    });
                } else {
                    layer.msg(res.msg, {icon: 5});
                }
            }
        });
        layer.close(index);
    }

    //撤回物料
    function member_del(obj,id){
        layer.confirm('确认要撤回吗？',function(index){
            $.ajax({
                url: '<?= Url::to(['shelf-materials/withdraw']) ?>',
                type: 'GET',
                data: {id: id},
                dataType: 'json',
                success: function (res) {
                    if (res.status === 200) {
                        $(obj).parents("tr").remove();
                        layer.msg('已撤回!',{icon:1,time:1500});
                    } else {
                        layer.msg(res.msg, {icon: 5});
                    }
                }
            });
        });
    }

    //一键保存物料
    function save_all(){
        var idlist   = [];
        $('.layui-form-checked').each(function () {
            if (typeof($(this).data('id')) !="undefined" && $(this).data('id')!=0){
                idlist.push($(this).data('id'));
            }
        });
        if(idlist.length == 0){
            layer.msg('请勾选要保存的物料!', {icon: 5});
            return false;
        }
        layer.load(3);
        $.get('<?= Url::to([$this->context->id . '/save-all'])?>', {idlist: idlist}, function (data) {
            layer.closeAll();
            var res = eval('(' + data + ')');
            if (res.status == 200) {
                layer.msg(res.msg, {icon: 1,time: 1500}, function () {
                    location.href= 'index?id=' + res.data;
                })
            } else {
                layer.msg(res.msg, {icon: 2, time: 3000})
            }
        });
    }
</script>
<?php $this->endBlock() ?>
