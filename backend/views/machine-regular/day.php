<?php

use kartik\select2\Select2;
use yii\helpers\Url;
use backend\models\UserInfo;
use backend\models\Facility;
use backend\models\MachineRoster;

?>
<div class = "x-body layui-anim layui-anim-up">
    <form >
         <div class = "layui-form-item">
            <label class = "layui-form-label"><span class = "x-red">*</span>区域:</label>
            <div class = "layui-input-inline" style = "width: 600px;">
                <select name = "area" id = "select-area"  class= "select-area" onchange="select2()" style = "height: 30px;">
                    <?php foreach (UserInfo::$areaArr as $key => $value): ?>
                        <option value = "<?= $key ?>"><?= $value ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class = "layui-form-item">
            <label class = "layui-form-label"><span class = "x-red">*</span>工序:</label>
            <div class = "layui-input-inline" style = "width: 600px;">
                <select name = "process" id = "select-process"  class = "select-process" onchange="select2()" style = "height: 30px;">
                    <?php foreach (Facility::$processList as $key => $value): ?>
                        <option value = "<?= $key ?>"><?= $value ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class = "layui-form-item">
            <label class = "layui-form-label"><span class = "x-red">*</span>星期:</label>
            <div class = "layui-input-inline" style = "width: 600px;">
                <?php echo Select2::widget([
                    'name'    => 'week_id',
                    'data'    => MachineRoster::$weekList,
                    'options' => ['multiple' => true, 'class' => 'week_id-select', 'placeholder' => '星期']
                ]); ?>
            </div>
        </div>
        <div class = "layui-form-item">
            <label class = "layui-form-label"><span class = "x-red">*</span>设备:</label>
            <select class="downList" id="downList" multiple="multiple" style = "width: 600px;height: 30px;"></select>
        </div>
        <div class = "layui-form-item">
            <label class = "layui-form-label">
                <span class = "x-red">*</span>白夜班:
            </label>
            <div>
                <label style="width: 50px;"><input type="radio" value="1" name="status" id="choose-class" lay-skin="primary" checked="">白班</label>
                <label style="width: 50px;"><input type="radio" value="2" name="status" id="choose-class" lay-skin="warning">夜班</label>
            </div>
        </div>
        <div class="layui-form-item  count">
            <label for="tel" class="layui-form-label">
                <span class="x-red">*</span>起始时间:
            </label>
            <div class="layui-input-inline">
                <input class="layui-input" placeholder="起始时间" lay-verify="required" id="start_time" name="start_time"  autocomplete="off">
            </div>
        </div>
        <div class = "layui-form-item">
            <label for = "" class = "layui-form-label"></label>
            <button class = "layui-btn" lay-filter = "update-day" lay-submit = "">
                保存
            </button>
        </div>
    </form>
</div>
<script>
    function changeArea(ele) {
        var selectProcess = $("#select-process option:selected").val();
        var selectAreaId = $("#select-area option:selected").val();
        $.ajax({
            type: 'GET',
            data: {area: selectAreaId,process: selectProcess },
            dataType: 'json',
            url: '<?= Url::to([$this->context->id . '/get-facility'])?>',
            success: function (res) {
                $('#downList').find("option:selected").attr("selected", false);

                if (res.status == 200) {
                    console.log(200);

                    var list = res.data[0].id;

                    var option = '<option>请选择类型</option>';

                    for (var i=0; i< res.data.length ; i++){
                        option += "<option value='" + res.data[i].id + "'>" + res.data[i].text + "</option>";
                        // console.log(res.data[i].id,res.data[i].number);
                    }

                    ele.html(option);
                }
                else {
                    console.log(400);

                    ele.empty();
                }
            }
        });
    }

    function select2(){

        $("#downList").select2({

            placeholder:"请选择",
            language: "zh-CN",
            tags: true,//允许手动添加
            // allowClear: true,//允许清空
            escapeMarkup: function (markup) { return markup; }, // 自定义格式化防止xss注入
            // minimumInputLength: 1,//最少输入多少个字符后开始查询
            formatResult: function formatRepo(repo){return repo.text;}, // 函数用来渲染结果
            formatSelection: function formatRepoSelection(repo){return repo.text;}, // 函数用于呈现当前的选择
            templateSelection:  changeArea($('.downList'))
        });
    }

    $(document).ready(function(){
        $("#downList").select2({

            placeholder:"请选择",
            language: "zh-CN",
            tags: true,//允许手动添加
            // allowClear: true,//允许清空
            escapeMarkup: function (markup) { return markup; }, // 自定义格式化防止xss注入
            // minimumInputLength: 1,//最少输入多少个字符后开始查询
            formatResult: function formatRepo(repo){return repo.text;}, // 函数用来渲染结果
            formatSelection: function formatRepoSelection(repo){return repo.text;}, // 函数用于呈现当前的选择
            templateSelection:  changeArea($('.downList'))
        });
    })


</script>

<?php
$importUrl = Url::to(["machine-regular/day"]);
$js        = <<<JS
         layui.laydate.render({
            elem: '#start_time'
         });

        layui.use(['laydate', 'form', 'layer'], function () {
            $(this).removeAttr("lay-key");
            var form = layui.form, layer = layui.layer;
            //监听提交
            form.on('submit(update-day)', function (data) {
                var selectProcess = $("#select-process option:selected").val();
                var selectAreaId = $("#select-area option:selected").val();
                
                 layer.load(3);
                 delete data.field["facility_id[0]"];
                 data.field.facility_id = $('.downList').select2('val');
                 delete data.field["week_id[0]"];
                 data.field.week_id = $('.week_id-select').select2('val');
                 data.field.area = selectAreaId;
                 data.field.process = selectProcess;
                 data.field.class = $('#choose-class').val();
                 data.field.start_time = $('#start_time').val();
                 console.log(data);
                $.post('{$importUrl}', data.field, function (res) {
                    layer.closeAll();
                    if (res.status === 200) {
                        layer.msg(res.msg, {icon: 1, time: 1500}, function () {
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
        $(".select2").next().remove(); //去掉select2 的样式问题
JS;
$this->registerJs($js);
?>

