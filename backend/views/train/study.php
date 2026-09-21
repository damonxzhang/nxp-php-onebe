<?php

use backend\models\UserInfo;
$baseUrl = Yii::$app->params['base_url'];
?>
<style>

    .choose {
        position: relative;
    }
    .input_check {
        position: absolute;
        visibility: hidden;
    }
    .input_check+label {
        display: inline-block;
        width: .8rem;
        height: .8rem;
        border: 1px solid #000000;
        cursor: pointer;
        border-radius: .5rem;
        position: absolute;
        left: -1.2rem;
    }
    .input_check:checked+label:after {
        content: "";
        position: absolute;
        left: 1px;
        bottom: 5px;
        width: 7px;
        height: 2px;
        border: 2px solid #000000;
        border-top-color: transparent;
        border-right-color: transparent;
        -ms-transform: rotate(-60deg);
        -moz-transform: rotate(-60deg);
        -webkit-transform: rotate(-60deg);
        transform: rotate(-45deg);
    }
</style>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">培训考核</a>
        <a href="javascript:;">培训列表</a>
        <a href="javascript:;">
          <cite>培训详情</cite>
        </a>
      </span>
</div>
<div class="x-body layui-anim layui-anim-up">
    <form class="layui-form">
        <div class="layui-form-item">
            <label for="nickname" class="layui-form-label">
                课程名称:
            </label>
            <div class="layui-input-inline">
                <input type="text" autocomplete="off" value="<?= $model->coursesInfo->name ?>" readonly="readonly" class="layui-input" style="width: 800px;">
            </div>
        </div>
        <div class="layui-form-item">
            <label for="nickname" class="layui-form-label">
                课程类型:
            </label>
            <div class="layui-input-inline">
                <input type="text" autocomplete="off" value="<?= \backend\models\TrainCoursesType::typeName($model->coursesInfo->type)  ?>" readonly="readonly" class="layui-input" style="width: 800px;">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                预期收益:
            </label>
            <div class="layui-input-inline">
                <input type="text" autocomplete="off" value="<?= $model->coursesInfo->effect ?>" readonly="readonly" class="layui-input" style="width: 800px;">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                课程内容:
            </label>
            <div class="layui-input-inline">
                <input type="text" autocomplete="off" value="<?= $model['content'] ?>" readonly="readonly" class="layui-input" style="width: 800px;">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                开始时间:
            </label>
            <div class="layui-input-inline">
                <input type="text" autocomplete="off" value="<?= date('Y-m-d',$model->start_time);?>" readonly="readonly" class="layui-input" style="width: 800px;">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                过期时间:
            </label>
            <div class="layui-input-inline">
                <input type="text" autocomplete="off" value="<?=  date('Y-m-d',strtotime(date('Y-m-d',$model->start_time)." + ".($model->end_time + 1) .' day'));?>" readonly="readonly" class="layui-input" style="width: 800px;">
            </div>
        </div>
        <label class="layui-form-label">
            课件:
        </label>
        <div class="layui-upload" style="margin-left: 110px;">
            <div class="layui-upload-list">
                <table class="layui-table" style="width: 800px;">
                    <thead>
                    <tr>
                        <th>文件名</th>
                        <th>文件大小</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody >

                    <?php
                    if(is_array($model->coursesInfo->ppt) && count($model->coursesInfo->ppt) > 0):
                        foreach ($model->coursesInfo->ppt as $list):
                            ?>
                            <tr>
                                <td><?= substr($list,strrpos($list,'/') +1)?></td>
                                <td> </td>
                                <td>
                                    <a href="javascript:;" data-url="<?= $list?>" class="layui-btn download-file"><i class="layui-icon">&#xe601;</i>下载</a>
                                </td>
                            </tr>
                        <?php endforeach;endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <label class="layui-form-label">
            视频:
        </label>
        <div class="layui-upload" style="margin-left: 110px;">
            <div class="layui-upload-list">
                <table class="layui-table" style="width: 800px;">
                    <thead>
                    <tr>
                        <th>文件名</th>
                        <th>文件大小</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody >
                    <?php
                    if(is_array($model->coursesInfo->video) && count($model->coursesInfo->video) > 0):
                        foreach ($model->coursesInfo->video as $list):
                            $name = substr($list,strrpos($list,'/') +1);
                            ?>
                            <tr>
                                <td><?= $name?></td>
                                <td> </td>
                                <td>
                                    <a type="button" data-url="<?=$list?>" class="layui-btn layui-btn-normal palyer_vid" download="<?=$name?>"><i class="layui-icon">&#xe652;</i>播放视频</a>
                                </td>
                            </tr>
                        <?php endforeach;endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-card-body">
                <div class="video" id="video" style="width: 100%;"></div>
            </div>
        </div>
    </form>
    <form class="study">
        <input type="hidden" name="promise" value="<?=$trainUser->study_start_time ?>">
        <div class="layui-form-item" style="width: 800px;">
            <div class="layui-input-block block">
                <?php if($type > \backend\models\TrainUser::STUDY_TYPE):?>

                    <span class="choose"><input type="checkbox" class="input_check" id="check3"><label for="check3"></label></span>
                    我承诺已完成学习内容
                    <?= \yii\helpers\Html::button('保存', ['class' => 'layui-btn submit','style' => 'float: right;']) ?>
                <?php elseif($type == \backend\models\TrainUser::STUDY_TYPE):?>
                    <?= \yii\helpers\Html::button('保存', ['class' => 'layui-btn study-complete','style' => 'float: right;']) ?>
                <?php endif;?>

            </div>

        </div>
    </form>
</div>

<?php

$js =<<<JS
    var promiseObj = $('input[name="promise"]');
    $('.submit').click(function() {
        var check = $('.input_check').prop('checked');
        if(check == false){ 
            layer.msg('请确认已完成学习内容！');
            return false;
        }
        var promise = check? 1 : 0;
        $.ajax({
            url:'save-study',
            dataType:'json',
            data:{id:{$trainUser->id},promise:promise},
            type:'post',
            success:function(res){
                if(res.status == 200){
                    layer.msg('培训成功!', {icon: 1,time: 1500}, function () {
                       window.location.href='index';
                     });
                }else{
                    layer.msg(res.msg);
                }
            }
        });
        
    });
    
    $('.study-complete').click(function(){
        window.location.href='index';
    });
  
    //保存开始学习时间
    function saveStartTime(){
        var result = false;
        
        $.ajax({
            url:'start-study',
            data:{tuId:{$trainUser->id},type:{$type}},
            async:false,
            dataType:'json',
            type:'post',
            success:function(res){
                if(res.status == 200){
                    promiseObj.val(res.data);
                    result = true;
                    
                }else{
                    layer.msg(res.msg);
                }
            }
        }); 
        return result;
    }
    
    $('.palyer_vid').click(function() { 
        if(saveStartTime()){
            var url = $(this).data('url');
            palyer_video(url,$(this)); 
        }
        
    });

    $('.download-file').click(function() {
        if(saveStartTime()){
            var url = $(this).data('url');
            window.location.href=url;
        }

        
    });

JS;
$this->registerJs($js);

?>
