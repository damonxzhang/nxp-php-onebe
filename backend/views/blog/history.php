<?php

use backend\models\UserInfo;
use yii\helpers\Url;
use backend\models\Blog;
use backend\models\User;
use yii\helpers\ArrayHelper;
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">日志管理</a>
        <a href="javascript:;">
          <cite>历史日志</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so">
            <input class="layui-input" id="autofocus" value="<?php echo ArrayHelper::getValue($request, 'title') ?>" autocomplete="off" placeholder="标题" name="title">
            <input class="layui-input" id="autofocus" value="<?php echo ArrayHelper::getValue($request, 'matter_number') ?>" autocomplete="off" placeholder="物料编号" name="matter_number">
            <input class="layui-input" id="autofocus" value="<?php echo ArrayHelper::getValue($request, 'device_number') ?>" autocomplete="off" placeholder="设备编号" name="device_number">
            <input class="layui-input" placeholder="开始日" name="start_time" id="start_time" value="<?= !empty($request['start_time']) ? date('Y-m-d H:i:s',strtotime($request['start_time'])) : '' ?>" autocomplete="off">
            <input class="layui-input" placeholder="截止日" name="end_time" id="end_time" value="<?= !empty($request['end_time']) ? date('Y-m-d H:i:s',strtotime($request['end_time'])) : '' ?>" autocomplete="off">
            <button class="layui-btn" lay-submit="" lay-filter="sreach"><i class="layui-icon">&#xe615;</i></button>
            <button class="layui-btn" type="button" onclick="location.href= '<?= Url::to([$this->context->id . '/history'])?>'" >
                <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>
            </button>
        </form>
    </div>
    <xblock>
        <button class="layui-btn layui-bg-black" onclick="export_excel()"><i class="layui-icon">&#xe62f;</i>导出</button>
        <span class="x-right" style="line-height:30px">共有数据：<span class="count_num"><?= $pagination->totalCount ?></span> 条 ( <?= $pagination->getPageCount() ?> 页 )</span>
    </xblock>
    <table class="layui-table">
        <thead>
        <tr>
            <th>
                <div class="layui-unselect header layui-form-checkbox" lay-skin="primary"><i class="layui-icon">&#xe605;</i></div>
            </th>
            <th>ID</th>
            <th>标题</th>
            <th>内容</th>
            <th>发布人</th>
            <th>状态</th>
            <th>创建时间</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php if (!empty($dataProvider)): ?>
            <?php foreach ($dataProvider as $value): ?>
                <tr>
                    <td>
                        <div class="layui-unselect layui-form-checkbox" lay-skin="primary" data-id=<?= $value['id'] ?>><i class="layui-icon">&#xe605;</i></div>
                    </td>
                    <td><?= $value['id'] ?></td>
                    <td><?= $value['title'] ?></td>
                    <td style="max-width: 110px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;"><?= $value['content_data'] ?></td>
                    <td><?= (new UserInfo())->getUserName($value['user_id']) ?></td>
                    <td><?= Blog::$statusArr[$value['status']] ?></td>
                    <td><?= date('Y-m-d H:i:s',strtotime($value['create_time'])) ?></td>
                    <td class="td-manage">
                        <button type="button" class="layui-btn layui-btn-xs" onclick="x_admin_show('编辑','<?= Url::to([$this->context->id . '/update', 'id' => $value['id'], 'define' =>'history']) ?>')">编辑</button>
                        <button type="button" class="layui-btn layui-btn-xs" onclick="x_admin_show('日志详情','<?= Url::to([$this->context->id . '/detail', 'id' => $value['id']]) ?>')">日志详情</button>
                        <button class="layui-btn layui-btn-xs" onclick="x_admin_show('反馈列表','<?= Url::to([$this->context->id . '/feedback', 'id' => $value['id']]) ?>')"><i class="layui-icon">&#xe63c;</i>反馈列表</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
    <?= $this->render('@app/views/layouts/pagination', compact('pagination')) ?>
</div>

<?php $this->beginBlock('footer') ?>
<script>
    //时间日期范围
    layui.use('laydate', function () {
        var laydate = layui.laydate;
        laydate.render({
            elem: '#create_time'
            ,type: 'datetime'
            ,range: '至'
        });
    });

    function changeStatus(id, status) {
        layer.load(3);
        $.post('<?= Url::to([$this->context->id . '/change-status']) ?>', {id: id, status: status}, function (res) {
            layer.closeAll();
            var icon = 2;
            if (res.status === 200){
                // $(obj).parents("tr").find(".td-status").find('span').removeClass('layui-btn-disabled').html('已启用');
                icon = 1;
            }
            layer.msg(res.msg, {icon: icon, time: 1500})
        }, 'json')
    }

    function export_excel(){
        var idlist   = [];
        $('.layui-form-checked').each(function () {
            if (typeof($(this).data('id')) !="undefined" && $(this).data('id')!=0){
                idlist.push($(this).data('id'));
            }
        });
        layui.use(['jquery', 'layer'], function() {
            var $ = layui.jquery;
            $.ajax({
                url: '<?= Url::to([$this->context->id . '/export'])?>',
                type: 'GET',
                data: {idlist: idlist, status: 4},
                dataType: 'json',
                success: function(res) {
                    LAY_EXCEL.exportExcel(res.data, res.msg, 'xlsx');
                }
            });
        });
    }
</script>
<?php $this->endBlock() ?>
