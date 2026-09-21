<?php

use backend\models\UserInfo;
use yii\helpers\Url;

?>

    <div class="x-body layui-anim layui-anim-up">
        <form class="layui-form">
            <div class="layui-form-item">
                <label for="nickname" class="layui-form-label">
                    通知标题:
                </label>
                <div class="layui-input-inline">
                    <input type="text" name="title" placeholder="请输入通知标题"  autocomplete="off" lay-verify="required" class="layui-input" style="width: 800px;">
                </div>
            </div>
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">
                    发布人:
                </label>
                <div class="layui-input-inline">
                    <input type="text" value="<?php echo (new UserInfo()) ->getUserName($uid) ?>" autocomplete="off" class="layui-input"  readonly="readonly" style="width: 800px;">
                </div>
            </div>

            <div class="layui-form-item">
                <label for="pwd" class="layui-form-label">
                    <span class="x-red">*</span> 通知人筛选:
                </label>
                <div class="layui-col-md1" id="section">
                    <select class="layui-col-md1" >
                        <option value="<?=$section['id']?>"><?=$section['value']?></option>
                    </select>

                </div>

                <div class="layui-col-md1" id="position" >
                    <select class="layui-col-md1" >
                        <option value="">请选择职务</option>

                        <?php foreach (UserInfo::$positionArr as $key =>  $value):?>
                            <option value="<?=$key?>"><?=$value?></option>
                        <?php endforeach;?>
                    </select>

                </div>

                <div class="layui-col-md1" id="shift">
                    <select class="layui-col-md1" >
                        <option value="">请选择班别</option>
                        <?php foreach (UserInfo::$shiftArr as $key => $value):?>
                            <option value="<?=$key?>"><?=$value?></option>
                        <?php endforeach;?>
                    </select>

                </div>

                <div class="layui-col-md1" id="userStations">
                    <select class="layui-col-md1" ">
                    <option value="">请选择站别</option>
                    <?php foreach ($userStations as $value):?>
                        <option value="<?=$value['id']?>"><?=$value['station']?></option>
                    <?php endforeach;?>
                    </select>

                </div>
                <input type="button" value="重置条件" style="margin-left: 10px;" class="layui-col-md1 layui-btn layui-btn-primary reload-key">

                <input type="button" value="搜索"   class="layui-col-md1 layui-btn layui-btn-primary search-user">

            </div>


            <div class="layui-form-item">
                <label for="pwd" class="layui-form-label">
                    通知人:
                </label>
                <div class="layui-input-inline" style="width: 800px;">
                    <select name="user_id" id="connectUser" lay-verify="required" xm-select="selectId" xm-select-search="">
                        <option value="">直接选择或搜索选择</option>
                        <?php foreach ($connectUser as $value): ?>
                            <option value="<?= $value['id'] ?>" ><?= $value['chinese_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">
                    报警时间:
                </label>
                <div class="layui-input-inline">
                    <input type="text" name="alarm_time" value="<?= date('Y-m-d', strtotime('+3 month')) ?>" readonly="readonly"  class="layui-input" style="width: 800px;">
                </div>
            </div>
            <div class="layui-form-item layui-form-text">
                <label for="desc" class="layui-form-label">
                    通知内容:
                </label>
                <div class="layui-input-block">
                    <textarea placeholder="请输入通知内容" lay-verify="required" name="content_data" class="layui-textarea" style="width: 800px;height: 300px;"></textarea>
                </div>
            </div>
            <div class="layui-form-item">
                <label for="" class="layui-form-label">
                </label>
                <button class="layui-btn" lay-filter="add" lay-submit="">
                    保存
                </button>
            </div>
        </form>
    </div>

<?php $this->beginBlock('footer') ?>
    <script>
        //人员搜索
        var formSelects = layui.formSelects;
        formSelects.render('selectId');

        layui.use(['form','layer'], function(){
            $ = layui.jquery;
            var form = layui.form ,layer = layui.layer;

            //监听提交
            form.on('submit(add)', function(data){
                layer.load(3);
                $.post('<?= Url::to(['notice/create']) ?>', data.field, function (res) {
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
    <script>
        var position = $('#position');
        var shift = $('#shift');
        var userStations = $('#userStations');

        //重新渲染select下拉框
        function showSearchResult(result,selectData){

            var content = '<option value="">请选择章节</option>';

            $.each(result, function (i, val) {
                content += '<option value="' + val.id + '">' + val.chinese_name + '</option>';
            });

            if(selectData){
                console.log(selectData);
                var selectVal = Array();
                $.each(selectData, function (i, val) {
                    selectVal.push(val.value);
                    content += '<option value="' + val.value + '">' + val.name + '</option>';
                });
            }
            console.log(content);
            var s = $('#connectUser');
            s.html(content);

            formSelects.render('selectId');
            formSelects.value('selectId',selectVal)
        }


        $(function(){
            //重置搜索
            $('.reload-key').click(function(){
                var select = "dd[lay-value='']";

                position.find('dl').find(select).click();
                shift.find('dl').find(select).click();
                userStations.find('dl').find(select).click();

            });
            //搜索
            $('.search-user').click(function () {
                var selectData =  formSelects.value('selectId','all');
                var arr = {
                    section:$('#section'),
                    position:position,
                    shift:shift,
                    area:userStations
                };
                $.each(arr,function(i,v){
                    arr[i] = v.find('dl').find('.layui-this').attr('lay-value');
                });

                $.post({
                    url:"<?= Url::to(['user-info/search-user']) ?>",
                    data:arr,
                    dataType:'json',
                    success:function(res){
                        if(res.status == 200){
                            showSearchResult(res.data,selectData)

                            layer.msg('搜索成功')

                        }else{

                            layer.msg(res.msg)
                        }
                    }
                })

            });
        })

    </script>
<?php $this->endBlock() ?>