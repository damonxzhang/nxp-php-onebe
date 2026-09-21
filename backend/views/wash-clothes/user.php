<?php
use backend\models\WashClothesUser;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">静电服清洗</a>
        <a href="javascript:;">
          <cite>静电服人员列表</cite>
        </a>
      </span>
</div>

<div class="x-body">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so">
            <input class="layui-input" value="<?php echo ArrayHelper::getValue($request, 'user_name') ?>" autocomplete="off" placeholder="姓名" name="user_name">
            <input class="layui-input" value="<?php echo ArrayHelper::getValue($request, 'pid') ?>" autocomplete="off" placeholder="PID" name="pid">
            <button class="layui-btn" type="submit"><i class="layui-icon">&#xe615;</i></button>
            <button class="layui-btn" type="button" onclick="location.href= '<?= Url::to([$this->context->id . '/user'])?>'" >
                <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>刷新
            </button>
        </form>
    </div>
    <xblock>
        <button id="downloadBtn" class = "layui-btn">下载模板</button>
        <button class = "layui-btn layui-bg-black" onclick = "x_admin_show('导入数据','<?= Url::to(['wash-clothes/import']) ?>',600,400)"><i class = "layui-icon">&#xe656;</i>导入数据</button>
        <span class="x-right" style="line-height:30px">共有数据：<span class="count_num"><?= $pagination->totalCount ?></span> 条 ( <?= $pagination->getPageCount() ?> 页 )</span>
    </xblock>
    <table class="layui-table">
        <thead>
            <th>ID</th>
            <th>姓名</th>
            <th>种类</th>
            <th>RFID</th>
            <th>状态</th>
            <th>创建时间</th>
            <th>过期时间</th>
            <th>PID</th>
            <th>清洗年份</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php foreach ($dataProvider as $list): ?>
            <tr>
                <td><?= $list['id'] ?></td>
                <td><?= $list['user_name'] ?></td>
                <td><?= ArrayHelper::getValue(WashClothesUser::$typeArr, $list['type'],'') ?></td>
                <td><?= $list['str'] ?></td>
                <td><?= ArrayHelper::getValue(WashClothesUser::$statusArr, $list['status'],'') ?></td>
                <td><?= date('Y-m-d H:i:s',strtotime($list['create_time'])) ?></td>
                <td><?= date('Y-m-d H:i:s',strtotime($list['end_time'])) ?></td>
                <td><?= $list['pid'] ?></td>
                <td><?= $list['year'] ?></td>
                <td class="td-manage">
                    <button class="layui-btn layui-btn-xs layui-btn-normal" onclick="x_admin_show('编辑','<?= Url::to([$this->context->id . '/update', 'id' => $list['id']]) ?>')"><i class="layui-icon">&#xe642;</i>编辑</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?= $this->render('@app/views/layouts/pagination', compact('pagination')) ?>
</div>

<?php $this->beginBlock('footer') ?>
<script>
$(document).ready(function() {
    $('#downloadBtn').click(function() {
        var fileUrl = '/file/WashClothed.xlsx'; // 替换为你要下载的文件的URL地址
        var fileName = 'WashClothed.xlsx'; // 替换为你要下载的文件名称
        var link = document.createElement('a');
        link.href = fileUrl;
        link.download = fileName;
        document.body.appendChild(link);
        link.click(); // 模拟点击下载
        document.body.removeChild(link); // 下载完成后移除a标签
    });
});
</script>
<?php $this->endBlock() ?>