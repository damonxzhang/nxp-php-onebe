<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use backend\models\Blog;
use backend\models\UserInfo;

?>
<style>
    #material label{
        width: auto;padding: 0
    }
    #material .layui-input-inline{
        width:15%;
    }
</style>
<div class="x-body layui-anim layui-anim-up">
    <div class="layui-row">
        <div class="layui-col-xs6">
            <form class="layui-form">
                <input type="hidden" name ="id" value="<?= $id ?>" >
                <input type="hidden" name ="define" value="<?= $define ?>" >
                <div class="layui-form-item">
                    <label for="nickname" class="layui-form-label">
                        日志标题:
                    </label>
                    <div class="layui-input-inline" style="width: 600px;">
                        <input type="text" value="<?= $model['title'] ?>" readonly="readonly" autocomplete="off" lay-verify="required" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="tel" class="layui-form-label">
                        发布人:
                    </label>
                    <div class="layui-input-inline" style="width: 600px;">
                        <input type="text" value="<?php echo (new UserInfo())->getUserName($model['user_id']) ?>"  autocomplete="off" class="layui-input" readonly="readonly">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">
                        是否必读:
                    </label>
                    <div class="layui-input-block">
                        <input type="radio" title="不必读" <?= $model['read_all'] == 0 ? 'checked' : '' ?>>
                        <input type="radio" title="必读" <?= $model['read_all'] == 1 ? 'checked' : '' ?>>
                    </div>
                </div>
                <div class="layui-form-item layui-form-text">
                    <label for="desc" class="layui-form-label">
                        日志内容:
                    </label>
                    <div class="layui-input-block">
                        <textarea  lay-verify="required" readonly="readonly" class="layui-textarea" style="width: 600px;height: 300px;"> <?php echo $model['content_data']; ?></textarea>
                    </div>
                </div>
                <?= \backend\widgets\SelectSearch::widget([
                    'name' => 'handover',
                    'text' => '用户',
                    'columnName' => 'handover_id',
                    'value' => $acceptUserArr ,
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
                    <div class="layui-input-inline" style="width: 600px; ">
                        <?php if(empty($model->type)): ?>
                            <select name="type" lay-verify="required" id="test1"  lay-filter="type" >

                                <?php foreach (Blog::$typeList as $key => $value): ?>
                                    <option value="<?= $key ?>"><?= $value ?></option>
                                <?php endforeach; ?>
                            </select>

                        <?php else: ?>
                            <select name="type" >
                                    <option value="<?= $model->type ?>"><?= Blog::$typeList[$model->type] ?></option>
                            </select>
                        <?php endif; ?>
                    </div>
                </div>
                <span id="material">

                    <?php
                        if(!empty($blogMaterial)):
                            foreach ($blogMaterial as $key => $materialModel):
                        ?>
                            <div class="layui-form-item ">

                                <?php if($model->type == 2): ?>
                                    <label class="layui-form-label" >
                                        <span class="x-red">*</span>设备编号:
                                    </label>
                                    <div class="layui-input-inline"  style="width:15%">
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
                                   <div class="layui-input-inline"  style="width:15%">
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
                                    <a href="javascript:;" class="layui-btn layui-btn-warm  add-row" data-type="<?=!empty($model->type)?$model->type : 1?>" >增加</a>
                                </div>
                            </div>
                    <?php endif;?>

            </span>


                <div class="layui-form-item">
                    <label class="layui-form-label">
                        <span class="x-red">*</span>反馈状态:
                    </label>
                    <div class="layui-input-inline" style="width: 600px;">
                        <select name="status" lay-verify="required">
                            <?php foreach (Blog::$addStatusArr as $key => $value): ?>
                                <option value="<?= $key ?>" <?= $key == $model['status'] ? 'selected' : '' ?>><?= $value ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="layui-form-item layui-form-text">
                    <label for="desc" class="layui-form-label">
                        <span class="x-red">*</span>反馈内容:
                    </label>
                    <div class="layui-input-block">
                        <textarea placeholder="请输入反馈内容" lay-verify="required"  name="reply" class="layui-textarea" style="width: 600px;height: 300px;"></textarea>
                    </div>
                </div>
                <div class="layui-upload" style="margin-left: 110px;">
                    <button type="button" class="layui-btn layui-bg-cyan" id="fileList">请选择文件</button>
                    <div class="layui-upload-list">
                        <table class="layui-table">
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
                    <label for="" class="layui-form-label"></label>
                    <button class="layui-btn" lay-filter="update" lay-submit="">更新</button>
                </div>
            </form>
        </div>
        <div class="layui-col-xs6">
            <?php if (!empty($detailModel)): ?>
                <?php foreach ($detailModel as $value): ?>
                    <hr class="layui-bg-red">
                    <table class="layui-table" lay-size="sm" style="margin-left: 10px;">
                        <tbody>
                        <tr style="color: #000000">
                            <td >反馈人</td>
                            <td><?php echo (new UserInfo())->getUserName($value['user_id']) ?></td>
                            <td>反馈时间</td>
                            <td><?= $value['create_time'] ?></td>
                            <td>反馈状态</td>
                            <td><?= ArrayHelper::getValue(Blog::$statusArr, $value['status'],'') ?></td>
                        </tr>
                        <tr style="color: #000000">
                            <td colspan="3" style="text-align:center;">反馈交接类型</td>
                            <td colspan="3" style="text-align:center;"><?= ArrayHelper::getValue(Blog::$typeList, $value['type'],'') ?></td>
                        </tr>
                        <tr style="color: #000000">
                            <td colspan="6" style="text-align:center;">反馈内容</td>
                        </tr>
                        <tr style="color: #000000">
                            <td colspan="6">
                                <textarea  lay-verify="required" readonly="readonly" class="layui-textarea" style="height: 300px;"> <?php echo $value['reply']; ?></textarea>
                            </td>
                        </tr>
                        <tr style="color: #000000">
                            <td colspan="3" style="text-align:center;">是否有文件</td>
                            <td colspan="3" style="text-align:center;">
                                <?php if (empty($blogFile)): ?>
                                    <button type="button" class="layui-btn layui-bg-red">无上传文件</button>
                                <?php else: ?>
                                    <button type="button" class="layui-btn layui-bg">有上传文件</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</div>
<?php
$updateUrl = Url::to(['blog/update']);
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
              
                reloadInput(data.value)
    
            });
        });
JS;
$this->registerJs($js);
?>

