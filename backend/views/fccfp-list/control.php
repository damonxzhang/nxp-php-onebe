<?php
use yii\helpers\Url;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <!-- 引入样式 -->
    <link href="<?= Url::to('@web/control/static/bootstrap.css') ?>" rel="stylesheet">
    <link href="<?= Url::to('@web/control/static/element-ui.css') ?>" rel="stylesheet">
    <link href="<?= Url::to('@web/control/static/style.css') ?>" rel="stylesheet">
    <script src="<?= Url::to('@web/control/static/vue.js') ?>"></script>
    <script src="<?= Url::to('@web/control/static/element-ui.js') ?>"></script>
    <script src="<?= Url::to('@web/control/static/jquery.min.js') ?>"></script>
    <script src="<?= Url::to('@web/control/static/Sortable.min.js') ?>"></script>
    <script src="<?= Url::to('@web/control/static/vuedraggable.umd.min.js') ?>"></script>
    <style>

        .main-box .stage-item:nth-of-type(1) .list-group-stage .list-group .list-group-item {
            background-color: #78eb78;
            color: #20c0c0;
        }
        .main-box .stage-item:nth-of-type(2) .list-group-stage .list-group .list-group-item {
            background-color: #e6e673;
            color: #000000;
        }
        .main-box .stage-item:nth-of-type(3) .list-group-stage .list-group .list-group-item {
           background-color: #55b274;
           color: #000000;
        }
        .main-box .stage-item:nth-of-type(4) .list-group-stage .list-group .list-group-item {
            background-color: #FFD39B;
            color: #CD0000;
        }

    </style>
</head>

<div id='el-app'>
    <draggable class="main-box" v-model="dragList" :move="onMove" filter=".undraggable">
        <div v-for="(stage,index) in dragList" class="stage-item" :key="index">
            <div class="stage-header">
                <div class="title" style="background-color: #4682B4;color: #FFFF00; font-size: 16px;text-align: center; line-height: 40px;  width: 280px; height: 40px; margin: 0 auto; ">{{stage.name}}</div>
                <div v-if="index == 0">
                    <el-button type="primary" circle size="mini" @click="moveStage(index)" style="background-color: #ff5722;">撤回物料</el-button>
                </div>
                <div v-if="index == 1">
                    <el-button type="primary" circle size="mini" @click="delStage(index)" style="background-color: #008B45;">保存物料</el-button>
                </div>
            </div>
            <draggable tag="span" class="list-group-stage" v-model="stage.list" v-bind="dragOptions" :move="onMove">
                <transition-group tag="ul" type="transition" class="list-group" :name="'flip-list'">
                   <li class="list-group-item stage-header" v-for="(element,k) in stage.list" :key="element.order">
                        <span class="name">{{element.name}}</span>
                    </li>
                </transition-group>
            </draggable>
        </div>
    </draggable>
</div>

<script>
    var app = new Vue({
        el: '#el-app',
        data() {
            const message  = <?php echo json_encode($fcinspover); ?>;
            const message2 = <?php echo json_encode($waitBakeTrackIn); ?>;
            const message3 = <?php echo empty($bakeTrackIn) ? '["暂无BAKE中的物料!!!"]' : json_encode($bakeTrackIn); ?>;
            const message4 = <?php echo empty($bakeTrackOut) ? '["暂无BAKE结束的物料!!!"]' : json_encode($bakeTrackOut); ?>;
            return {
                dragList: [{
                    list: message.map((name, index) => {
                        return {
                            name,
                            order: index + 1,
                            fixed: false
                        };
                    }),
                    order: 10001,
                    name: "FC_INSP 结束",
                    fixed: true
                }, {
                    list: message2.map((name, index) => {
                        return {
                            name,
                            order: index + 20,
                            fixed: false
                        };
                    }),
                    order: 10003,
                    name: "后线确认需要BAKE的物料",
                    fixed: true
                }, {
                    list: message3.map((name, index) => {
                        return {
                            name,
                            order: index + 40,
                            fixed: true
                        };
                    }),
                    order: 10005,
                    name: "BAKE中的物料",
                    fixed: true
                }, {
                    list: message4.map((name, index) => {
                        return {
                            name,
                            order: index + 60,
                            fixed: true
                        };
                    }),
                    order: 10005,
                    name: "BAKE结束的物料",
                    fixed: true
                }],
                editable: true,
                order: 1000
            };
        },
        methods: {
            orderList() {
                this.list = this.list.sort((one, two) => {
                    return one.order - two.order;
                });
            },
            onMove({
                relatedContext,
                draggedContext
            }) {
                const relatedElement = relatedContext.element;
                const draggedElement = draggedContext.element;
                return (
                    (!relatedElement || !relatedElement.fixed) && !draggedElement.fixed
                );
            },
            // 撤回物料
            moveStage(index) {
                var containername = this.dragList[index].list;
                if($.isEmptyObject(containername)) {
                    layer.msg('请拖拽需要撤回的物料编码!');
                    return false;  
                }

                $.ajax({
                    url: "<?= Url::to(['fccfp-list/movedropbake']) ?>",
                    type: 'post',
                    dataType: 'json',
                    data: {
                        date: containername
                    },
                    success: function(data) {
                        layer.msg(data.msg, {icon: 1,time: 3000}, function () {
                            window.location.reload(true);
                        })
                        return false;
                    }
                });
            },
            
            delStage(index) {
                var containername = this.dragList[index].list;
                if($.isEmptyObject(containername)) {
                    layer.msg('请拖拽物料编码!');
                    return false;  
                }

                var layer = layui.layer;
                var util = layui.util;
                var form = layui.form;
                layer.open({
                    type: 1,
                    area: '400px',
                    resize: false,
                    shadeClose: true,
                    title: '请选择Bake时间',
                    content: `
                    <div class="layui-form" style="margin: 10px;">
                        <div class="demo-login-container">
                         <div class="layui-form-item">
                            <label class="layui-form-label">
                                Time:
                            </label>
                            <div class="layui-input-inline" style="width: 250px;">
                                <input type="text" name="input_drop_bake_time" value="" lay-verify="required" id="bake-laydate" placeholder="请选择Bake时间" autocomplete="off" class="layui-input" lay-affix="clear"  lay-on="time-create">
                            </div>
                        </div>
                        <div class="layui-form-item" style="margin-left: 30%; width: 150px; ">
                            <button class="layui-btn layui-btn-fluid" lay-submit lay-filter="bake-submit">提交</button>
                        </div>
                        </div>
                    </div>
                    `,
                    success: function(){
                    // 对弹层中的表单进行初始化渲染
                    form.render();
                    // 表单提交事件
                    form.on('submit(bake-submit)', function(data){
                        var input_drop_bake_time = data.field.input_drop_bake_time; // 获取表单字段值
                        $.ajax({
                            url: "<?= Url::to(['fccfp-list/dropbake']) ?>",
                            type: 'post',
                            dataType: 'json',
                            data: {
                                date: containername,
                                input_drop_bake_time: input_drop_bake_time
                            },
                            success: function(data) {
                                layer.msg(data.msg, {icon: 1,time: 3000}, function () {
                                    window.location.reload(true);
                                })
                                return false;
                            }
                        });
                        return false; // 阻止默认 form 跳转
                    });
                    }
                });
                var laydate = layui.laydate;
                // 渲染
                laydate.render({
                    elem: '#bake-laydate',
                    type: 'datetime'
                });
            }
        },
        computed: {
            dragOptions() {
                return {
                    animation: 1,
                    group: "description",
                    disabled: !this.editable,
                    ghostClass: "ghost"
                };
            },
            listString() {
                return JSON.stringify(this.dragList, null, 2);
            }
        }

    });
</script>

</body>

</html>