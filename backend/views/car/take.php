<?php

use yii\helpers\Url;
use backend\models\UserInfo;
use backend\models\Car;
$this->registerCssFile('@css/ir.css');
?>
<div class="x-body layui-anim layui-anim-up">
    <form class="layui-form">
        <div class="layui-row">
            <div class="layui-col-sm4">
                <div class="layui-form-item">
                    <label class="layui-form-label">
                    TRACE NO:
                    </label>
                    <div class="layui-input-inline">
                        <input type="hidden" id="car_id" value="<?= $carModel->id ?>">
                        <input type="text" value="<?php echo $carModel['trace_no']; ?>" readonly="readonly"  autocomplete="off" class="layui-input">
                    </div>
                </div>
            </div>
            <div class="layui-col-sm4">
                <div class="layui-form-item">
                    <label class="layui-form-label">
                    日期:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text"  value="<?php echo $carModel['date']; ?>"  readonly="readonly"  autocomplete="off" class="layui-input">
                    </div>
                </div>
            </div>
            <div class="layui-col-sm4">
                <div class="layui-form-item">
                    <label class="layui-form-label">
                    领取人:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text" readonly="readonly"  value="<?php echo (new Car())->getOperatorChineseName($carModel['id']); ?>" autocomplete="off" class="layui-input">
                    </div>
                </div>
            </div>
            <div class="layui-form-item layui-form-text">
                <label for="desc" class="layui-form-label">
                缺陷原因:
                </label>
                <div class="layui-input-block">
                    <textarea class="layui-textarea" readonly="readonly" style="width: 800px;height: 100px;"> <?php echo $carModel['reasons_defects']; ?> </textarea>
                </div>
            </div>
            <div class="layui-form-item layui-form-text">
                <label for="desc" class="layui-form-label">
                改正行动:
                </label>
                <div class="layui-input-block">
                    <textarea  class="layui-textarea" readonly="readonly" style="width: 800px;height: 100px;"><?php echo $carModel['corrective_action']; ?></textarea>
                </div>
            </div>
            <div class="layui-form-item layui-form-text">
                <label for="desc" class="layui-form-label">
                    上传图片:
                </label>
                <div class="layui-input-block">
                    <div class="layui-upload">
                    <blockquote class="layui-elem-quote layui-quote-nm" style="width: 800px;margin-top: 10px;">
                            预览图：
                            <div class="layui-upload-list">
                                <?php
                                if(!empty($carModel->second_files_mapping)):
                                    foreach ($carModel->second_files_mapping as $val):
                                        if(empty($val)) continue;
                                        ?>
                                        <span class="itemimg">
                                            <img src="<?=$val?>" class="layui-upload-img show-img">
                                            <i class="delicon layui-icon layui-icon-close-fill image-span"></i>
                                        </span>
                                    <?php
                                    endforeach;
                                endif;
                                ?>
                            </div>
                        </blockquote>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-form-item layui-form-text">
            <label for="desc" class="layui-form-label">
            驳回原因:
            </label>
            <div class="layui-input-block">
                <textarea name="reasons_dismissal" class="layui-textarea" style="width: 800px;height: 100px;"></textarea>
            </div>
        </div>
        <div class="layui-form-item" style="margin-top: 50px;">
            <label for="" class="layui-form-label"></label>
            <button class="layui-btn" lay-filter="addtake" lay-submit="" type="button">
                确认
            </button>
            <button class="layui-btn layui-bg-orange" lay-filter="rejecttake" lay-submit="" type="button">
                驳回
            </button>
        </div>
    </form>
</div>

<?php $this->beginBlock('footer') ?>
<script>
    //提交数据
    layui.use(['form','layer'], function(){
        $ = layui.jquery;
        var form = layui.form ,layer = layui.layer;
        var car_id  = $('#car_id').val();

        //监听提交
        form.on('submit(addtake)', function(data){
          //  layer.load(3);
            $.post('<?= Url::to(['car/take']) ?>',  { car_id: car_id,result:1,reasons_dismissal:data.field.reasons_dismissal}, function (res) {
           //     layer.closeAll();
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
    //提交数据
    layui.use(['form','layer'], function(){
        $ = layui.jquery;
        var form = layui.form ,layer = layui.layer;
        var car_id   = $('#car_id').val();
      
        //监听提交
        form.on('submit(rejecttake)', function(data){
          //  layer.load(3);
            $.post('<?= Url::to(['car/take']) ?>',  { car_id: car_id,result:2,reasons_dismissal:data.field.reasons_dismissal}, function (res) {
           //     layer.closeAll();
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
