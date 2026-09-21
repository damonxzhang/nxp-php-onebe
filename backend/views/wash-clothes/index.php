<?php
use backend\models\UserInfo;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use backend\models\WashClothesUser;

?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">静电服清洗</a>
        <a href="javascript:;">
          <cite>静电服清洗列表</cite>
        </a>
      </span>
</div>

<div class="x-body">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so">
            <input class="layui-input" value="<?php echo ArrayHelper::getValue($request, 'user_name') ?>" autocomplete="off" placeholder="姓名" name="user_name">
            <input class="layui-input" value="<?php echo ArrayHelper::getValue($request, 'pid') ?>" autocomplete="off" placeholder="PID" name="pid">
            <button class="layui-btn" type="submit"><i class="layui-icon">&#xe615;</i></button>
            <button class="layui-btn" type="button" onclick="location.href= '<?= Url::to([$this->context->id . '/index'])?>'" >
                <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>刷新
            </button>
        </form>
    </div>
    <xblock>
        <button class="layui-btn layui-bg-black" onclick="export_excel()"><i class="layui-icon">&#xe62f;</i>导出</button>
        <?php if ($userInfo->id == 1): ?>
            <button class = "layui-btn layui-bg-orange" onclick = "x_admin_show('导入数据','<?= Url::to(['wash-clothes/common-import']) ?>',600,400)"><i class = "layui-icon">&#xe656;</i>导入数据</button>
        <?php endif; ?>
        <span class="x-right" style="line-height:30px">共有数据：<span class="count_num"><?= $pagination->totalCount ?></span> 条 ( <?= $pagination->getPageCount() ?> 页 )</span>
    </xblock>
    <table class="layui-table">
        <thead>
            <th>ID</th>
            <th>姓名</th>
            <th>种类</th>
            <th>RFID</th>
            <th>PID</th>
            <th>班别</th>
            <th>区域</th>
            <th>清洗时间</th>
            <th>领取时间</th>
            <th>刷卡时间</th>
        </thead>
        <tbody>
        <?php foreach ($dataProvider as $list): ?>
            <tr>
                <td><?= $list['id'] ?></td>
                <td><?= $list['user_name'] ?></td>
                <td><?= ArrayHelper::getValue(WashClothesUser::$typeArr, $list['type'],'') ?></td>
                <td><?= $list['rfid'] ?></td>
                <td><?= $list['pid'] ?></td>
                <td><?= ArrayHelper::getValue(UserInfo::$shiftArr, $list['shift'],'') ?></td>
                <td><?= ArrayHelper::getValue(UserInfo::$areaArr, $list['area'],'') ?></td>
                <td><?= $list['invest_time'] ?></td>
                <td><?= $list['receive_time'] ?></td>
                <td><?= date('Y-m-d H:i:s',strtotime($list['create_time'])) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?= $this->render('@app/views/layouts/pagination', compact('pagination')) ?>
</div>

<?php $this->beginBlock('footer') ?>
<script>
    function export_excel(){
        layui.use(['jquery', 'layer'], function() {
            var $ = layui.jquery;
            $.ajax({
                type: 'GET',
                dataType: 'json',
                url: '<?= Url::to([$this->context->id . '/export'])?>',
                success: function(res) {
                    LAY_EXCEL.exportExcel(res.data, res.msg, 'xlsx');
                }
            });
        });
    }
</script>
<?php $this->endBlock() ?>