<?php

use backend\models\ShelfMaterialsModel;
use yii\helpers\Url;
?>

<div class="x-body layui-anim layui-anim-up">
    <table class="layui-table" id="abnormal-table">
        <thead>
        <tr>
            <th>型号</th>
            <th>申请数量</th>
            <th>已打印物料数量</th>
            <th>异常数量</th>
        </thead>
        <tbody>
        <?php if (!empty($dataProvider)): ?>
            <?php foreach ($dataProvider as $value): ?>
                <tr>
                    <td hidden="hidden"><input type="text" value="<?= $value['id'] ?>"></td>
                    <td><?= (new ShelfMaterialsModel())->getPartNo($value['model_id']) ?></td>
                    <td><?= $value['apply_number'] ?></td>
                    <td><?= $value['print_number'] ?></td>
                    <td><input type="number" value="<?= $value['abnormal_number'] ?>"></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
    <div class="layui-form-item" style="margin-top: 50px;">
        <input type="button" id="submit-abnormal" class="layui-btn layui-btn-lg" value="保存">
    </div>
</div>

<?php

$submitUrl   = Url::to(["shelf-materials-bill/bill-abnormal-number"]);
$js = <<<JS
    $(document).ready(function() {
        //正的,小数部分以5结尾的浮点数或正整数
       // var reg = /^[1-9]\d*\.[5]$|0\.[5]$|^[1-9]\d*$/;
        var reg = /^[0-9]*$|^[0-9]+(\.0{1,100})*$|^[0-9]+(\.50{1,100})*$|^[0-9]+(\.5)*$/;
        //保存
        $("#submit-abnormal").click(function() {
            var str = "";
            var trLen = $("#abnormal-table tbody tr").length;
            for (var i = 0; i < trLen; i++) {
                var tr = $("#abnormal-table tbody").find("tr").eq(i); //获取每一行tr
                var id = $(tr).find("input").eq(0).val();
                var abnormal_number = $(tr).find("input").eq(1).val();
                var checkAmt = abnormal_number.match(reg) == null  ?  '' : abnormal_number.match(reg);
                if (checkAmt === '') {
                    layui.layer.msg("请输 0.5 的倍数的数字", {icon: 2, time: 3000});
                    return false;
                }

                var applyNumber = $(tr).find('td:nth-child(3)').text();
                if(Number(applyNumber) > 0){
                    //检查异常数量是否大于申请数量
                    if (Number(abnormal_number) > Number(applyNumber)) {
                        layui.layer.msg("异常数量不能大于申请数量", {icon: 2, time: 3000});
                        return false;
                    }

                    var printNumber = $(tr).find('td:nth-child(4)').text();
                    if(Number(printNumber) > 0){
                        //异常数量加已打印数量是否大于申请数量
                        if ((Number(abnormal_number) + Number(printNumber)) > Number(applyNumber)) {
                            layui.layer.msg("异常数量加已打印数量不能大于申请数量", {icon: 2, time: 3000});
                            return false;
                        }
                    }
                }
                str = str + id + ";" + abnormal_number + "|" ;//换行
            }
          
            if(str !== '') {
                layui.use(['layer'], function(){
                    $ = layui.jquery;
                    var layer = layui.layer;
                    $.ajax({
                        type: 'POST',
                        data: {abnormal_str: str},
                        dataType: 'json',
                        url: '{$submitUrl}',
                        success: function(res) {
                            if (res.status === 200) {
                                layer.msg(res.msg, {icon: 1,time: 1500}, function () {
                                    var index = parent.layer.getFrameIndex(window.name);
                                    parent.layer.close(index);
                                    parent.location.reload();
                                })
                            } else {
                                layer.msg(res.msg, {icon: 2, time: 3000});
                            }
                        }
                    });
                    return false;
                });
            }else{
                var layer = layui.layer;
                layer.msg('暂无需要填写异常!', {icon: 2, time: 3000});
            }
        });
    });

JS;
$this->registerJs($js);
?>

