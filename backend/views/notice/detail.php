<?php

use backend\models\UserInfo;
?>
<div class="x-body layui-anim layui-anim-up">
    <form class="layui-form">
        <div class="layui-form-item">
            <label for="nickname" class="layui-form-label">
                通知标题:
            </label>
            <div class="layui-input-inline">
                <input type="text" autocomplete="off" value="<?= $list['title'] ?>" readonly="readonly" class="layui-input" style="width: 800px;">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                发起人:
            </label>
            <div class="layui-input-inline">
                <input type="text" autocomplete="off" value="<?= (new UserInfo())->getUserName($list['create_user_id']) ?>" readonly="readonly" class="layui-input" style="width: 800px;">
            </div>
        </div>
        <div class="layui-form-item">
            <label for="nickname" class="layui-form-label">
                报警时间:
            </label>
            <div class="layui-input-inline">
                <input type="text" autocomplete="off" value="<?= date('Y-m-d H:i:s',strtotime($list['alarm_time'])) ?>" readonly="readonly" class="layui-input" style="width: 800px;">
            </div>
        </div>
        <div class="layui-form-item layui-form-text">
            <label for="desc" class="layui-form-label">
                通知内容:
            </label>
            <div class="layui-input-block">
                <textarea class="layui-textarea" readonly="readonly" style="height: 150px;width: 800px;"> <?php echo $list['content_data']; ?></textarea>
            </div>
        </div>
    </form>
    <div class="layui-row layui-col-space30">
        <div class="layui-col-md5">
            <div class="layui-form">
                <table class="layui-table">
                    <thead>
                    <tr>
                        <th colspan="2" class="layui-bg-black" style="text-align: center">未查阅人员</th>
                    </tr>
                    <tr>
                        <th style="width: 5px">#</th>
                        <th style="text-align: center">人员名称</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($noReadUserName as $key => $list): ?>
                        <tr>
                            <td><?= $key + 1; ?></td>
                            <td style="text-align: center"><?= $list['chinese_name'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="layui-col-md5">
            <table class="layui-table">
                <thead>
                <tr>
                    <th colspan="3" class="layui-bg-green" style="text-align: center">已查阅人员</th>
                </tr>
                <tr>
                    <th style="width: 5px">#</th>
                    <th style="text-align: center">人员名称</th>
                    <th>查阅时间</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($readUserName as $key => $list): ?>
                    <tr>
                        <td><?= $key + 1 ?></td>
                        <td style="text-align: center"><?= $list['chinese_name'] ?></td>
                        <td><?= date('Y-m-d H:i:s',strtotime($list['notice_time'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


