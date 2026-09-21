<?php

use backend\models\UserInfo;
use yii\helpers\Url;
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">消息管理</a>
        <a href="javascript:;">
          <cite>消息草稿箱</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>标题</th>
            <th>内容</th>
            <th>发布人</th>
            <th>结束日期</th>
            <th>首次延期日期</th>
            <th>二次延期日期</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php if (!empty($dataProvider)): ?>
            <?php foreach ($dataProvider as $value): ?>
                <tr>
                    <td><?= $value['id'] ?></td>
                    <td><?= $value['title'] ?></td>
                    <td style="max-width: 110px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;"><?= $value['content_data'] ?></td>
                    <td><?= (new UserInfo())->getUserName($value['user_id']) ?></td>
                    <td><?= $value['alarm_time'] ?></td>
                    <td><?= $value['first_delay_time'] ?></td>
                    <td><?= $value['second_delay_time'] ?></td>
                    <td class="td-manage">
                        <button type="button" class="layui-btn layui-btn-xs" onclick="x_admin_show('编辑','<?= Url::to([$this->context->id . '/update-draft', 'id' => $value['id']]) ?>')">编辑</button>
                        <button type="button" class="layui-btn layui-btn-xs layui-btn-danger delte-draft" data-id="<?=  $value['id'] ?>">删除</button>
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
    $('.delte-draft').click(function () {
        var id = $(this).data('id');
        layer.confirm('是否确认删除？', {
            btn: ['确认', '取消'] //可以无限个按钮
            ,
        }, function(index, layero){
            $.ajax({
                type:'get',
                url:'delete-draft?id='+id,
                dataType:'json',
                success:function (res) {
                    if(res.status == 200){
                        layer.msg('删除成功');
                        window.location.reload();
                    }else{
                        layer.msg(res.msg);
                    }
                }
            })
        });
    });
</script>
<?php $this->endBlock() ?>
