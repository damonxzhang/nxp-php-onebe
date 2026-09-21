<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\TrainPpt */


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
                课件名称:
            </label>
            <div class="layui-input-inline">
                <input type="text" autocomplete="off" value="<?= $model['name'] ?>" readonly="readonly" class="layui-input" style="width: 800px;">
            </div>
        </div>
        <div class="layui-form-item">
            <label for="nickname" class="layui-form-label">
                课件类型:
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
                <input type="text" autocomplete="off" value="<?= (new \backend\models\UserInfo())->getUserName($model['create_user']) ?>" readonly="readonly" class="layui-input" style="width: 800px;">
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
                        $file_path = json_decode($model->path ,1);
                        if(is_array($file_path) && count($file_path) > 0):
                        foreach ($file_path as $list):
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
        <div class="layui-form-item">
            <div class="layui-card-body">
                <div class="video" id="video" style="width: 100%;"></div>
            </div>
        </div>
    </form>
</div>
