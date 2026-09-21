<?php
use yii\helpers\Url;
use backend\models\Shelf;
?>
    <div class="x-body layui-anim layui-anim-up">
        <form class="layui-form">
            <div class="layui-form-item">
                <label for="name" class="layui-form-label">
                    <span class="x-red">*</span>货架标识:
                </label>
                <div class="layui-input-inline">
                    <select name="mark" lay-verify="required">
                        <?php foreach (Shelf::$markArr as $key => $value): ?>
                            <option value="<?= $key ?>" ><?= $value ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label for="name" class="layui-form-label">
                    <span class="x-red">*</span>格子编号:
                </label>
                <div class="layui-input-inline">
                    <input type="text" name="number" id="autofocus" lay-verify="required" autocomplete="off" class="layui-input" >
                </div>
            </div>
            <div class="layui-form-item">
                <label for="name" class="layui-form-label">
                    <span class="x-red">*</span>货架格子号:
                </label>
                <div class="layui-input-inline">
                    <input type="text" name="sort"  lay-verify="required" autocomplete="off" class="layui-input" >
                </div>
            </div>
            <div class="layui-form-item">
                <label for="name" class="layui-form-label">
                    <span class="x-red">*</span>楼层:
                </label>
                <div class="layui-input-inline">
                    <select name="floor" lay-verify="required">
                        <?php foreach (Shelf::$floorList as $key => $value): ?>
                            <option value="<?= $key ?>" ><?= $value ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label for="name" class="layui-form-label">
                    车号:
                </label>
                <div class="layui-input-inline">
                    <input type="text" name="car_number" autocomplete="off" class="layui-input" >
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
        layui.use(['form','layer'], function(){
            $ = layui.jquery;
            var form = layui.form ,layer = layui.layer;
            //监听提交
            form.on('submit(add)', function(data){
                layer.load(3);
                $.post('<?= Url::to(['shelf/create']) ?>', data.field, function (res) {
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