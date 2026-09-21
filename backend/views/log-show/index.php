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
          <cite>日志列表</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <div class="layui-row">

    </div>
    <xblock>

        <a href="<?= Url::to(['roll-index'])?>"  target="_Blank" class="layui-btn"><i class="layui-icon">&#xe62f;</i>展示</a>
    </xblock>
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>标题</th>
            <th>发布人</th>
            <th>交接人</th>
            <th>创建时间</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php if (!empty($logList)): ?>
            <?php foreach ($logList as $value): ?>
                <tr >
                    <td><?= $value['bid'] ?></td>
                    <td width="80%" ><?= $value['title'] ?></td>
                    <td><?= ArrayHelper::getValue(Blog::$statusArr, $value['status'],'') ?></td>
                    <td><?= ArrayHelper::getValue(Blog::$readAllArr, $value['read_all'],'') ?></td>
                    <td><?=  strtotime($value['create_time'])  ?></td>
                    <td class="td-manage">

                        <button type="button" data-id="<?= $value['bid'] ?>" class="layui-btn layui-btn-danger disable-log" >禁用</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php $this->beginBlock('footer') ?>
<script>
    $('.disable-log').click(function () {
        var id = $(this).data('id');
        layer.confirm('是否确认不再此页面显示该日志', {
            btn: ['确认','取消'] //按钮
        }, function(){
            layer.load(3);
            $.post('<?=Url::to('disable')?>',{id:id},function (res) {
                    if(res.status == 200){

                        window.location.reload();
                    }else{
                        console.log(res);
                        layer.msg(res.msg);
                    }
            },'json');
        });
    });
</script>
<?php $this->endBlock() ?>
