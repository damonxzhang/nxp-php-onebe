<?php

use backend\models\UserInfo;
$baseUrl = Yii::$app->params['base_url'];
?>

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
                                <td><?=\common\models\Upload::getFileSize($list) ?></td>
                                <td>
                                    <a href="<?= $list?>" class="layui-btn"><i class="layui-icon">&#xe601;</i>下载</a>

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
                            ?>
                            <tr>
                                <td><?= substr($list,strrpos($list,'/') +1)?></td>
                                <td><?=  \common\models\Upload::getFileSize($list)?></td>
                                <td>
                                    <a type="button" onclick='palyer_video("<?=$list?>")' class="layui-btn layui-btn-normal" ><i class="layui-icon">&#xe652;</i>播放视频</a>
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
</div>
<?php $this->beginBlock('footer') ?>
<script>
</script>
<?php $this->endBlock() ?>
