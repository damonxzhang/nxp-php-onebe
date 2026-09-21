<?php

use yii\helpers\Url;
use backend\models\LogHandover;
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>生产日志</title>
    <link href="<?= Url::to('@web/lib/layui-v2.11.0/css/layui.css') ?>" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        body {
            background-color: #f5f7fa;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .nav-buttons {
            display: flex;
            gap: 10px;
        }

        .nav-button {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .nav-button.primary {
            background-color: #8bc34a;
            color: white;
        }

        .nav-button.secondary {
            background-color: #4caf50;
            color: white;
        }

        .info-section {
            display: flex;
            justify-content: space-between;
            color: #666;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .info-label {
            color: #ff9800;
        }

        .title-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .title {
            font-size: 24px;
            color: #333;
            position: relative;
            padding-left: 20px;
        }

        .title::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 20px;
            background-color: #8bc34a;
            border-radius: 2px;
        }

        .create-button {
            padding: 8px 16px;
            background-color: #8bc34a;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            background-color: #f8f9fa;
            padding: 12px;
            text-align: left;
            color: #333;
            font-weight: 500;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            color: #666;
        }

        .action-link {
            color: #2196f3;
            text-decoration: none;
            cursor: pointer;
        }

        .action-link:hover {
            text-decoration: underline;
        }

        .footer-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .footer-buttons-right {
            display: flex;
            gap: 10px;
        }

        .footer-button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
        }

        .footer-button.green {
            background-color: #8bc34a;
            color: white;
        }

        .footer-button.orange {
            background-color: #ff9800;
            color: white;
        }

        .footer-button.white {
            background-color:#16baaa;
            color: #FFFFFF;
        }

        .footer-button.dark {
            background-color:#2f363c;
            color: #FFFFFF;
            margin-left:30px;
        }

        .footer-button:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
        .ws-docs-anim > div{padding: 16px; transition: all .3s;}
        .ws-docs-anim > div .layui-anim{  
            width: 100px;
            height: 100px;
            line-height: 100px;
            margin: 50% auto 50px;
            text-align: center;
            cursor: pointer;
            color: #fff;
            border-radius: 50%;
            user-select: none;
            font-size: 18px;
        }
        .ws-docs-anim > div .layui-anim-material{
            background-color: #16b777;
        }
        .ws-docs-anim > div .layui-anim-appliances{
            background-color: #ffb800 ;
        }
        .ws-docs-anim > div .layui-anim-comment{
            background-color: #31bdec;
        }
        .ws-docs-anim > div > div{text-align: center; white-space: nowrap;}
        .layui-form-label-layer {
            position: relative;
            float: left;
            display: block;
            /* padding: 9px 15px; */
            width: 80px;
            font-weight: 400;
            line-height: 20px;
            text-align: right
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="nav-buttons">
                <button class="nav-button primary" onclick="lastonelog()">上一个交接班</button>
                <button class="nav-button secondary">回到当前班</button>
            </div>
            <div class="info-section">
                <div class="info-item" style="margin-right: 30px;">
                    <span class="info-label"> From : </span>
                    <span><?= $userStr ?></span> 
                </div>
                <div class="info-item" style="margin-right: 30px;">
                    <span class="info-label"> Date : </span>
                    <span><?= $date ?></span>
                </div>
                <div class="info-item">
                    <button type="button" class="nav-button layui-bg-red" lay-on="confirm">退出</button>
                </div>
            </div>
        </div>
        <div class="title-section">
            <h1 class="title">生产日志</h1>
            <?php if ($userCopulateNow == 0): ?>
                <button type="button" class="create-button" lay-on="test-page-custom">
                    <span>📝</span>
                    创建新内容
                </button>
            <?php endif; ?>
        </div>
        <div class="layui-table-body layui-table-main" style="height: 400px;">
            <table cellspacing="0" cellpadding="0" class="layui-table" style="table-layout: fixed;" lay-even>
                <thead>
                    <tr>
                        <th style="width: 5%;"> ID </th>
                        <th style="width: 85%;"> 日志内容描述 </th>
                        <th style="width: 10%;"> 操作 </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($dataProvider)): ?>
                        <?php foreach ($dataProvider as $key => $value): ?>
                            <tr >
                                <td><?= $key+1 ?></td>
                                <td><?= $value['chinese_name'].' : '.$value['content'] ?></td>
                                <td>
                                    <?php if ($value['type'] == 1 && $value['copulate'] == 0 && $value['user_id'] == $uid): ?>
                                        <button type="button" data-id="<?= $value['id'] ?>" class="layui-btn layui-btn-primary layui-btn-sm layui-border-green" onclick="log_handover_update('<?= Url::to(['log-handover-pad/type-update', 'id' => $value['id']]) ?>')">编辑信息</button>
                                    <?php elseif($value['type'] == 2 && $value['copulate'] == 0 && $value['user_id'] == $uid): ?>
                                        <button type="button" data-id="<?= $value['id'] ?>" class="layui-btn layui-btn-primary layui-btn-sm layui-border-green" onclick="log_handover_update('<?= Url::to(['log-handover-pad/device-type-update', 'id' => $value['id']]) ?>')">编辑信息</button>
                                    <?php elseif($value['type'] == 3 && $value['copulate'] == 0 && $value['user_id'] == $uid): ?>
                                        <button type="button" data-id="<?= $value['id'] ?>" class="layui-btn layui-btn-primary layui-btn-sm layui-border-green" onclick="log_handover_update('<?= Url::to(['log-handover-pad/comment-update', 'id' => $value['id']]) ?>')">编辑信息</button>
                                    <?php endif; ?>
                                    <?php if($value['status'] == 1 && $value['copulate'] == LogHandover::COPULATE_GO && $value['user_id'] != $uid): ?>
                                        <button type="button" class="layui-btn layui-btn-primary  layui-btn-sm layui-border-green" onclick="additional_information('<?= Url::to(['log-handover-pad/additional-information', 'id' => $value['id']]) ?>')">补充信息</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr ><td colspan="3" style="font-size: 16px;color:red;text-align: center;">暂无交接数据</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="footer-buttons">
            <?php if ($showDefault == 0): ?>
                <button class="footer-button green" lay-on="default-handover">
                    <span>📝</span>
                    添加默认交接
                </button>
            <?php else: ?>
                <div class="footer-button"></div>
            <?php endif; ?>
            <div class="footer-buttons-right">
                <?php if ($userCopulateNow == 1): ?>
                    <button class="footer-button orange" lay-on="retract-handover">
                        <span>🔄</span>
                        撤销
                    </button>
                <?php endif; ?>
                <?php if ($userCopulateNow == 0): ?>
                    <button class="footer-button white" lay-on="submit-handover-white">
                        <span>📋</span>
                        白班交接
                    </button>
                    <button class="footer-button dark" lay-on="submit-handover-dark">
                        <span>📋</span>
                        夜班交接
                    </button>
                <?php endif; ?>
                <?php if ($userCopulateNow == 2): ?>
                    <button class="footer-button green" lay-on="confirmed-handover">
                        <span>✅</span>
                        确认交接
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="<?= Url::to('@web/lib/layui-v2.11.0/layui.js') ?>"></script>
    <script src="<?= Url::to('@web/lib/jquery-3.7.1/jquery-3.7.1.min.js') ?>"></script>
    <script>
        
        /**上一个交接班 */
        function lastonelog() {
            window.location.href = "<?= Url::to(['log-handover-pad/lastonelog']) ?>";
        }

        /**更新 */
        function log_handover_update(url) {
            layer.open({
                type: 2,
                area: ['700px', '600px'],
                fix: false, //不固定
                resize: false,
                shadeClose: true,
                shade: 0.4,
                title: '更新内容',
                content: url
            });
        }

        /**补充信息 */
        function additional_information(url) {
            layer.open({
                type: 2,
                area: ['600px', '400px'],
                fix: false, //不固定
                resize: false,
                shadeClose: true,
                shade: 0.4,
                title: '补充信息',
                content: url
            });
        }

        layui.use(function(){
            var $ = layui.$;
            var layer = layui.layer;
            var util  = layui.util;
            var form  = layui.form;
            var table = layui.table;
            var dropdown = layui.dropdown; //下拉菜单

            // 事件
            util.on('lay-on', {
                confirm: function(){
                    layer.confirm('确认退出当前登录账号？', {
                        btn: ['确认', '取消'] //按钮
                    }, function(){
                        window.location.href = "<?= Url::to(['log-handover-pad/logout']) ?>";
                    });
                },

                'submit-handover-white': function(){
                    layer.confirm('确认提交白班交接？', {
                        btn: ['确认', '返回'] //按钮
                    }, function(){
                        $.get('<?= Url::to(['log-handover-pad/submit-handover-white']) ?>', function (res) {
                            layer.closeAll();
                            if (res.status === 200) {
                                layer.msg(res.msg, {icon: 1,time: 1500}, function () {
                                    var index = parent.layer.getFrameIndex(window.name);
                                    parent.location.reload();
                                })
                            } else {
                                layer.msg(res.msg, {icon: 2, time: 1500});
                            }
                        }, 'json');
                        return false; // 阻止默认 form 跳转
                    });
                },

                 'submit-handover-dark': function(){
                    layer.confirm('确认提交夜班交接？', {
                        btn: ['确认', '返回'] //按钮
                    }, function(){
                        $.get('<?= Url::to(['log-handover-pad/submit-handover-dark']) ?>', function (res) {
                            layer.closeAll();
                            if (res.status === 200) {
                                layer.msg(res.msg, {icon: 1,time: 1500}, function () {
                                    var index = parent.layer.getFrameIndex(window.name);
                                    parent.location.reload();
                                })
                            } else {
                                layer.msg(res.msg, {icon: 2, time: 1500});
                            }
                        }, 'json');
                        return false; // 阻止默认 form 跳转
                    });
                },


                'retract-handover': function(){
                    layer.confirm('确认撤销交接？', {
                        btn: ['撤销', '返回'] //按钮
                    }, function(){
                        $.get('<?= Url::to(['log-handover-pad/retract-handover']) ?>', function (res) {
                            layer.closeAll();
                            if (res.status === 200) {
                                layer.msg(res.msg, {icon: 1,time: 1500}, function () {
                                    var index = parent.layer.getFrameIndex(window.name);
                                    parent.location.reload();
                                })
                            } else {
                                layer.msg(res.msg, {icon: 2, time: 1500});
                            }
                        }, 'json');
                        return false; // 阻止默认 form 跳转
                    });
                },

                'confirmed-handover': function(){
                    layer.confirm('确认交接？', {
                        btn: ['确认', '取消'] //按钮
                    }, function(){
                        $.get('<?= Url::to(['log-handover-pad/confirmed-handover']) ?>', function (res) {
                            layer.closeAll();
                            if (res.status === 200) {
                                layer.msg(res.msg, {icon: 1,time: 1500}, function () {
                                    var index = parent.layer.getFrameIndex(window.name);
                                    parent.location.reload();
                                })
                            } else {
                                layer.msg(res.msg, {icon: 2, time: 1500});
                            }
                        }, 'json');
                        return false; // 阻止默认 form 跳转
                    });
                },

                'return-button': function(){
                    var index = parent.layer.getFrameIndex(window.name);
                        parent.layer.close(index);
                        parent.location.reload();
                },

                'test-page-custom': function(){
                    layer.open({
                        type: 1,
                        id:'selectdata',//弹层的唯一索引值
                        area: 'auto',
                        offset: 'auto',// 坐标始终垂直水平居中
                        closeBtn: 0,// 不显示关闭图标
                        resize: false,
                        shadeClose: true, // 点击遮罩关闭层
                        title: false, // 不显示标题栏
                        content: `
                            <div class="layui-row ws-docs-anim" style="margin-left:50px;">
                                <div class="layui-col-sm3">
                                    <div class="layui-anim layui-anim-material" data-anim="layui-anim-material" lay-on="material-create">物料</div>
                                </div>
                                <div class="layui-col-sm3" style="margin-left:20px;">
                                    <div class="layui-anim layui-anim-appliances" data-anim="layui-anim-appliances" lay-on="appliances-create">设备</div>
                                </div>
                                <div class="layui-col-sm3" style="margin-left:20px;">
                                    <div class="layui-anim layui-anim-comment" data-anim="layui-anim-comment" lay-on="comment-create">备注</div>
                                </div>
                            </div>
                        `
                    });
                },
                'material-create': function(){
                    layer.open({
                        type: 1,
                        area: ['700px', '600px'],
                        resize: false,
                        shadeClose: true,
                        title: '创建物料新内容',
                        content: `
                            <div class="layui-form" lay-filter="filter-test-layer" style="margin: 16px;">
                                <div class="demo-login-container">
                                    <div class="layui-form-item layui-form-text">
                                        <label class="layui-form-label-layer">信息内容</label>
                                        <div class="layui-input-block">
                                            <textarea placeholder="请输入信息内容" name="content" class="layui-textarea" lay-verify="required" ></textarea>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label-layer">物料类型：</label>
                                        <div class="layui-input-block">
                                            <select name="type_id" lay-verify="required">
                                                <option value=""></option>
                                                <?php foreach ($logHandoverTypeArr as $key => $value): ?>
                                                    <option value="<?= $key ?>" ><?= $value ?></option>
                                                <?php endforeach; ?>
                                            </select> 
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label-layer">批次类型：</label>
                                        <div class="layui-input-block">
                                            <select name="batch_id" lay-verify="required">
                                                <option value=""></option>
                                                <?php foreach ($logHandoverBatchArr as $key => $value): ?>
                                                    <option value="<?= $key ?>" ><?= $value ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label-layer">状态是否延续：</label>
                                        <div class="layui-input-block">
                                            <input type="radio" name="status" value="1" title="是">
                                            <input type="radio" name="status" value="2" title="否" checked>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <div class="layui-input-block" style="margin-bottom: 6px; text-align: right;">
                                            <button type="button" class="layui-btn layui-btn-primary layui-bg-orange" lay-on="return-button">返回</button>
                                            <button type="submit" class="layui-btn" lay-submit lay-filter="type-submit-val">提交</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `,
                        success: function(){
                            // 对弹层中的表单进行初始化渲染
                            form.render();
                            // 表单提交事件
                            form.on('submit(type-submit-val)', function(data){
                                layer.load(3);
                                $.post('<?= Url::to(['log-handover-pad/type-create']) ?>', data.field, function (res) {
                                    layer.closeAll();
                                    if (res.status === 200) {
                                        layer.msg(res.msg, {icon: 1,time: 1500}, function () {
                                            var index = parent.layer.getFrameIndex(window.name);
                                            parent.layer.close(index);
                                            parent.location.reload();
                                        })
                                    } else {
                                        layer.msg(res.msg, {icon: 2, time: 1500});
                                    }
                                }, 'json');
                                return false; // 阻止默认 form 跳转
                            });
                        }
                    });
                },

                'appliances-create': function(){
                    layer.open({
                        type: 1,
                        area: ['700px', '600px'],
                        resize: false,
                        shadeClose: true,
                        title: '创建设备新内容',
                        content: `
                            <div class="layui-form" lay-filter="filter-test-layer" style="margin: 16px;">
                                <div class="demo-login-container">
                                    <div class="layui-form-item layui-form-text">
                                        <label class="layui-form-label-layer">信息内容</label>
                                        <div class="layui-input-block">
                                            <textarea placeholder="请输入信息内容" name="content" class="layui-textarea" lay-verify="required" ></textarea>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label-layer">设备类型：</label>
                                        <div class="layui-input-block">
                                            <select name="device_type_id" lay-verify="required">
                                                <option value=""></option>
                                                <?php foreach ($logHandoverDeviceTypeArr as $key => $value): ?>
                                                    <option value="<?= $key ?>" ><?= $value ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label-layer">状态是否延续：</label>
                                        <div class="layui-input-block">
                                            <input type="radio" name="status" value="1" title="是">
                                            <input type="radio" name="status" value="2" title="否" checked>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <div class="layui-input-block" style="margin-bottom: 6px; text-align: right;">
                                            <button type="button" class="layui-btn layui-btn-primary layui-bg-orange" lay-on="return-button">返回</button>
                                            <button type="submit" class="layui-btn" lay-submit lay-filter="device-type-submit-val">提交</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `,
                        success: function(){
                            // 对弹层中的表单进行初始化渲染
                            form.render();
                            // 表单提交事件
                            form.on('submit(device-type-submit-val)', function(data){
                                layer.load(3);
                                $.post('<?= Url::to(['log-handover-pad/device-type-create']) ?>', data.field, function (res) {
                                    layer.closeAll();
                                    if (res.status === 200) {
                                        layer.msg(res.msg, {icon: 1,time: 1500}, function () {
                                            var index = parent.layer.getFrameIndex(window.name);
                                            parent.layer.close(index);
                                            parent.location.reload();
                                        })
                                    } else {
                                        layer.msg(res.msg, {icon: 2, time: 1500});
                                    }
                                }, 'json');
                                return false; // 阻止默认 form 跳转
                            });
                        }
                    });
                },

                'comment-create': function(){
                    layer.open({
                        type: 1,
                        area: ['700px', '600px'],
                        resize: false,
                        shadeClose: true,
                        title: '创建备注新内容',
                        content: `
                            <div class="layui-form" lay-filter="filter-test-layer" style="margin: 16px;">
                                <div class="demo-login-container">
                                    <div class="layui-form-item layui-form-text">
                                        <label class="layui-form-label-layer">信息内容</label>
                                        <div class="layui-input-block">
                                            <textarea placeholder="请输入信息内容" name="content" class="layui-textarea" lay-verify="required" ></textarea>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label-layer">备注类型：</label>
                                        <div class="layui-input-block">
                                            <select name="comment_id" lay-verify="required">
                                                <option value=""></option>
                                                <?php foreach ($logHandoverCommentTypeArr as $key => $value): ?>
                                                    <option value="<?= $key ?>" ><?= $value ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label-layer">状态是否延续：</label>
                                        <div class="layui-input-block">
                                            <input type="radio" name="status" value="1" title="是">
                                            <input type="radio" name="status" value="2" title="否" checked>
                                        </div>
                                    </div>

                                    <div class="layui-form-item">
                                        <div class="layui-input-block" style="margin-bottom: 6px; text-align: right;">
                                            <button type="button" class="layui-btn layui-btn-primary layui-bg-orange" lay-on="return-button">返回</button>
                                            <button type="submit" class="layui-btn" lay-submit lay-filter="comment-submit-val">提交</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `,
                        success: function(){
                            // 对弹层中的表单进行初始化渲染
                            form.render();
                            // 表单提交事件
                            form.on('submit(comment-submit-val)', function(data){
                                layer.load(3);
                                $.post('<?= Url::to(['log-handover-pad/comment-create']) ?>', data.field, function (res) {
                                    layer.closeAll();
                                    if (res.status === 200) {
                                        layer.msg(res.msg, {icon: 1,time: 1500}, function () {
                                            var index = parent.layer.getFrameIndex(window.name);
                                            parent.layer.close(index);
                                            parent.location.reload();
                                        })
                                    } else {
                                        layer.msg(res.msg, {icon: 2, time: 1500});
                                    }
                                }, 'json');
                                return false; // 阻止默认 form 跳转
                            });
                        }
                    });
                },

                'default-handover': function(){
                    layer.open({
                        type: 1,
                        id:'selectdata',//弹层的唯一索引值
                        area: 'auto',
                        offset: 'auto',// 坐标始终垂直水平居中
                        closeBtn: 0,// 不显示关闭图标
                        resize: false,
                        shadeClose: true, // 点击遮罩关闭层
                        title: false, // 不显示标题栏
                        content: `
                        <div class="layui-row ws-docs-anim" style="margin-left:50px;">
                            <div class="layui-col-sm3">
                                <div class="layui-anim layui-anim-material" data-anim="layui-anim-material" lay-on="default-handover-material-create">物料</div>
                            </div>
                            <div class="layui-col-sm3" style="margin-left:20px;">
                                <div class="layui-anim layui-anim-appliances" data-anim="layui-anim-appliances" lay-on="default-handover-appliances-create">设备</div>
                            </div>
                            <div class="layui-col-sm3" style="margin-left:20px;">
                                <div class="layui-anim layui-anim-comment" data-anim="layui-anim-comment" lay-on="default-handover-comment-create">备注</div>
                            </div>
                        </div>
                        `
                    });
                },

                'default-handover-material-create': function(){
                    layer.confirm('确认添加物料默认交接？', {
                        btn: ['确认', '取消'] //按钮
                    }, function(){
                        $.get('<?= Url::to(['log-handover-pad/default-handover-material-create']) ?>', function (res) {
                            layer.closeAll();
                            if (res.status === 200) {
                                layer.msg(res.msg, {icon: 1,time: 1500}, function () {
                                    var index = parent.layer.getFrameIndex(window.name);
                                    parent.location.reload();
                                })
                            } else {
                                layer.msg(res.msg, {icon: 2, time: 1500});
                            }
                        }, 'json');
                        return false; // 阻止默认 form 跳转
                    });
                },

                'default-handover-appliances-create': function(){
                    layer.confirm('确认添加设备默认交接？', {
                        btn: ['确认', '取消'] //按钮
                    }, function(){
                        $.get('<?= Url::to(['log-handover-pad/default-handover-appliances-create']) ?>', function (res) {
                            layer.closeAll();
                            if (res.status === 200) {
                                layer.msg(res.msg, {icon: 1,time: 1500}, function () {
                                    var index = parent.layer.getFrameIndex(window.name);
                                    parent.location.reload();
                                })
                            } else {
                                layer.msg(res.msg, {icon: 2, time: 1500});
                            }
                        }, 'json');
                        return false; // 阻止默认 form 跳转
                    });
                },

                'default-handover-comment-create': function(){
                    layer.confirm('确认添加备注默认交接？', {
                        btn: ['确认', '取消'] //按钮
                    }, function(){
                        $.get('<?= Url::to(['log-handover-pad/default-handover-comment-create']) ?>', function (res) {
                            layer.closeAll();
                            if (res.status === 200) {
                                layer.msg(res.msg, {icon: 1,time: 1500}, function () {
                                    var index = parent.layer.getFrameIndex(window.name);
                                    parent.location.reload();
                                })
                            } else {
                                layer.msg(res.msg, {icon: 2, time: 1500});
                            }
                        }, 'json');
                        return false; // 阻止默认 form 跳转
                    });
                },
            });
        });
    </script>
</body>
</html>