<?php

use backend\models\UserInfo;
$baseUrl = Yii::$app->params['base_url'];
?>

<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">培训视频</a>
        <a href="javascript:;">视频列表</a>
        <a href="javascript:;">
          <cite>视频详情</cite>
        </a>
      </span>
</div>
<div class="x-body layui-anim layui-anim-up">
    <form class="layui-form">
        <div class="layui-form-item">
            <label for="nickname" class="layui-form-label">
                视频名称:
            </label>
            <div class="layui-input-inline">
                <input type="text" autocomplete="off" value="<?= $model['name'] ?>" readonly="readonly" class="layui-input" style="width: 800px;">
            </div>
        </div>
        <div class="layui-form-item">
            <label for="nickname" class="layui-form-label">
                视频类型:
            </label>
            <div class="layui-input-inline">
                <input type="text" autocomplete="off" value="<?=\backend\models\TrainFileType::getTypeName($model['type']);  ?>" readonly="readonly" class="layui-input" style="width: 800px;">

            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                发起人:
            </label>
            <div class="layui-input-inline">
                <input type="text" autocomplete="off" value="<?= (new UserInfo())->getUserName($model['create_user']) ?>" readonly="readonly" class="layui-input" style="width: 800px;">
            </div>
        </div>

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
                    $file_path = json_decode($model->video_path,1);
                    if(is_array($file_path) && count($file_path) > 0):
                    foreach ($file_path as $list):
                        ?>
                        <tr>
                            <td><?= substr($list,strrpos($list,'/') +1)?></td>
                            <td></td>
                            <td>
                                <a type="button" onclick='palyer_video("<?=$list?>",this)' class="layui-btn layui-btn-normal" ><i class="layui-icon">&#xe652;</i>播放视频</a>
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
