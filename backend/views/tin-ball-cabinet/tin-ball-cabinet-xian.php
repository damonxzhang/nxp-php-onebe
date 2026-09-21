<?php

use yii\helpers\Url;

?>
<style>
    * {
        margin:0;
        padding:0;
        border:0;
    }
    table {
        width:100%;
        margin:20px auto;
        border-collapse:collapse;
    }
    table tr td {
        border:1px solid rosybrown;
        height:25px;
    }
    table tr td input {
        width:100%;
        height:100%;
    }

</style>
<div class="x-body layui-anim layui-anim-up">
    <table class="layui-table" id="bill-table">
        <thead>
            <th>型号</th>
            <th>中间库库存(条)</th>
            <th>产线库存(瓶)</th>
            <th>申请数量(条)</th>
        </thead>
        <tbody>
            <tr>
                <td hidden="hidden"><input value=""></td>
                <td style="width: 30%;">  
                    <select id="addRowSelect">
                        <option value="">下拉选择</option>
                        <?php foreach ($partNoArr as $value): ?>
                            <option value="<?= $value['id'] ?>"><?= $value['part_no'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <div class="layui-form-item" style="margin-top: 50px;">
        <input type="button" id="savemodel" class="layui-btn layui-btn-lg" value="保存">
    </div>
</div>

<?php
$modelDetailUrl = Url::to(["tin-ball-cabinet/get-model-detail"]);
$saveModelUrl   = Url::to(["tin-ball-cabinet/save-model-detail"]);
$js = <<<JS
    $(document).ready(function() {

        $('#addRowSelect').change(function(){
            var tdAmt = $('#bill-table tbody tr:nth-last-child(2) td:last-child input').val();
            if (tdAmt == '') {
                layui.layer.msg("上一行“申请数量(条)”不允许空", {icon: 2, time: 3000});
                $('#addRowSelect option:selected').prop('selected', false);
                return false;

            }else if(tdAmt > 0){
                //检查申请数量是否大于中间库存数量
                var tdMdt = $('#bill-table tbody tr:nth-last-child(2) td:nth-child(3) input').val();
                if (Number(tdAmt) > Number(tdMdt)) {
                    layui.layer.msg("上一行“申请数量(条)”不允许大于中间库库存(条)", {icon: 2, time: 3000});
                    $('#addRowSelect option:selected').prop('selected', false);
                    return false;
                }

                //正的,小数部分以5结尾的浮点数或正整数
              //  var reg = /^[1-9]\d*\.[5]$|0\.[5]$|^[1-9]\d*$/;
                var reg = /^[0-9]*$|^[0-9]+(\.0{1,100})*$|^[0-9]+(\.50{1,100})*$|^[0-9]+(\.5)*$/;
                var checkAmt = tdAmt.match(reg) == null  ?  '' : tdAmt.match(reg);
                if (checkAmt === '') {
                    layui.layer.msg("上一行请输 0.5 的倍数的数字", {icon: 2, time: 3000});
                    $('#addRowSelect option:selected').prop('selected', false);
                    return false;
                }
            }

            var selectedValue = $(this).find('option:selected').val();
            if(selectedValue !== '') {
                $.ajax({
                    type: 'POST',
                    data: {id: selectedValue},
                    dataType: 'json',
                    url: '{$modelDetailUrl}',
                    success: function(res) {
                        if (res.status === 200) {
                            var newRow = '<tr><td hidden="hidden"><input type="text" value="'+ res.data.id+'"></td><td hidden="hidden"><input type="text" value="'+ res.data.api_partno_id +'"></td><td><input type="text" readonly value="'+ res.data.part_no+'"></td><td><input type="text" readonly value="'+ res.data.middle_number+'"></td><td><input type="text" readonly value="'+ res.data.product_number+'"></td><td><input type="number"  /></td></tr>';          
                            $('#bill-table tr').eq(-1).before(newRow); // 在第二行之前插入新行
                            $('#addRowSelect option[value="' + selectedValue + '"]').remove(); // 删除选中的选项
                        } else {
                            layui.layer.msg(res.msg, {icon: 2, time: 3000});
                        }
                    }
                });
            }
        });
       
        //保存物料型号
        $("#savemodel").click(function() {
            var tdAmt = $('#bill-table tbody tr:nth-last-child(2) td:last-child input').val();
            if (tdAmt == '') {
                layui.layer.msg("“申请数量(条)”不允许空", {icon: 2, time: 3000});
                $('#addRowSelect option:selected').prop('selected', false);
                return false;
            }
            //检查申请数量是否大于中间库存数量
            var tdMdt = $('#bill-table tbody tr:nth-last-child(2) td:nth-child(3) input').val();
            if (Number(tdAmt) > Number(tdMdt)) {
                layui.layer.msg("上一行“申请数量(条)”不允许大于中间库库存(条)", {icon: 2, time: 3000});
                return false;
            }

            //正的,小数部分以5结尾的浮点数或正整数
           // var reg = /^[1-9]\d*\.[5]$|0\.[5]$|^[1-9]\d*$/;
            var reg = /^[0-9]*$|^[0-9]+(\.0{1,100})*$|^[0-9]+(\.50{1,100})*$|^[0-9]+(\.5)*$/;
            var checkAmt = tdAmt.match(reg) == null  ?  '' : tdAmt.match(reg);
            if (checkAmt === '') {
                layui.layer.msg("请输 0.5 的倍数的数字", {icon: 2, time: 3000});
                return false;
            }
            var model_str = "";
            var trLen = $("#bill-table tbody tr").length;
            var number = trLen - 1;
            for (var i = 0; i < number; i++) {
                var tr = $("#bill-table tbody").find("tr").eq(i); //获取每一行tr
                var tdLen = $(tr).find("td").length;
                for (var j = 0; j < tdLen; j++) {
                    td = $(tr).find("td input").eq(j); //获取每行td
                    var dir = $(tr).find("input").eq(j).val();
                    if(j == tdLen - 1){
                        model_str = model_str + dir;
                    }else{
                        model_str = model_str + dir + ";";
                    }
                }
                model_str = model_str + "|" ;//换行
            }
            
            if(model_str !== '') {
                $('#savemodel').attr('disabled',"true");
                layui.use(['layer'], function(){
                    $ = layui.jquery;
                    var layer = layui.layer;
                    layer.load(2,{shade:0.2});
                    $.ajax({
                        type: 'POST',
                        data: {model_str: model_str},
                        dataType: 'json',
                        url: '{$saveModelUrl}',
                        success: function(res) {
                            if (res.status === 200) {
                                layer.msg(res.msg, {icon: 1,time: 1500}, function () {
                                    var index = parent.layer.getFrameIndex(window.name);
                                    parent.layer.close(index);
                                    parent.location.reload();
                                })
                            } else {
                                layer.closeAll('loading');
                                layer.msg(res.msg, {icon: 2, time: 3000});
                                $('#savemodel').removeAttr("disabled"); 
                            }
                        }
                    });
                    return false;
                });
            }else{
                var layer = layui.layer;
                layer.msg('请选择型号！', {icon: 2, time: 3000});
            }
        });
    });

JS;
$this->registerJs($js);


 ?>

