<?php

use yii\helpers\Html;
use backend\widgets\LayuiActiveForm;
use yii\helpers\Url;

?>
<div class="x-body layui-anim layui-anim-up">
    <form class="layui-form">
        <input type="hidden" name="id" value="<?= $model['id'] ?>">
        <div class="layui-form-item">
            <label class="layui-form-label">
            CaseOutline:
            </label>
            <div class="layui-input-inline">
                <input type="text" name="case_outline" value="<?= $model['case_outline'] ?>" lay-verify="required" autocomplete="off" style="width: 400px;" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
            PkgCode:
            </label>
            <div class="layui-input-inline">
                <input type="text" name="pkg_code" value="<?= $model['pkg_code'] ?>" lay-verify="required" autocomplete="off" style="width: 400px;" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
            PkgType:
            </label>
            <div class="layui-input-inline">
                <input type="text" name="pkg_type" value="<?= $model['pkg_type'] ?>" lay-verify="required" autocomplete="off" style="width: 400px;" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
             大小材料:
            </label>
            <div class="layui-input-inline" style="width: 400px;">
                <select name="type" lay-verify="required">
                    <option value="1" <?= $model['type']== 1 ?'selected':''  ?>>小材料</option>
                    <option value="2" <?= $model['type']== 2 ?'selected':''  ?>>大材料</option>
                </select>
            </div>
        </div>
        <div class="form-group field-irjudge-status">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">物料产品工程师:</label>
                <div class="layui-input-inline" style="width: 400px;">
                    <?= \backend\widgets\SearchUser::widget([
                        'name' => Html::getInputName($model, 'type_user_id'),
                        'url' => \yii\helpers\Url::to('/user-info/search-by-name'),
                        'value' => $model->type_user_id
                    ])?>
                <div class="layui-col-md4" style="color:red"><div class="help-block"></div></div>
            </div>
        </div>
        <div class="layui-form-item" style="margin-top:40px;">
            <label for="" class="layui-form-label"></label>
            <button class="layui-btn" lay-filter="update" lay-submit="">
                更新
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
            $.post('<?= Url::to(['car/pkgcode-update']) ?>', data.field, function (res) {
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

