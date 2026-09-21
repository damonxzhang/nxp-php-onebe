<?php

use yii\helpers\Html;
use backend\widgets\LayuiActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\TrainCourses */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="reain-courses-form">

    <?php $form = LayuiActiveForm::begin(); ?>

    <?= $form->field($model, 'name',['required' => true])->textInput(['maxlength' => true, 'placeholder'=>"输入课程名称"]) ?>

    <?= $form->field($model, 'type',['required' => true])->dropDownList( [''=>'请选择类型'] +  (new \backend\models\TrainCoursesType())->typeDropDownList()) ?>

    <?= $form->field($model, 'duration',['required' => true])->textInput(['placeholder'=>"输入课程时长,单位：分钟"]) ?>

    <?= $form->field($model, 'effect',['required' => true])->textarea(['rows' => 6,'class' =>'layui-textarea']) ?>

    <?= $form->field($model, 'comment',['required' => true])->textarea(['rows' => 6,'class' =>'layui-textarea']) ?>

    <div class="form-group">
        <div class="layui-form-item">
            <label for="tel" class="layui-form-label"> <label class="control-label" for="traincourses-name"></label></label>
            <div class="layui-input-inline" style="width: 400px;">
                <input type="text" id="ppt-name" class="layui-input" maxlength="255" aria-required="true">
            </div>
            <a href="javascript:;" class="layui-btn search-ppt" lay-submit='' lay-filter="search-video">搜索</a>
            <div class="layui-col-md4" style="color:red"><div class="help-block"></div></div>
        </div>
    </div>
    <div class="form-group layui-form-item">
        <label for="" class="layui-form-label">课件 </label>
        <div class="layui-upload" style="margin-left: 110px;margin-bottom: 15px;">

            <div class="layui-upload-list" >

                <table class="layui-table"  id="ppt_table" lay-filter="vedio_table" style="width: 800px ">

                </table>

            </div>
        </div>
    </div>

    <div class="form-group" style="padding-top:50px;">
        <div class="layui-form-item">
            <label for="tel" class="layui-form-label"> <label class="control-label" for="traincourses-name"></label></label>
            <div class="layui-input-inline" style="width: 400px;">
                <input type="text" id="video-name" class="layui-input" maxlength="255" aria-required="true">
            </div>
            <a href="javascript:;" class="layui-btn search-video" lay-submit='' lay-filter="search-video">搜索</a>
            <div class="layui-col-md4" style="color:red"><div class="help-block"></div></div>
        </div>
    </div>
    <div class="form-group layui-form-item">

        <label for="" class="layui-form-label">视频 </label>
        <div class="layui-upload" style="margin-left: 110px;">

            <div class="layui-upload-list" >

                <table class="layui-table"  id="video_table" lay-filter="video_table" style="width: 800px ">

                </table>

            </div>
        </div>
    </div>
    <div class="layui-form-item"  style="margin-top: 50px;">
        <label for="" class="layui-form-label">
        </label>
        <?= Html::button('保存', ['class' => 'layui-btn ',' lay-submit' => '','lay-filter'=>"add"]) ?>
    </div>

    <?php LayuiActiveForm::end(); ?>
</div>

<?php

$js = <<<JS
    function getData(url){
        var resultData; 
        $.ajax({
            url:url, 
            type:'get',
            dataType:'json',
            async:false,
            success:function(res){ 
                if(res.code == 0){
                   resultData = res.data 
                }else{
                    layer.msg('服务器错误！');return;
                }
            }
        });  
        return resultData;
    }

    layui.use('layer',function() {
        var video_data = getData('/train-video/search-videos?id={$model->video}');
        var table = layui.table; 
            table.render({
            elem: '#video_table'
            ,height: 212 
            ,width:600 
                ,limit: Number.MAX_VALUE
            ,cols: [[ //表头
                {field: 'id',type:'checkbox', title: 'ID', width:80, sort: true, fixed: 'left'}
                ,{field: 'name', title: '视频名称 ', width:516} 
            ]],
            data:video_data
        });  
        
        var ppt_data = getData('/train-ppt/search-ppt?id={$model->ppt}');
        table.render({
            elem: '#ppt_table'
            ,height: 212 
            ,width:600 
                ,limit: Number.MAX_VALUE
            ,cols: [[ //表头
                {field: 'id',type:'checkbox', title: 'ID', width:80, sort: true, fixed: 'left'}
                ,{field: 'name', title: '课件名称 ', width:516} 
            ]],
            data:ppt_data
        }); 
        
        $('.search-ppt').click(function() {
            var checkStatus = table.checkStatus('ppt_table'); //idTest 即为基础参数 id 对应的值
 
            var data = checkStatus.data; 
            var ids = '';
            if($.isArray(data)){
                $.each(data,function(i,v){
                    console.log(v);
                    ids += ','+v.id  ;
                })
            }  
            var name = $('#ppt-name').val();
            var url = '/train-ppt/search-ppt?id='+ids;
            if(name){
                url = '/train-ppt/search-ppt?id='+ids+'&name='+name;
            }
            data = getData(url);
             
            table.render({
                elem: '#ppt_table'
                ,height: 212 
                ,width:600  
                ,limit: Number.MAX_VALUE
                ,cols: [[ //表头 
                    {field: 'id',type:'checkbox', title: 'ID', width:80, sort: true, fixed: 'left'}
                    ,{field: 'name', title: '课件名称 ', width:516} 
                ]]
                ,data:data
            }); 
        }); 
        
        $('.search-video').click(function() {
            var checkStatus = table.checkStatus('video_table'); //idTest 即为基础参数 id 对应的值
 
            var data = checkStatus.data; 
            var ids = '';
            if($.isArray(data)){
                $.each(data,function(i,v){
                    console.log(v);
                    ids += ','+v.id  ;
                })
            }  
            var name = $('#video-name').val();
            var url = '/train-video/search-videos?id='+ids;
            if(name){
                url = '/train-video/search-videos?id='+ids+'&name='+name;
            }
            data = getData(url); 
            table.render({
                elem: '#video_table'
                ,height: 212 
                ,width:600  
                ,limit: Number.MAX_VALUE
                ,cols: [[ //表头
                      {field: 'id',type:'checkbox', title: 'ID', width:80, sort: true, fixed: 'left'}
                      ,{field: 'name', title: '课件名称 ', width:516} 
                ]]
                ,data:data
            }); 
        });
    });
    layui.use('form', function(){
        var form = layui.form;
        var table = layui.table;
        function getSelectedId(id){
            var data = table.checkStatus(id).data;
            if(data){
                var ids = [];
                $.each(data,function(i,v) { 
                    ids[i] = v.id; 
                });
                return ids;
            }
            return false;
        }
        form.on('submit(add)', function(data){ 
            var data = data.field;
             
            data['TrainCourses[ppt]']   = getSelectedId('ppt_table');  
            data['TrainCourses[video]'] = getSelectedId('video_table'); 
            
            layer.load(3);
            $.post('',data,function(res) {
                layer.closeAll();
                if(res.status == 200){
                    location.href='index';
                }else{
                    layer.closeAll(); 
                    layer.msg(res.msg)
                }
            },'json');
        });
        
    
    });
JS;

$this->registerJs($js);
?>
