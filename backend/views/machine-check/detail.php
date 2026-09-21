<?php
use yii\helpers\Url;
?>
<div class="x-body layui-anim layui-anim-up">
    <form class="layui-form">
        <div class="layui-col-sm11">
            <input type="hidden" name="check_id" value="<?= $checkId ?>">
            <div class="layui-form-item">
                <label class="layui-form-label">
                    机器编号:
                </label>
                <div class="layui-input-inline">
                    <input type="text" value="<?= $number ?>" id="number_value" readonly="readonly"  autocomplete="off" class="layui-input">
                </div>
                <button type="button" class="layui-btn layui-btn-confirm" >确认</button>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">
                    二维码:
                </label>
                <div class="layui-input-inline">
                    <input type="text" value="" id="qrcode_value" readonly="readonly" autocomplete="off" class="layui-input">
                </div>
                <button type="button" class="layui-btn" onclick="x_admin_show('拍摄','<?= Url::to([$this->context->id . '/qrcode-camera']) ?>')"><i class="layui-icon">&#xe65d;</i>拍摄</button>
            </div>
            <div class="layui-upload-list">
                <table class="layui-table" style="margin-left: 110px;">
                    <thead>
                    <tr>
                        <th>序号</th>
                        <th>位置编号</th>
                        <th>位置图</th>
                        <th>标准</th>
                        <th>评分</th>
                        <th>备注</th>
                        <th>拍照</th>
                        <th>附件</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($optionArr)): ?>
                        <?php foreach ($optionArr as $key => $value): ?>
                            <tr>
                                <td style="width: 2%"><?= $key+1 ?></td>
                                <td style="width: 3%"><?= $value['position'] ?></td>
                                <td style="width:10%;height:100%;border: 1px #c0c0c0 solid;margin:0 ;padding:0 ;">
                                    <?php if (!empty($value['file_path'])): ?>
                                        <?php foreach ($value['file_path'] as $path): ?>
                                            <img src="<?= $baseUrl.$path?>" class="ez" alt="" style="margin:0;padding:0;float:left;">
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </td>
                                <td style="width: 15%"><?=$value['standard'] ?></td>
                                <td style="width: 15%">
                                    <input type="hidden" name="option_id[]" value="<?= $value['id'] ?>">
                                    <input type="radio" value="0" name="score[<?= $key ?>]" lay-skin="primary" title="0分" checked="">
                                    <input type="radio" value="1" name="score[<?= $key ?>]" lay-skin="warning" title="1分">
                                    <input type="radio" value="3" name="score[<?= $key ?>]" lay-skin="warning" title="3分">
                                </td>
                                <td style="width: 20%">
                                     <textarea placeholder="请输入备注" name="remarks[<?= $key ?>]" class="layui-textarea" style="width: 250px;height: 100px;"></textarea>
                                </td>
                                <td style="width: 3%">
                                    <button type="button" class="layui-btn" onclick="x_admin_show('拍照','<?= Url::to([$this->context->id . '/camera', 'key' => $key]) ?>')"><i class="layui-icon">&#xe65d;</i>拍照</button>
                                </td>
                                <td style="width: 20%">
                                    <div id="filepath<?= $key ?>"> </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="layui-form-item">
            <label for="" class="layui-form-label"></label>
            <button class="layui-btn layui-btn layui-btn-lg" lay-filter="update" lay-submit="">
                保存
            </button>
        </div>
    </form>
</div>
<?php $this->beginBlock('footer') ?>
<script>
    //获取子页面的传值
    function ChooseAdidValues(key,fileId,filepath) {
        if (filepath != "") {
            var html = '<span>' +
                '<input type="hidden" name="file_id['+key+'][]" value="'+fileId+'">'+
                '<img src=<?php $baseUrl ?>"' + filepath + '" class="ez" alt="">' +
                '<button type="button" style ="margin-left:10px " class="layui-btn layui-btn-danger" ><i class="layui-icon">&#xe640;</i>删除</button>'+
                '</span>';
            $('#filepath'+key).append(html);
        }
    }

    //删除图片
    $(document).on('click','.layui-btn-danger',function(){
        var _this = $(this).parents('span');
        layer.confirm('是否确认删除?',function(index){
            _this.remove();
            layer.closeAll();
        });
    });

    //确认设备
    $(document).on('click','.layui-btn-confirm',function(){
        layer.msg('确认成功!', {icon: 1, time: 1500});
    });

    //获取子页面的传值
    function ChooseQrcodeValues(qrcodeValue) {
        $('#qrcode_value').val(qrcodeValue);
    }
    
    //保存数据
    layui.use(['form','layer'], function(){
        var form = layui.form ,layer = layui.layer;
        form.on('submit(update)', function(data){
            // var qrcode_value = $('#qrcode_value').val();
            // var number_value = $('#number_value').val();
            // if(qrcode_value != number_value){
            //     layer.msg('请检查机器编号!', {icon: 2, time: 1500});
            //     return false;
            // }
            layer.load(3);
            $.post('<?= Url::to(['machine-check/detail']) ?>', data.field, function (res) {
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
