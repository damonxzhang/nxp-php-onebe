<?php

use backend\models\ShelfMaterials;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">货架管理</a>
        <a href="javascript:;">
          <cite>转交物料</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so">
            <div class="layui-form-item">
                <label for="name" class="layui-form-label">
                    <span class="x-red">*</span>物料编号:
                </label>
                <div class="layui-input-inline">
                    <input type="text" id="autofocus" style="width: 190px" name="cont_no" value="<?= ArrayHelper::getValue($request, 'cont_no') ?>" lay-verify="required" autocomplete="off" class="layui-input" >
                </div>
                <button class="layui-btn"  lay-submit="" lay-filter="sreach"><i class="layui-icon">&#xe615;</i>查询</button>
            </div>
        </form>
    </div>
    <div class="layui-row">
        <form class="layui-form" id="submit-form">
            <input type="hidden" name ="cont_no" value="<?= ArrayHelper::getValue($request, 'cont_no') ?>">
            <div class="layui-form-item">
                <label class="layui-form-label">
                    <span class="x-red">*</span>当前所有者:
                </label>
                <div class="layui-input-inline">
                    <input type="text" name="badge_code" id="badge_code" value="<?= ArrayHelper::getValue($request, 'badge_code') ?>" onkeypress="if(event.keyCode==13) focusNextInput(this);"  lay-verify="required" autocomplete="off" class="layui-input" >
                </div>
            </div>
            <div class="layui-form-item">
                <label for="name" class="layui-form-label">
                    <span class="x-red">*</span>转交人员:
                </label>
                <div class="layui-input-inline">
                    <input type="text" name="transfer_badge_code" id="transfer_badge_code" value="<?= ArrayHelper::getValue($request, 'transfer_badge_code') ?>" onkeypress="if(event.keyCode==13) subform();" lay-verify="required" autocomplete="off" class="layui-input" >
                </div>
            </div>
            <div class="layui-form-item">
                <label for="" class="layui-form-label"></label>
                <button  class="layui-btn" lay-filter="transfer" lay-submit="" id="submitform" onclick="subform()" type="button">
                    完成转交
                </button>
            </div>
        </form>
    </div>
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>物料编号</th>
            <th>货架编号</th>
            <th>操作人</th>
            <th>操作状态</th>
            <th>出库时间</th>
            <th>醒料起始时间</th>
            <th>醒料截止时间</th>
            <th>过期时间</th>
        </thead>
        <tbody>
        <?php if (!empty($dataProvider)): ?>
            <?php foreach ($dataProvider as $value): ?>
                <tr>
                    <td><?= $value['id'] ?></td>
                    <td><?= $value['cont_no'] ?></td>
                    <td><?= ArrayHelper::getValue($value->shelf, 'shelf_number','') ?></td>
                    <td><?= ArrayHelper::getValue($value->userinfo, 'chinese_name','') ?></td>
                    <td><?= ArrayHelper::getValue(ShelfMaterials::$statusArr, $value['status'],'') ?></td>
                    <td><?= empty($value['library_time']) ? '' :date('Y-m-d H:i:s',strtotime($value['library_time'])) ?></td>
                    <td><?= date('Y-m-d H:i:s',strtotime($value['start_time'])) ?></td>
                    <td><?= date('Y-m-d H:i:s',strtotime($value['end_time'])) ?></td>
                    <td><?= date('Y-m-d H:i:s',strtotime($value['expire_time'])) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
    <?= $this->render('@app/views/layouts/pagination', compact('pagination')) ?>
</div>

<?php $this->beginBlock('footer') ?>
<script>
    layui.use(['form','layer'], function(){
        $ = layui.jquery;
        var form = layui.form ,layer = layui.layer;
        //查询物料
        form.on('submit(search)', function(data){
            layer.load(3);
            $.post('<?= Url::to(['shelf-materials/transfer']) ?>', data.field, function (res) {
                layer.closeAll();
                if (res.status === 200) {
                    layer.msg(res.msg, {icon: 1,time: 2000}, function () {
                        location.href='<?= Url::to(ArrayHelper::merge(['shelf-materials/transfer'], Yii::$app->request->queryParams)) ?>';
                    });
                } else {
                    layer.msg(res.msg, {icon: 2, time: 2000})
                }
            }, 'json');
            return false;
        });
    });

    //转交物料
    layui.use(['form','layer'], function(){
        $ = layui.jquery;
        var form = layui.form ,layer = layui.layer;
        //监听提交
        form.on('submit(transfer)', function(data){
            layer.load(3);
            $.post('<?= Url::to(['shelf-materials/transfer-success']) ?>', data.field, function (res) {
                layer.closeAll();
                if (res.status === 200) {
                    layer.msg(res.msg, {icon: 1,time: 2000}, function () {
                        location.href='<?= Url::to(ArrayHelper::merge(['shelf-materials/transfer'], Yii::$app->request->queryParams)) ?>';
                    });
                } else {
                    layer.msg(res.msg, {icon: 2, time: 2000})
                }
            }, 'json');
            return false;
        });
    });
</script>
<?php $this->endBlock() ?>

