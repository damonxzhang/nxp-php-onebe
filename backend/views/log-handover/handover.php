<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use backend\models\UserInfo;
use backend\models\LogHandover;
use yii\helpers\Html;
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">白夜班日志交接</a>
        <a href="javascript:;">
          <cite>日志交接</cite>
        </a>
      </span>
</div>

<div class="x-body">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so">
            <div class="layui-input-inline">
                <?= Html::dropDownList('section', ArrayHelper::getValue($request, 'section'), ArrayHelper::merge(['' => '部门'], UserInfo::$sectionArr), ['name' => 'section']); ?>
            </div>
            <input class="layui-input" value="<?php echo ArrayHelper::getValue($request, 'content') ?>" autocomplete="off" placeholder="日志内容" name="content">
            <input class="layui-input" placeholder="请选择日期" name="date" id="endTime" value="<?= !empty($request['date']) ? $request['date'] : '' ?>" autocomplete="off">
            <div class="layui-input-inline">
                <?= Html::dropDownList('classes', ArrayHelper::getValue($request, 'classes'), ArrayHelper::merge(['' => '白夜班'], LogHandover::$classesArr), ['name' => 'classes']); ?>
            </div>
            <button class="layui-btn" lay-submit lay-filter="submit"><i class="layui-icon">&#xe615;</i></button>
            <button class="layui-btn" type="button" onclick="location.href= '<?= Url::to([$this->context->id . '/handover'])?>'" >
                <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>刷新
            </button>
            <button type="button" class="layui-btn layui-bg-red" onclick="batch_deletion()"><i class="layui-icon layui-icon-delete"></i>批量删除</button>
            <button type="button" class="layui-btn layui-btn-warm" onclick="x_admin_show('打印预览','<?= Url::to([$this->context->id . '/loglist', 'date' => ArrayHelper::getValue($request, 'date',date('Y-m-d',time())),'classes'=> ArrayHelper::getValue($request, 'classes',0),'section'=> ArrayHelper::getValue($request, 'section',0),'content'=> ArrayHelper::getValue($request, 'content','')]) ?>')"><i class="layui-icon layui-icon-print"></i>打印预览</button>
        </form>
    </div>
    <xblock style="height: 29px;">
        <span class="x-right" style="line-height:30px">共有数据：<span class="count_num"><?= $pagination->totalCount ?></span> 条 ( <?= $pagination->getPageCount() ?> 页 )</span>
    </xblock>
    <table class="layui-table">
        <thead>
            <th>
                <div class="layui-unselect header layui-form-checkbox" lay-skin="primary"><i class="layui-icon">&#xe605;</i></div>
            </th>
            <th>序号</th>
            <th>标题</th>
            <th>交接类型</th>
            <th>物料类型</th>
            <th>批次类型</th>
            <th>设备类型</th>
            <th>备注类型</th>
            <th>发布人</th>
            <th>区域</th>
            <th>班别</th>
            <th>白夜班</th>
            <th>日期</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php foreach ($dataProvider as $list): ?>
            <tr <?php if ($list['is_sensitive_words'] == 1): ?> style="font-weight:bold;color: red;" <?php endif; ?>>
                <td>
                    <div class="layui-unselect layui-form-checkbox" lay-skin="primary" data-id=<?= $list['id'] ?>><i class="layui-icon">&#xe605;</i></div>
                </td>
                <td><?= $list['id'] ?></td>
                <td><?= $list['content'] ?></td>
                <td><?= $list['type_name'] ?></td>
                <td><?= ArrayHelper::getValue($logHandoverTypeArr, $list['type_id'],'') ?></td>
                <td><?= ArrayHelper::getValue($logHandoverBatchArr, $list['batch_id'],'') ?></td>
                <td><?= ArrayHelper::getValue($logHandoverDeviceTypeArr, $list['device_type_id'],'') ?></td>
                <td><?= ArrayHelper::getValue($logHandoverCommentTypeArr, $list['comment_id'],'') ?></td>
                <td><?= $list['chinese_name'] ?></td>
                <td><?= ArrayHelper::getValue(UserInfo::$sectionArr, $list['section'],'') ?></td>
                <td><?= ArrayHelper::getValue(UserInfo::$shiftArr, $list['shift'],'') ?></td>
                <td><?= ArrayHelper::getValue(LogHandover::$classesArr, $list['classes'],'') ?></td>
                <td><?= $list['date'] ?></td>
                <td class="td-manage">
                    <button type="button" class="layui-btn layui-btn-xs" onclick="x_admin_show('详情','<?= Url::to([$this->context->id . '/handover-detail', 'id' => $list['id']]) ?>')"><i class="layui-icon">&#xe63c;</i>详情</button>
                    <button class="layui-btn layui-btn-xs layui-btn-normal" onclick="x_admin_show('编辑','<?= Url::to([$this->context->id . '/handover-update', 'id' => $list['id']]) ?>')"><i class="layui-icon">&#xe642;</i>编辑</button>
                    <button class="layui-btn layui-btn-xs" onclick="del(this,'<?= $list['id'] ?>', '<?= Url::to([$this->context->id . '/handover-del']) ?>')"><i class="layui-icon">&#xe640;</i>删除</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?= $this->render('@app/views/layouts/pagination', compact('pagination')) ?>
</div>


<?php $this->beginBlock('footer') ?>
<script>
    layui.use(['form', 'layer'], function(){
        var form = layui.form;
        var layer = layui.layer;
        
        // 监听表单提交
        form.on('submit(submit)', function(data){
           var $ = layui.jquery;
            if (data.field.date.trim() === "" && data.field.classes.trim() != "") {
                layer.msg('请选择日期!');
                return false;
            }

            if (data.field.date.trim() != "" && data.field.classes.trim() === "") {
                layer.msg('请选择白夜班!');
                return false;
            }
            form.submit();
            return false; // 阻止表单跳转
        });
    });

    function batch_deletion(){
        var idlist   = [];
        $('.layui-form-checked').each(function () {
            if (typeof($(this).data('id')) !="undefined" && $(this).data('id')!=0){
                idlist.push($(this).data('id'));
            }
        });
        if (idlist.length === 0) {
            layer.msg('请先勾选需要批量删除的数据!');
            return false;
        }
      
        layui.use(['jquery', 'layer'], function() {
            layer.confirm('确定要删除么？', {
            btn: ['确定', '取消'] //按钮
            }, function(){
                var $ = layui.jquery;
                $.ajax({
                    url: '<?= Url::to([$this->context->id . '/batch-deletion'])?>',
                    type: 'GET',
                    data: {idlist: idlist},
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 200) {
                            layer.msg(res.msg, {icon: 1,time: 1500}, function () {
                                location.reload();
                            })
                        } else {
                            layer.msg(res.msg, {icon: 2, time: 1500})
                        }
                    }
                });
            });
            
        });
    }

</script>
<?php $this->endBlock() ?>