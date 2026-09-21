<?php
use yii\helpers\Url;
use backend\models\Blog;
use backend\models\UserInfo;
use backend\models\BlogUser;
?>

<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">日志管理</a>
        <a href="javascript:;">
          <cite>反馈列表</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <xblock>
        <div><p>日志标题：<?= $title ?></p></div>
    </xblock>
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>发布人</th>
            <th>修改人</th>
            <th>交接人</th>
            <th>反馈内容</th>
            <th>反馈状态</th>
            <th>反馈时间</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php if (!empty($dataProvider)): ?>
            <?php foreach ($dataProvider as $value): ?>
                <tr>
                    <td><?= $value['id'] ?></td>
                    <td><?= (new Blog())->getBlogUserName($value['blog_id']) ?></td>
                    <td><?= (new UserInfo())->getUserName($value['user_id']) ?></td>
                    <td><?= (new BlogUser())->getUserName($value['id']) ?></td>
                    <td style="max-width: 110px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;"><?= $value['reply'] ?></td>
                    <td><?= Blog::$statusArr[$value['status']] ?></td>
                    <td><?= date('Y-m-d H:i:s',strtotime($value['create_time'])) ?></td>
                    <td class="td-manage">
                        <button class="layui-btn layui-btn-xs layui-btn-normal" onclick="x_admin_show('详情','<?= Url::to([$this->context->id . '/self', 'id' => $value['id']]) ?>')">详情</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
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
</script>
<?php $this->endBlock() ?>
