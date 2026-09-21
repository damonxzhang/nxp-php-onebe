<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use backend\models\TrainFileType;
/* @var $this yii\web\View */
/* @var $model backend\models\TrainPpt */
/* @var $form yii\widgets\ActiveForm */
?>
<style>
    .upload-type-1{
        display: none;
    }
</style>
<div class="reain-ppt-form">

    <?php $form = \backend\widgets\LayuiActiveForm::begin(['id' => 'ppt_form']); ?>

    <?= $form->field($model, 'name',['required' => true])->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type',['required' => true])->dropDownList(ArrayHelper::merge(['' => '请选择类型'],TrainFileType::getTypes(TrainFileType::TYPE_PPT))) ?>

    <?= $form->field($model, 'status')->dropDownList( \backend\models\TrainPpt::$statusArr) ?>

    <?= $form->field($model, 'comment')->textInput(['maxlength' => true]) ?>

    <div class="form-group field-trainvideo-name required">
        <div class="layui-form-item">
            <label for="tel" class="layui-form-label">
                <label class="control-label" for="trainvideo-name">上传方式</label>:
            </label>
            <div class="layui-input-inline" style="width: 800px;">
                <select lay-filter="upload-type">
                    <option value="1">文件上传</option>
                    <option value="2">名称输入</option>
                </select>
            <div class="layui-col-md4" style="color:red"><div class="help-block"></div></div>
            </div>
        </div>
    </div>
    <div class="form-group field-trainvideo-name required upload-type-1">
        <div class="layui-form-item">
            <label for="tel" class="layui-form-label">
                <label class="control-label" for="trainvideo-name">文件名称</label>:
            </label>
            <div class="layui-input-inline" style="width: 800px;">
                <input type="text" class="layui-input" name="file_path" placeholder="请输入文件名称,多个文件可用逗号隔开" aria-required="true"></div>
            <div class="layui-col-md4" style="color:red"><div class="help-block"></div></div>
        </div>
    </div>

    <div class="layui-upload upload-type-2" style="margin-left: 110px;">
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
                <tbody id="demoList">
                <?php
                $paths = json_decode($model->path,1);
                if(is_array($paths) && count($paths) > 0):
                    foreach ($paths as $val):
                        ?>
                        <tr class="ppt-paths">
                            <td><?= substr($val,strrpos($val,'/') + 1) ?></td>
                            <td><span style="color: #5FB878;">上传成功</span></td>
                            <td>
                                <button class="layui-btn layui-btn-xs demo-reload layui-hide">重传</button>
                                <a class="layui-btn layui-btn-xs delete-ppt">删除</a><input type="hidden" name="upload_file[]" value="<?=$val?>">
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
        <label for="" class="layui-form-label">
        </label>
        <?= Html::button('保存', ['class' => 'layui-btn submit']) ?>
    </div>
    <?php \backend\widgets\LayuiActiveForm::end(); ?>

</div>
<?php
$js = <<<JS
    layui.use('layer', function(){
        var layer = layui.layer;
                  
        $('.delete-ppt').click(function() {
            $(this).parents('.ppt-paths').remove();
        });
        $('.submit').click(function() {
            var form = $('#ppt_form').serialize();
            layer.load(3);
            $.post('',form,function(res) { 
                layer.closeAll();
                if(res.status == 200){
                    location.href="/train-ppt/index"
                }else{
                    layer.closeAll();

                    layer.msg(res.msg);
                }
            },'json');
        });
        var form = layui.form;
        form.on('select(upload-type)',function(data){
            var type = data.value;
             console.log(type);
            if(type == 1){
                $('.upload-type-2').show();
                $('.upload-type-1').hide();
            }else if( type == 2 ){
                $('.upload-type-1').show();
                $('.upload-type-2').hide();
            }
        });
        
    }); 
    
    

JS;
$this->registerJs($js);
?>