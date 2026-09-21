<?php
use yii\helpers\Url;
use backend\models\ShelfScattered;

use yii\helpers\Html;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;
use backend\models\UserInfo;
use backend\widgets\SearchUser;
use backend\widgets\LayuiActiveForm;

?>
    <div class="x-body layui-anim layui-anim-up">
        <form class="layui-form">
            <div class="layui-form-item">
                <label for="name" class="layui-form-label">
                    <span class="x-red">*</span>数量(磅):
                </label>
                <div class="layui-input-inline">
                    <input type="text" name="count" id="autofocus" lay-verify="required" autocomplete="off" class="layui-input" >
                </div>
            </div>
            <div class="layui-form-item">
            <label class="layui-form-label">
                    报废时间:
                </label>
                <div class="layui-input-inline">
                    <input type="text" autocomplete="off" value="" readonly="readonly" class="layui-input" id="scattered_time" name="scattered_time" style="width: 150px;">
                </div>
            </div>
            <div class="layui-form-item">
                <label for="" class="layui-form-label"></label>
                <button  class="layui-btn" lay-filter="add" lay-submit="">
                    增加
                </button>
            </div>
        </form>
    </div>

<?php $this->beginBlock('footer') ?>
    <script>
        layui.use('laydate', function(){
            var laydate = layui.laydate;
            laydate.render({
                elem: '#scattered_time'
                ,trigger : 'click'
                ,type: 'datetime'
                ,format: 'yyyy-MM-dd HH:mm:ss'
            });
        });

        layui.use(['form','layer'], function(){
            $ = layui.jquery;
            var form = layui.form ,layer = layui.layer;
            //监听提交
            form.on('submit(add)', function(data){
                layer.load(3);
                $.post('<?= Url::to(['shelf-scattered/create']) ?>', data.field, function (res) {
                    layer.closeAll();
                    if (res.status === 200) {
                        layer.msg(res.msg, {icon: 1,time: 1500}, function () {
                            var index = parent.layer.getFrameIndex(window.name);
                            parent.layer.close(index);
                            parent.location.reload();
                        })
                    } else {
                        layer.msg(res.msg, {icon: 2, time: 1500})
                    }
                }, 'json');
                return false;
            });
        });
    </script>
<?php $this->endBlock() ?>
