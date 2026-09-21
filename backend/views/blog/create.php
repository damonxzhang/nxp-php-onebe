<?php

use backend\models\UserInfo;
use yii\helpers\Url;
use backend\models\Blog;

?>
    <style>
        #material .layui-input-inline{

            width: 10%;
        }
    </style>
    <div class="x-body layui-anim layui-anim-up">
        <form class="layui-form">
            <div class="layui-form-item">
                <label for="nickname" class="layui-form-label">
                    <span class="x-red">*</span>日志标题:
                </label>
                <div class="layui-input-inline">
                    <input type="text" name="title" placeholder="请输入日志标题"  autocomplete="off" lay-verify="required" class="layui-input" style="width: 800px;">
                </div>
            </div>

            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">
                    <span class="x-red">*</span>发布人:
                </label>
                <div class="layui-input-inline">
                    <input type="text" value="<?php echo (new UserInfo()) ->getUserName($uid) ?>" autocomplete="off" class="layui-input"  readonly="readonly" style="width: 800px;">
                </div>
            </div>

            <?= \backend\widgets\SelectSearch::widget([
                'name' => 'handover',
                'text' => '用户',
                'columnName' => 'handover_id',
                'required' => true,
                'selectWidth' => '800px',
                'section' => $section,
                'options' => [
                    'multiple' => true,
                ],
            ])?>
            <div class="layui-form-item">
                <label class="layui-form-label">
                    <span class="x-red">*</span>交接类型:
                </label>
                <div class="layui-input-inline" style="width: 800px;">
                    <select name="type" lay-verify="required" id="test1"  lay-filter="type"   >
                        <?php foreach (Blog::$typeList as $key => $value): ?>
                            <option value="<?= $key ?>"><?= $value ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <span id="material" >

                <div class="layui-form-item">

                    <label class="layui-form-label">
                        <span class="x-red">*</span>物料批号:
                    </label>
                    <div class="layui-input-inline " >
                        <input type="text" name="matter_number[0]"  autocomplete="off" class="layui-input" >
                    </div>

                    <label class="layui-form-label">
                        位置号:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text" name="address[0]"  autocomplete="off" class="layui-input"     >
                    </div>
                    <div class="layui-input-inline" >
                        <a href="javascript:;" class="layui-btn layui-btn-warm add-row" data-type="1" >增加</a>
                    </div>

                </div>

            </span>

            <div class="layui-form-item">
                <label class="layui-form-label">
                    <span class="x-red">*</span>交接状态:
                </label>
                <div class="layui-input-inline" style="width: 800px;">
                    <select name="status" lay-verify="required">
                        <?php foreach (Blog::$addStatusArr as $key => $value): ?>
                            <option value="<?= $key ?>"><?= $value ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">
                    <span class="x-red">*</span>过期时间:
                </label>
                <div class="layui-input-inline" style="width: 800px;">
                    <input class="layui-input" placeholder="过期时间" lay-verify="required" name="alarm_time" id="alarmTime" autocomplete="off">
                </div>
            </div>
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">
                    <span class="x-red">*</span>所有人必读:
                </label>
                <div class="layui-input-block">
                    <input type="radio" name="read_all" value="0" title="不必读" checked>
                    <input type="radio" name="read_all" value="1" title="必读">
                </div>
            </div>
            <div class="layui-form-item layui-form-text">
                <label for="desc" class="layui-form-label">
                    <span class="x-red">*</span>日志内容:
                </label>
                <div class="layui-input-block">
                    <textarea placeholder="请输入日志内容" lay-verify="required" name="content_data" class="layui-textarea" style="width: 800px;height: 300px;"></textarea>
                </div>
            </div>
            <div class="layui-upload" style="margin-left: 110px;">
                <button type="button" class="layui-btn layui-bg-cyan" id="fileList">请选择文件</button>
                <div class="layui-upload-list">
                    <table class="layui-table" style="width: 800px;">
                        <thead>
                        <tr>
                            <th>文件名</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody id="demoList"></tbody>
                    </table>
                </div>
                <button type="button" onclick="uploadFile(this)" class="layui-btn layui-btn-normal" id="testListAction">上传文件</button>
            </div>
            <div class="layui-form-item"  style="margin-top: 50px;">
                <label for="" class="layui-form-label">
                </label>
                <button class="layui-btn" lay-filter="add" lay-submit="">
                    保存为日志
                </button>
                <button class="layui-btn " lay-filter="add_draft" lay-submit="">
                    保存为草稿
                </button>
            </div>
        </form>
    </div>

<?php
$createUrl = Url::to(["blog/create"]);
$draftUrl = Url::to(["blog/create-draft"]);
$js = <<<JS
        var rowNum = 0;
        //物料和5s的inout
        function getInputHtml(num,type){
             
            var html = '<label class="layui-form-label">' ;
            
            if(type == 1){
                html += '<span class="x-red">*</span>';
            }
            html += '物料批号:' +
                '</label>' +
                '<div class="layui-input-inline" >' +
                '<input type="text" name="matter_number['+num+']"  autocomplete="off" class="layui-input" >' +
                '</div>' +
                '<label class="layui-form-label">' +
                '位置号:' +
                '</label>' +
                '<div class="layui-input-inline">' +
                '<input type="text" name="address['+num+']"  autocomplete="off" class="layui-input">' +
                '</div>' ;
            return html;
        }
        //设备的input
        function getAdditionalInputHtml(num) {
            var html =  '<label class="layui-form-label">' +
                '<span class="x-red">*</span>设备编号:' +
                '</label>' +
                '<div class="layui-input-inline">' +
                '<input type="text" name="device_number['+num+']"  autocomplete="off" class="layui-input" >' +
                '</div>';
            return html += getInputHtml(num);
        }
 
        //选择类型时更新输入框
        function reloadInput(type) {
            var html = '<div class="layui-form-item ">';
            rowNum = 0;
            
            if(type == 2){
                html += getAdditionalInputHtml(rowNum);
            }else if(type == 1 || type > 2){
                html += getInputHtml(rowNum,type);
            } 
            
            html += '<div class="layui-input-inline" >' +
                ' <a href="javascript:;" class="layui-btn layui-btn-warm add-row" data-type="'+ type +'">增加</a>' +
                '</div></div>'; 
            $('#material').html(html);
            $('#material').attr('data-type',type);
        }

        $(document).on('click','.add-row',function(){
            rowNum +=1;
             var html = ' <div class="layui-form-item">'; 
            var type = $(this).data('type'); 
             if(type == 2){
                html += getAdditionalInputHtml(rowNum);
            }else if(type == 1 || type > 2){
                html += getInputHtml(rowNum,type);
            } 
            
            html += '<div class="layui-input-inline" >'+
                    '<a href="javascript:;" class="layui-btn layui-btn-danger delete-row" data-type="1" >删除</a>'+
                    '</div></dib>';
            $('#material').append(html);
        });
        $(document).on('click','.delete-row',function(){
    
            $(this).parent().parent().remove();
        });

        function getFutureDate(mo){
            var today=new Date();
            var year=today.getFullYear();
            var month=today.getMonth();
            var date=today.getDate() + mo;
            var hours=today.getHours() ;
            var minutes=today.getMinutes();
            var seconds=today.getSeconds();
            return new Date(year,month,date,hours,minutes,seconds)
        }

        //日期时间
        layui.use('laydate', function() {
            var laydate = layui.laydate;
            $(this).removeAttr("lay-key");

            laydate.render({
                elem: '#alarmTime'
                ,trigger : 'click'
                ,type: 'datetime'
                ,value:getFutureDate(4)
            });
    
            var form = layui.form;
            form.on('select(type)', function(data){
                var num = 0; 
                if(data.value == 1){//物料
                    num =  4 ;
                }else if(data.value == 2){//设备
                    num =  7 ;
                }else if(data.value == 3){//5s
                    num = 1  ;
                }
    
                laydate.render({
                    elem: '#alarmTime'
                    ,type: 'datetime'
                    ,value:getFutureDate(num)
                    ,closeStop: '#test1' //这里代表的意思是：点击 test1 所在元素阻止关闭事件冒泡。如果不设定，则无法弹出控件
                });
                reloadInput(data.value)
    
            });
    

        });
  
        layui.use(['form','layer'], function(){
            $ = layui.jquery;
            var form = layui.form ,layer = layui.layer;

            //监听提交
            form.on('submit(add)', function(data){
                // layer.load(3);
                $.post('{$createUrl}', data.field, function (res) {
                    layer.closeAll();
                    if (res.status === 200) {
                        layer.msg(res.msg, {icon: 1,time: 1500}, function () {
                            var index = parent.layer.getFrameIndex(window.name);
                            parent.layer.close(index);
                            parent.location.reload();
                        })
                    } else {
                        layer.msg(res.msg, {icon: 2, time: 3000})
                    }
                }, 'json');
                return false;
            });
            //监听保存到草稿箱
            form.on('submit(add_draft)', function(data){
                layer.load(3);
                $.post('{$draftUrl}', data.field, function (res) {
                    layer.closeAll();
                    if (res.status === 200) {
                        layer.msg(res.msg, {icon: 1,time: 1500}, function () {
                            var index = parent.layer.getFrameIndex(window.name);
                            parent.layer.close(index);
                            parent.location.reload();
                        })
                    } else {
                        layer.msg(res.msg, {icon: 2, time: 3000})
                    }
                }, 'json');
                return false;
            });
        }); 
JS;
$this->registerJs($js);


 ?>