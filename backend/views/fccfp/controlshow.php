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

</head>


<div id='el-app'>
    <draggable class="main-box" v-model="dragList" :move="onMove" filter=".undraggable">
        <div v-for="(stage,index) in dragList" class="stage-item" :key="index">
            <div class="stage-header">
                <div class="title" style="background-color: #4682B4;color: #FFFF00; font-size: 16px;text-align: center; line-height: 40px;  width: 280px; height: 40px; margin: 0 auto; ">{{stage.name}}</div>
            </div>
            <draggable tag="span" class="list-group-stage" v-model="stage.list" v-bind="dragOptions" :move="onMove">
                <transition-group tag="ul" type="transition" class="list-group" :name="'flip-list'">
                    <li class="list-group-item stage-header" v-if="index == 0" v-for="(element,k) in stage.list" :key="element.order"  style="background-color: #78eb78;color: #20c0c0;">
                        <span class="name">{{element.name}}</span>
                    </li>
                    <li class="list-group-item stage-header" v-if="index == 1" v-for="(element,k) in stage.list" :key="element.order"  style="background-color: #e6e673;color: #000000;">
                        <span class="name">{{element.name}}</span>
                    </li>
                    <li class="list-group-item stage-header" v-if="index == 2" v-for="(element,k) in stage.list" :key="element.order"  style="background-color: #55b274;color: #000000;">
                        <span class="name">{{element.name}}</span>
                    </li>
                    <li class="list-group-item stage-header" v-if="index == 3" v-for="(element,k) in stage.list" :key="element.order"  style="background-color: #FFD39B;color: #CD0000;">
                        <span class="name">{{element.name}}</span>
                    </li>
                </transition-group>
            </draggable>
        </div>
    </draggable>
</div>

<script>
    $(document).ready(function() {
        // 每 300000 毫秒（5分钟）刷新一次页面
        setInterval(function() {
            location.reload(); // 强制从服务器重新加载页面
        }, 300000); // 5分钟 = 5 * 60 * 1000 毫秒
    });
    var app = new Vue({
        el: '#el-app',
        data() {
            const message  = JSON.parse('<?php echo json_encode($fcinspover); ?>');
            const message2 = JSON.parse('<?php echo json_encode($waitBakeTrackIn); ?>');
            const message3 = JSON.parse('<?php echo json_encode($bakeTrackIn); ?>');
            const message4 = JSON.parse('<?php echo json_encode($bakeTrackOut); ?>');
            return {
                dragList: [{
                    list: message.map((name, index) => {
                        return {
                            name,
                            order: index + 1,
                            fixed: true
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
                            fixed: true
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