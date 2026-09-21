<?php

use backend\models\UserInfo;
use yii\helpers\Url;
use backend\models\Blog;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">日志管理</a>
        <a href="javascript:;">
          <cite>日志草稿箱</cite>
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
            <th>状态</th>
            <th>必读</th>
            <th>创建时间</th>
            <th>更新时间</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php if (!empty($dataProvider)): ?>
            <?php foreach ($dataProvider as $value): ?>
                <tr <?php if ($value['status'] == Blog::STATUS_DEFERRED): ?> style="font-weight:bold;color: red;" <?php endif; ?>>

                    <td><?= $value['id'] ?></td>
                    <td><?= $value['title'] ?></td>
                    <td style="max-width: 110px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;"><?= $value['content_data'] ?></td>
                    <td><?= ArrayHelper::getValue(Blog::$statusArr, $value['status'],'') ?></td>
                    <td><?= ArrayHelper::getValue(Blog::$readAllArr, $value['read_all'],'') ?></td>
                    <td><?= date('Y-m-d H:i:s',strtotime($value['create_time'])) ?></td>
                    <td><?= date('Y-m-d H:i:s',strtotime($value['update_time'])) ?></td>
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
                type: 'GET',
                data: {idlist: idlist},
                dataType: 'json',
                url: '<?= Url::to([$this->context->id . '/export'])?>',
                success: function(res) {
                    LAY_EXCEL.exportExcel(res.data, res.msg, 'xlsx');
                }
            });
        });
    }
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
    function add_show(){
        var idlist   = [];
        $('.layui-form-checked').each(function () {
            if (typeof($(this).data('id')) !="undefined" && $(this).data('id')!=0){
                idlist.push($(this).data('id'));
            }
        });
        layui.use(['jquery', 'layer'], function() {
            var $ = layui.jquery;
            $.ajax({
                type: 'GET',
                data: {idlist: idlist},
                dataType: 'json',
                url: '<?= Url::to([$this->context->id . '/add-show'])?>',
                success: function(res) {
                    if(res.status == 200){
                        layer.msg(res.msg)
                        
                    }else{
                        layer.msg(res.msg)
                    }
                }
            });
        });
    }
</script>
<?php $this->endBlock() ?>
