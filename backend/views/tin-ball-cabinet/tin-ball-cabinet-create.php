<?php

use backend\models\TinBallCabinetRelation;
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
            <th>中间库库存</th>
            <th>产线库存</th>
            <th>当前单位</th>
            <th>申请数量</th>
            <th>操作</th>
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
                <td style="width: 16%;">
                    <select id="unitSelect">
                        <option value="">请选择单位</option>
                        <?php foreach (TinBallCabinetRelation::$unitArr as $key => $unit): ?>
                            <option value="<?= $key ?>"><?= $unit ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
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

    $(document).on('click', '.delete-row-btn', function() {
        var deleteBtn = $(this);
        var layer = layui.layer;
        layer.confirm('确定要删除这一行吗？', {icon: 3}, function(index){
            deleteBtn.closest('tr').remove();
            layer.msg('删除成功!', {icon: 1});
            layer.close(index);
        });
    });

    $(document).ready(function() {
        $('#addRowSelect').change(function(){
            var trCount = $('#bill-table tbody tr').length;
            if (trCount <= 1) {
                return false;
            }

            var prevRow = $('#bill-table tbody tr:nth-last-child(2)');
            var tdInput = prevRow.find('td:has(input):last input').val() || '';
            tdAmt = $.trim(tdInput);
            if (tdAmt == '') {
                layui.layer.msg("上一行“申请数量”不允许空", {icon: 2, time: 3000});
                $('#addRowSelect option:selected').prop('selected', false);
                return false;

            }else if(tdAmt > 0){
                // 获取中间库存值（去空格+容错）
                var tdMdt = prevRow.find('td:nth-child(4) input').val() || '0';
                tdMdt = $.trim(tdMdt);
                var amtNum = parseFloat(tdAmt) || 0;
                var mdtNum = parseFloat(tdMdt) || 0;

                // 校验：申请数量不允许大于中间库存
                if (amtNum > mdtNum) {
                    layui.layer.msg("上一行“申请数量”不允许大于中间库库存", {icon: 2, time: 3000});
                    $('#addRowSelect option:selected').prop('selected', false);
                    return false;
                }

                // 校验：必须是0.5的倍数
                var reg = /^\d+(\.5)?$/;
                if (!reg.test(tdAmt)) {
                    layui.layer.msg("上一行请输 0.5 的倍数的数字", {icon: 2, time: 3000});
                    $('#addRowSelect option:selected').prop('selected', false);
                    return false;
                }
            }
        });
        $('#unitSelect').change(function(){
            var addRowSelectValue = $('#addRowSelect').find('option:selected').val();
            if(addRowSelectValue === ''){
                $(this).val('');
                var layer = layui.layer;
                layer.msg('请先选择型号！', {icon: 2, time: 3000});
                return false;
            }

            var unitSelectValue   = $(this).find('option:selected').val();
            if(unitSelectValue !== '') {
                $(this).val('');
                $.ajax({
                    type: 'POST',
                    data: {id: addRowSelectValue,unit: unitSelectValue},
                    dataType: 'json',
                    url: '{$modelDetailUrl}',
                    success: function(res) {
                        if (res.status === 200) {
                            var newRow = '<tr><td hidden="hidden"><input type="text" value="'+ res.data.id+'"></td><td hidden="hidden"><input type="text" value="'+ res.data.api_partno_id +'"></td><td><input type="text" readonly value="'+ res.data.part_no+'"></td><td><input type="text" readonly value="'+ res.data.middle_number+'"></td><td><input type="text" readonly value="'+ res.data.product_number+'"></td><td><input type="text" readonly value="'+ res.data.unit+'"></td><td><input type="number" /></td><td><button class="layui-btn layui-btn delete-row-btn" ><i class="layui-icon">&#xe640;</i>删除</button></td></tr>';          
                            $('#bill-table tr').eq(-1).before(newRow); // 在第二行之前插入新行
                            $('#addRowSelect option[value="' + addRowSelectValue + '"]').remove(); // 删除选中的选项
                        } else {
                            layui.layer.msg(res.msg, {icon: 2, time: 3000});
                        }
                    }
                });
            }
        });
       
        //保存物料型号
        $("#savemodel").click(function() {
			$("#savemodel").prop('disabled', true).text('提交中...');
            var prevRow = $('#bill-table tbody tr:nth-last-child(2)');
            var tdInput = prevRow.find('td:has(input):last input').val() || '';
            tdAmt = $.trim(tdInput);
            if (tdAmt == '') {
                layui.layer.msg("上一行“申请数量”不允许空", {icon: 2, time: 3000});
                $('#addRowSelect option:selected').prop('selected', false);
                return false;
            }

            // 获取中间库存值（去空格+容错）
            var tdMdt = prevRow.find('td:nth-child(4) input').val() || '0';
            tdMdt = $.trim(tdMdt);
            var amtNum = parseFloat(tdAmt) || 0;
            var mdtNum = parseFloat(tdMdt) || 0;
            // 校验：申请数量不允许大于中间库存
            if (amtNum > mdtNum) {
                layui.layer.msg("上一行“申请数量”不允许大于中间库库存", {icon: 2, time: 3000});
                return false;
            }

            // 校验：必须是0.5的倍数
            var reg = /^\d+(\.5)?$/;
            if (!reg.test(tdAmt)) {
                layui.layer.msg("上一行请输 0.5 的倍数的数字", {icon: 2, time: 3000});
                return false;
            }

            var model_str = "";
            var trLen = $("#bill-table tbody tr").length;
            var number = trLen - 1;
            for (var i = 0; i < number; i++) {
                var tr = $("#bill-table tbody").find("tr").eq(i); //获取每一行tr
                var tdLen = $(tr).find("td").length;
                for (var j = 0; j < tdLen-1; j++) {
                    td = $(tr).find("td input").eq(j); //获取每行td
                    var dir = $(tr).find("input").eq(j).val();
                    model_str = model_str + dir + ";";
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

