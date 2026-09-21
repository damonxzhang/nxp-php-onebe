<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use backend\models\Blog;
use backend\models\UserInfo;

?>
<style>

    #material .layui-input-inline{
        width:10%;
    }
</style>
<div class="x-body layui-anim layui-anim-up">

<form class="layui-form">
    <input type="hidden" name ="draft_id" value="<?= $id ?>" >
    <div class="layui-form-item">
        <label for="nickname" class="layui-form-label">
            日志标题:
        </label>
        <div class="layui-input-inline" style="width: 600px;">
            <input type="text" name="title" value="<?= $model['title'] ?>" autocomplete="off" lay-verify="required" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label for="tel" class="layui-form-label">
            发布人:
        </label>
        <div class="layui-input-inline" style="width: 600px;">
            <input type="text" name="user_id" value="<?php echo (new UserInfo())->getUserName($model['user_id']) ?>"  autocomplete="off" class="layui-input" readonly="readonly">
        </div>
    </div>

    <?= \backend\widgets\SelectSearch::widget([
        'name' => 'handover',
        'text' => '用户',
        'columnName' => 'handover_id',
        'value' => $acceptUserArr,
        'required' => true,
        'selectWidth' => '600px',
        'options' => [
            'multiple' => true,
        ],
        'selectList' => [
            'section' => [
                'data' =>[$section['id'] => $section['value']]
            ],
            'position' => [
                'text' =>'请选择职务',
                'data'=> UserInfo::$positionArr,
            ],
            'shift' => [
                'text' =>'请选择班别',
                'data'=> UserInfo::$shiftArr,
            ],
            'user-stations' => [
                'text' =>'请选择站别',
                'data'=> ArrayHelper::map((new \backend\models\UserStation())->getStation(),'id','station'),
            ],
        ]
    ])?>
    <div class="layui-form-item" style="margin-top: 10px">
        <label class="layui-form-label">
            <span class="x-red">*</span>交接类型:
        </label>
        <div class="layui-input-inline" style="width: 600px;">
            <?php if(empty($model->type)): ?>
                <select name="type" lay-verify="required" id="test1"  lay-filter="type" >
                    <?php foreach (Blog::$typeList as $key => $value): ?>
                        <option value="<?= $key ?>"><?= $value ?></option>
                    <?php endforeach; ?>
                </select>

            <?php else: ?>
                <select name="type" >
                    <option value="<?= $model->type ?>"><?= $type[$model->type] ?></option>
                </select>
            <?php endif; ?>
        </div>
    </div>
    <span id="material" >

           <?php
           if(!empty($blogMaterial)):
               foreach ($blogMaterial as $key => $materialModel):
                   ?>
                   <div class="layui-form-item ">
                        <?php if($model->type == 2): ?>
                            <label class="layui-form-label" >
                                <span class="x-red">*</span>设备编号:
                            </label>
                            <div class="layui-input-inline" >
                                <input type="text" name="device_number[<?=$key?>]" value="<?=$materialModel->device_number?>" autocomplete="off" class="layui-input" >
                            </div>
                        <?php endif; ?>
                            <label class="layui-form-label" >
                                <?php if($model->type == 1){ echo '<span class="x-red">*</span>';}?>
                                物料批号:
                            </label>
                            <div class="layui-input-inline" >
                                <input type="text" name="matter_number[<?=$key?>]" value="<?=$materialModel->matter_number?>" autocomplete="off" class="layui-input" >
                            </div>
                            <label class="layui-form-label" >
                                位置号:
                            </label>
                            <div class="layui-input-inline" >
                                <input type="text" name="address[<?=$key?>]" value="<?=$materialModel->address?>"  autocomplete="off" class="layui-input"     >
                            </div>
                            <input type="hidden" name="material_id[<?=$key?>]" value="<?=$materialModel->id?>">
                        <?php if($key == 0):?>
                            <div class="layui-input-inline"  style="width:10%" >
                                <a href="javascript:;" class="layui-btn layui-btn-warm  add-row" data-type="<?=$model->type?>" >增加</a>
                            </div>
                        <?php endif;?>
                    </div>

               <?php
               endforeach;
           else:
           ?>
                <div class="layui-form-item ">
                    <?php if(!empty($model->type) && $model->type == 2): ?>
                        <label class="layui-form-label" >
                            <span class="x-red">*</span>设备编号:
                        </label>
                        <div class="layui-input-inline" >
                            <input type="text" name="device_number[0]"  autocomplete="off" class="layui-input" >
                        </div>
                    <?php endif; ?>
                        <label class="layui-form-label" >
                            <?php if(!empty($model->type) && $model->type == 1){ echo '<span class="x-red">*</span>';}?>
                            物料批号:
                        </label>
                    <div class="layui-input-inline" >
                        <input type="text" name="matter_number[0]" autocomplete="off" class="layui-input" >
                    </div>
                    <label class="layui-form-label" >
                        位置号:
                    </label>
                    <div class="layui-input-inline" >
                        <input type="text" name="address[0]" autocomplete="off" class="layui-input"     >
                    </div>

                    <div class="layui-input-inline"  style="width:10%" >
                        <a href="javascript:;" class="layui-btn layui-btn-warm  add-row" data-type="<?= !empty($model->type) ? $model->type : 1 ;?> " >增加</a>
                    </div>
                </div>
            <?php endif;?>

    </span>
    <div class="layui-form-item">
        <label class="layui-form-label">
            <span class="x-red">*</span>交接状态:
        </label>
        <div class="layui-input-inline" style="width: 600px;">
            <select name="status" lay-verify="required">
                <?php foreach (Blog::$addStatusArr as $key => $value): ?>
                    <option value="<?= $key ?>" <?= $key == $model['status'] ? 'selected' : '' ?>><?= $value ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label for="tel" class="layui-form-label">
            <span class="x-red">*</span>过期时间:
        </label>
        <div class="layui-input-inline" style="width: 600px;">
            <input class="layui-input" placeholder="过期时间" lay-verify="required" value="<?=$model['alarm_time']?>" name="alarm_time" id="alarmTime" autocomplete="off">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">
            所有人必读:
        </label>
        <div class="layui-input-block">
            <input type="radio" name="read_all" title="不必读" value="0"  <?= $model['read_all'] == 0 ? 'checked' : '' ?> >
            <input type="radio" name="read_all" title="必读" value="1" <?= $model['read_all'] == 1 ? 'checked' : '' ?>>
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label for="desc" class="layui-form-label">
            日志内容:
        </label>
        <div class="layui-input-block">
            <textarea  lay-verify="required" name="content_data" class="layui-textarea" style="width: 600px;height: 300px;"> <?php echo $model['content_data']; ?></textarea>
        </div>
    </div>

    <div class="layui-upload" style="margin-left: 110px;">
        <button type="button" class="layui-btn layui-bg-cyan" id="fileList">请选择文件</button>
        <div class="layui-upload-list">
            <table class="layui-table" style="width: 600px;">
                <thead>
                <tr>
                    <th>文件名</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody id="demoList">
                    <?php if(!empty($blogFile)):?>
                    <?php foreach($blogFile as $value):?>
                            <tr >
                                <td><?=$value['file_name']?></td>
                                <td><span style="color: #5FB878;">上传成功</span></td>
                                <td>
                                    <a class="layui-btn layui-btn-xs delete-file">删除</a>
                                    <input type="hidden" name="upload_file[]" value="<?=$value['file_path']?>">
                                </td>
                            </tr>
                    <?php endforeach;?>
                    <?php endif;?>

                </tbody>
            </table>
        </div>
        <button type="button" onclick="uploadFile(this)" class="layui-btn layui-btn-normal" id="testListAction">上传文件</button>
    </div>
    <div class="layui-form-item"  style="margin-top: 50px;">
        <label for="" class="layui-form-label"></label>
        <button class="layui-btn" lay-filter="create" lay-submit="">保存为日志</button>
        <button class="layui-btn" lay-filter="update" lay-submit="">保存为草稿</button>
    </div>
</form>
</div>

<?php

$createUrl = Url::to(['blog/create']);
$updateUrl = Url::to(['blog/update-draft']);
$rowNum = count($blogMaterial) > 0 ? count($blogMaterial) - 1:count($blogMaterial);

$js = <<<JS
  
   var rowNum = {$rowNum };
    //物料和5s的inout
    function getInputHtml(num,type = false){
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
            '<div class="layui-input-inline" >' +
            '<input type="text" name="address['+num+']"  autocomplete="off" class="layui-input">' +
            '</div>' ;
        return html;
    }
    //设备的input
    function getAdditionalInputHtml(num) {
        var html =  '<label class="layui-form-label">' +
            '<span class="x-red">*</span>设备编号:' +
            '</label>' +
            '<div class="layui-input-inline"    >' +
            '<input type="text" name="device_number['+num+']"  autocomplete="off" class="layui-input" >' +
            '</div>';
        return html += getInputHtml(num);
    }
    //选择类型时更新输入框
    function reloadInput(type) {
        var html = '<div class="layui-form-item ">';
        rowNum = 0;
        if(type == 1 || type > 2){
            html += getInputHtml(rowNum,type);
        }else if(type == 2){
            html += getAdditionalInputHtml(rowNum);
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

        if(type == 1 || type > 2){
            html += getInputHtml(rowNum,type);
        }else if(type == 2){
            html += getAdditionalInputHtml(rowNum);
        }
        html += '<div class="layui-input-inline" >'+
            '<a href="javascript:;" class="layui-btn layui-btn-danger delete-row" data-type="1" >删除</a>'+
            '</div></dib>'; 
        $('#material').append(html);
    });

    $(document).on('click','.delete-row',function(){

        $(this).parent().parent().remove();
    });

  
  layui.use(['form','layer'], function(){
        $ = layui.jquery;
        var form = layui.form ,layer = layui.layer;

        //监听提交
        form.on('submit(update)', function(data){
            layer.load(3);
            $.post('{$updateUrl}', data.field, function (res) {
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
        //监听提交
        form.on('submit(create)', function(data){
            layer.load(3);
            $.post('{$createUrl}', data.field, function (res) {
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
  
        //日期时间
        layui.use('laydate', function() {
            var laydate = layui.laydate;
            $(this).removeAttr("lay-key");
            laydate.render({
                elem: '#alarmTime'
                ,trigger : 'click'
                ,type: 'datetime'
            });
        });
         form.on('select(type)', function(data){
  
            var num = 0; 
            if(data.value == 1){//物料
                num =  4 ;
            }else if(data.value == 2){//设备
                num =  7 ;
            }else if(data.value == 3){//5s
                num = 1  ;
            }
 
            reloadInput(data.value)

        });
    });


    $('.delete-file').click(function () {
        $(this).parents('tr').remove();
    });

   

JS;
$this->registerJs($js);
?>



