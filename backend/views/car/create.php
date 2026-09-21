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
    .layui-table-cell {
		overflow: visible;
	}
	
	.layui-table-box {
		overflow: visible;
	}
	
	.layui-table-body {
		overflow: visible;
	}

</style>

<div class="x-body layui-anim layui-anim-up">
    <form class="layui-form">
        <div class="layui-row">
            <div class="layui-col-sm4">
         
                <div class="layui-form-item" style="display: flex;">
                    <label class="layui-form-label">
                    <span class="x-red">*</span> 批号:
                    </label>
                    <div class="layui-input-inline" >
                        <input type="text" name="batch_number"  id="batch_number" lay-verify="required" autocomplete="off" class="layui-input">
                    </div>
                    <div style="margin-right: 5px; ">
                        <button type="button" class="layui-btn get-station">获取操作站</button>
                    </div>
                </div>
                <div class="layui-form-item" style="display: flex;">
                    <label class="layui-form-label">
                    <span class="x-red">*</span>  操作站:
                    </label>
                    <div class="layui-input-inline">
                        <select name="station" lay-verify="required" id="stationSelect">
                        </select>
                    </div>
                    <div style="margin-right: 5px; ">
                        <button type="button" class="layui-btn get-parameters">确认</button>
                    </div>
                </div>
            </div>
            <div class="layui-col-sm4">
                <div class="layui-form-item">
                    <label class="layui-form-label">
                    日期:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text" name="date"  value="<?php echo date('Y-m-d', time()); ?>" readonly="readonly" lay-verify="required" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">
                    器件型号:
                    </label>
                    <div class="layui-input-inline">
                       <input type="text" name="device_model"  id="device_model" readonly="readonly" lay-verify="required" autocomplete="off" class="layui-input" >
                    </div>
                </div>
               
            </div>
            <div class="layui-col-sm4">
                <div class="layui-form-item">
                    <label class="layui-form-label">
                    班别:
                    </label>
                    <div class="layui-input-inline">
                      <input type="text" value="<?= ArrayHelper::getValue(UserInfo::$shiftArr, $userInfo['shift'], '') ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">
                    封装类型:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text" name="package"  id="package"  readonly="readonly"  required lay-verify="required" autocomplete="off" class="layui-input">
                    </div>
                </div>
            </div>
            <div class="layui-col-sm4">
                <div class="layui-form-item">
                    <label class="layui-form-label">
                    机器号:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text" name="machine_number"  id="machine_number"   readonly="readonly"  autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">
                    <span class="x-red">*</span>缺陷项目:
                    </label>
                    <div class="layui-input-inline">
                        <select name="defective_items" lay-verify="required">
                            <?php foreach ($dafectArr as $defectid => $defect): ?>
                                <option value="<?= $defectid ?>"><?= $defect ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="layui-col-sm4">
                <div class="layui-form-item">
                    <label class="layui-form-label">
                    操作员:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text" name="operator" id="operator" readonly="readonly"  required lay-verify="required" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">
                    <span class="x-red">*</span>次品缺陷数量:
                    </label>
                    <div class="layui-input-inline">
                        <input type="number" name="defective_number"  required lay-verify="required" autocomplete="off" class="layui-input" >
                    </div>
                </div>
               
            </div>
            <div class="layui-col-sm4">
                <div class="layui-form-item">
                    <label class="layui-form-label">
                    检查员:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text" readonly="readonly"  value="<?= $userInfo->chinese_name; ?>" autocomplete="off" class="layui-input">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">
                <span class="x-red">*</span>物料存放位置:
                </label>
                <div class="layui-input-block">
                    <input type="text" name="storage_location"  required lay-verify="required" autocomplete="off" class="layui-input" style="width: 800px;">
                </div>
            </div>
            <div class="layui-form-item layui-form-text">
                <label for="desc" class="layui-form-label">
                    <span class="x-red">*</span>缺陷项目描述:
                </label>
                <div class="layui-input-block">
                    <textarea placeholder="请输入内容" lay-verify="required" name="description" class="layui-textarea" style="width: 800px;height: 100px;"></textarea>
                </div>
            </div>
            <div class="layui-form-item layui-form-text">
                <label for="desc" class="layui-form-label">
                    <span class="x-red">*</span>上传图片:
                </label>
                <div class="layui-input-block">
                    <div class="layui-upload">
                    <button type="button" class="layui-btn" id="image_upload">多图片上传</button>
                    <blockquote class="layui-elem-quote layui-quote-nm" style="width: 800px;margin-top: 10px;">
                            预览图：<div class="layui-upload-list" id="image_show"></div>
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
    $('.get-station').click(()=>{
        var layer = layui.layer;
        var batchNumber   = $('#batch_number').val();
        if(batchNumber == '' || batchNumber == "underfined"){
            layer.msg('请输入批号', {icon: 2, time: 1500});
        }
        $.post('<?= Url::to(['car/get-station']) ?>', {batch_number:batchNumber}, function (res) {
            layer.closeAll();
            if (res.status === 100) {
                layer.msg(res.msg, {icon: 2, time: 1500});
            } else {
                layer.msg('获取成功!');
                $('#stationSelect').empty();
                $.each(res.data, function(index, value) {
                    var option = $('<option></option>').attr('value', value).text(value);
                    $('#stationSelect').append(option);
                });
                layui.form.render('select');
            }
        }, 'json');
        return false;
    });
    
    $('.get-parameters').click(()=>{
        var layer = layui.layer;
        var batchNumber   = $('#batch_number').val();
        var selectedValue = $('#stationSelect').val();
        if(selectedValue == '' || selectedValue == "underfined"){
            layer.msg('请先选择操作站！', {icon: 2, time: 1500});
            return false;
        }
        layer.load(3);
        $.post('<?= Url::to(['car/get-parameters']) ?>', { batch_number: batchNumber,station:selectedValue}, function(res) {
            if (res.status === 200) {
                layer.closeAll();
                layer.msg(res.msg, {icon: 1,time: 1500}, function () {
                    $('#device_model').val(res.data.device_model);
                    $('#package').val(res.data.package);
                    $('#machine_number').val(res.data.machine_number);
                    $('#operator').val(res.data.operator);
                });
            }else{
                layer.closeAll();
                layer.msg(res.msg, {icon: 2, time: 3000});
            }
        }, 'json');
        return false;
    });

    //提交数据
    layui.use(['form','layer'], function(){
        $ = layui.jquery;
        var form = layui.form ,layer = layui.layer;
        //监听提交
        form.on('submit(add)', function(data){
            var checkb = true;
            var json=eval(data.field);
            $.each(json,function(key,value){  
                if(!json.hasOwnProperty("files_mapping[0]")){
                    checkb = false;
                    return false;
                }
            });
            if (checkb == false) {
                layer.msg('请上传图片！');
                return false;
            }else{
                layer.load(3);
                $.post('<?= Url::to(['car/create']) ?>', data.field, function (res) {
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
            }
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
                        '<input type="hidden" name="files_mapping[]" value="'+res.path+'">'+
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