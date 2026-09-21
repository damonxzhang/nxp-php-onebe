<?php

use kartik\select2\Select2;
use yii\helpers\Url;
use backend\models\UserInfo;
use backend\models\Facility;
use backend\models\MachineRoster;

?>
<div class = "x-body layui-anim layui-anim-up">
    <form >
        <div id="regular_id" clase="regular_id" style="display:none"><?php $id ?></div>
        <div id="facility_id_list" clase="facility_id_list" style="display:none"><?php $model['facility_id'] ?></div>
        <div class = "layui-form-item">
            <label class = "layui-form-label"><span class = "x-red">*</span>区域:</label>
            <div class = "layui-input-inline" style = "width: 600px;">
                <select name = "area" id = "select-area"  class= "select-area" onchange="select2()" style = "height: 30px;">
                    <?php foreach (UserInfo::$areaArr as $key => $value): ?>
                        <option <?= $key == $model['area'] ? 'selected' : '' ?> value = "<?= $key ?>"><?= $value ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class = "layui-form-item">
            <label class = "layui-form-label"><span class = "x-red">*</span>工序:</label>
            <div class = "layui-input-inline" style = "width: 600px;">
                <select name = "process" id = "select-process"  class = "select-process" onchange="select2()" style = "height: 30px;">
                    <?php foreach (Facility::$processList as $key => $value): ?>
                        <option <?= $key == $model['process'] ? 'selected' : '' ?> value = "<?= $key ?>"><?= $value ?></option>
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
                <input class="layui-input" value="<?php echo $model->start_time ?>" placeholder="起始时间" lay-verify="required" id="start_time" name="start_time"  autocomplete="off">
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
            data: {area: selectAreaId,process: selectProcess,list: '' },
            dataType: 'json',
            url: '<?= Url::to([$this->context->id . '/get-facility'])?>',
            success: function (res) {
                $('#downList').find("option:selected").attr("selected", false);

                if (res.status == 200) {
                    console.log('changeArea - 200');

                    var list = res.data[0].id;

                    var option = '<option>请选择类型</option>';

                    for (var i=0; i< res.data.length ; i++){
                        option += "<option value='" + res.data[i].id + "'>" + res.data[i].text + "</option>";
                        // console.log(res.data[i].id,res.data[i].number);
                    }

                    ele.html(option);
                }
                else {
                    console.log('changeArea - 400');

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



</script>

<?php
$importUrl = Url::to(["machine-regular/update"]);
$getFacilityUrl = Url::to(["machine-regular/get-facility"]);
$js        = <<<JS
         layui.laydate.render({
            elem: '#start_time'
         });

function init(ele,list_input){
        var selectProcess = $("#select-process option:selected").val();
        var selectAreaId = $("#select-area option:selected").val();
        
        // 获取下拉列表
        $.ajax({
            type: 'GET',
            data: {area: selectAreaId,process: selectProcess },
            dataType: 'json',
            url: '{$getFacilityUrl}',
            success: function (res) {
                if (res.status == 200) {
                    console.log('init - 200')
                    // var list = res.data[0].id;
                    // var option = '<option>请选择类型</option>';
                    // for (var i=0; i< res.data.length ; i++){
                    //     option += "<option value='" + res.data[i].id + "'>" + res.data[i].text + "</option>";
                    // }
                    // ele.html(option);
                    ele.select2({
                        data: res.data,
                        placeholder: "请选择"
                    });
                }
                else {
                    ele.empty();
                }
            }
        });
        
        //获取默认内容
        $.ajax({
            type: 'GET',
            data: {area: '',process: '',list:list_input },
            dataType: 'json',
            url: '{$getFacilityUrl}',
            success: function (res) {
                if (res.status == 200) {
                    console.log('list - 200');
                    var data_list = [];
                    for (var i=0; i< res.data.length ; i++){
                        data_list[i] = res.data[i].id ;
                    }
                    ele.val(data_list).trigger('change');
                    console.log(data_list);
                    console.log(ele.val());
                }
                else {                    
                    console.log('list - 400');
                    ele.empty();
                }
            }
        });
    }
    

    $(document).ready(function(){
        $(".downList").select2({

            placeholder:"请选择",
            language: "zh-CN",
            tags: true,//允许手动添加
            // allowClear: true,//允许清空
            escapeMarkup: function (markup) { return markup; }, // 自定义格式化防止xss注入
            // minimumInputLength: 1,//最少输入多少个字符后开始查询
            formatResult: function formatRepo(repo){return repo.text;}, // 函数用来渲染结果
            formatSelection: function formatRepoSelection(repo){return repo.text;}, // 函数用于呈现当前的选择
            templateSelection:  init($('.downList'),{$model->facility_id})
        });
        
        $(".week_id-select").val({$model->week}).trigger('change');

        
    })


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
                 data.field.regular_id = {$model->id};
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

