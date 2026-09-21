<?php
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use backend\models\BillShelf;
use yii\helpers\Html;
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">流程单管理</a>
        <a href="javascript:;">
          <cite>流程单管控</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so">
            <input class="layui-input" id="autofocus" value="<?php echo ArrayHelper::getValue($request, 'materials_number') ?>" autocomplete="off" placeholder="物料批号" name="materials_number">
            <button class="layui-btn"  lay-submit="" lay-filter="sreach"><i class="layui-icon">&#xe615;</i>查询</button>
            <button class="layui-btn" type="button" onclick="location.href= '<?= Url::to([$this->context->id . '/index'])?>'" >
                <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>刷新
            </button>
        </form>
    </div>
    <xblock>
        <button class="layui-btn" onclick="x_admin_show('放入流程单','<?= Url::to(['bill-materials/create']) ?>', 460,350)"><i class="layui-icon"></i>放入流程单</button>
        <span class="x-right" style="line-height:30px">共有数据：<span class="count_num"><?= $pagination->totalCount ?></span> 条 ( <?= $pagination->getPageCount() ?> 页 )</span>
    </xblock>
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>存储单元编号</th>
            <th>物料批号</th>
            <th>创建时间</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php foreach ($dataProvider as $list): ?>
            <tr>
                <td><?= $list['id'] ?></td>
                <td><?= $list['shelf_number'] ?></td>
                <td><?= $list['materials_number'] ?></td>
                <td><?= $list['create_time'] ?></td>
                <td>
                <button type="button" class="layui-btn layui-btn-xs layui-btn-danger down-draft" data-id="<?=  $list['id'] ?>">取走流程单</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?= $this->render('@app/views/layouts/pagination', compact('pagination')) ?>
</div>


<?php $this->beginBlock('footer') ?>
<script>
    $('.down-draft').click(function () {
        var id = $(this).data('id');
        layer.confirm('是否确认取走流程单？', {
            btn: ['确认', '取消'] //可以无限个按钮
            ,
        }, function(index, layero){
            $.ajax({
                type:'get',
                url:'down?id='+id,
                dataType:'json',
                success:function (res) {
                    if(res.status == 200){
                        layer.msg('取走成功');
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
