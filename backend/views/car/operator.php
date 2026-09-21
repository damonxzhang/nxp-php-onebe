<?php
use backend\models\UserInfo;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
$this->registerCssFile('@css/ir.css');
?>
<style>
    .auto-input{
        display: none;
    }
</style>
<div class="x-body layui-anim layui-anim-up">
    <form class="layui-form">
        <div class="layui-row">
            <div class="layui-col-sm4">
                <div class="layui-form-item">
                    <label class="layui-form-label">
                    TRACE NO:
                    </label>
                    <div class="layui-input-inline">
                        <input type="hidden" name="car_id" value="<?= $carModel->id ?>">
                        <input type="text" value="<?= $carModel->trace_no; ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
            </div>
            <div class="layui-col-sm4">
                <div class="layui-form-item">
                    <label class="layui-form-label">
                    日期:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text" value="<?php echo date('Y-m-d', time()); ?>" readonly="readonly" lay-verify="required" autocomplete="off" class="layui-input">
                    </div>
                </div>
            </div>
            <div class="layui-col-sm4">
                <div class="layui-form-item">
                    <label class="layui-form-label">
                    领取人:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text" readonly="readonly"  value="<?= $userInfo->chinese_name; ?>" autocomplete="off" class="layui-input">
                    </div>
                </div>
            </div>
            <?php if(!empty($carOperatorJudgeTake)){ ?>
                <div class="layui-form-item layui-form-text">
                    <label for="desc" class="layui-form-label">
                    带班驳回原因:
                    </label>
                    <div class="layui-input-block">
                        <textarea  readonly="readonly" class="layui-textarea" style="width: 800px;height: 100px;"><?php echo $carOperatorJudge['reasons_dismissal']; ?> </textarea>
                    </div>
                </div>
            <?php } ?>
            <?php if(!empty($carOperatorJudgeHighlevel)){ ?>
                <div class="layui-form-item layui-form-text">
                    <label for="desc" class="layui-form-label">
                    HighLevel驳回原因:
                    </label>
                    <div class="layui-input-block">
                        <textarea  readonly="readonly" class="layui-textarea" style="width: 800px;height: 100px;"><?php echo $carOperatorJudgeHighlevel['reasons_dismissal']; ?> </textarea>
                    </div>
                </div>
            <?php } ?>
            <div class="layui-form-item layui-form-text">
                <label for="desc" class="layui-form-label">
                    <span class="x-red">*</span>缺陷原因:
                </label>
                <div class="layui-input-block">
                    <textarea lay-verify="required" name="reasons_defects"  class="layui-textarea" style="width: 800px;height: 100px;"><?php echo $carModel['reasons_defects']; ?> </textarea>
                </div>
            </div>
            <div class="layui-form-item layui-form-text">
                <label for="desc" class="layui-form-label">
                <span class="x-red">*</span>改正行动:
                </label>
                <div class="layui-input-block">
                    <textarea lay-verify="required" name="corrective_action" class="layui-textarea" style="width: 800px;height: 100px;"><?php echo $carModel['corrective_action']; ?> </textarea>
                </div>
            </div>

            <div class="layui-form-item layui-form-text">
                <label for="desc" class="layui-form-label">
                    上传图片:
                </label>
                <div class="layui-input-block">
                    <div class="layui-upload">
                    <button type="button" class="layui-btn" id="image_upload">多图片上传</button>
                    <blockquote class="layui-elem-quote layui-quote-nm" style="width: 800px;margin-top: 10px;">
                            预览图：
                            <div class="layui-upload-list" id="image_show">
                                <?php
                                if(!empty($carModel->second_files_mapping)):
                                    foreach ($carModel->second_files_mapping as  $val):
                                        if(empty($val)) continue;
                                        ?>
                                        <span class="itemimg">
                                        <input type="hidden" name="second_files_mapping[]" value="<?=$val?>">
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
        <div class="layui-form-item" style="margin-top: 50px;">
            <label for="" class="layui-form-label"></label>
            <button class="layui-btn" lay-filter="add" lay-submit="" type="button">
                保存
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
        //监听提交
        form.on('submit(add)', function(data){
            // var checkb = true;
            // var json=eval(data.field);
            // $.each(json,function(key,value){  
            //     if(!json.hasOwnProperty("second_files_mapping[0]")){
            //         checkb = false;
            //         return false;
            //     }
            // });
            // if (checkb == false) {
            //     layer.msg('请上传图片！');
            //     return false;
            // }else{
                layer.load(3);
                $.post('<?= Url::to(['car/operator']) ?>', data.field, function (res) {
                    layer.closeAll();
                    if (res.status === 200) {
                        layer.msg(res.msg, {icon: 1,time: 1500}, function () {
                            var index = parent.layer.getFrameIndex(window.name);
                            parent.layer.close(index);
                            parent.location.reload();
                        })
                    } else if(res.status === 400) {
                        layer.alert(res.msg);
                    }else{
                        layer.msg(res.msg, {icon: 2, time: 1500});
                    }
                }, 'json');
                return false;
       //     }
        });
    });
</script>
<?php $this->endBlock() ?>
<?php
$js = <<<JS

      /**
     * Ir页面图片上传
     */
    layui.use('upload', function() {
        var $ = layui.jquery,
            upload = layui.upload;
        //多图片上传
        upload.render({
            elem: '#image_upload',
            url: 'upload',
            multiple: true,
            done: function(res) {
                //上传完毕
                if(res.code == 200){
                    var html = '<span class="itemimg">' +
                        '<input type="hidden" name="second_files_mapping[]" value="'+res.path+'">'+
                        '<img src="' + res.path + '"  class="layui-upload-img show-img">' +
                        '<i class="delicon layui-icon layui-icon-close-fill image-span"></i>'+
                        '</span>';
                    $('#image_show').append(html)
                }else{
                    layer.msg(res.message);
                }
    
            }
        });
    });
    $(document).on('click','.image-span',function(){
        var _this = $(this).parents('span');
        layer.confirm('是否确认删除?',function(index){
            _this.remove();
            layer.closeAll();
        });
    });
  
JS;

$this->registerJs($js);

?>