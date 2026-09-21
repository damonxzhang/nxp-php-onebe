<?php
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use backend\models\UserInfo;
use backend\models\Role;
use yii\helpers\Html;

?>

<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">DPR</a>
        <a href="javascript:;">
          <cite>人员列表</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so">
        <input class="layui-input" name="chinese_name" value="<?php echo ArrayHelper::getValue($request, 'chinese_name') ?>" autocomplete="off" placeholder="姓名">
            <button class="layui-btn"  lay-submit="" lay-filter="sreach"><i class="layui-icon">&#xe615;</i>查询</button>
            <button class="layui-btn" type="button" onclick="location.href= '<?= Url::to([$this->context->id . '/user-list'])?>'" >
                <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>刷新
            </button>
        </form>
    </div>
    <xblock>
        <button class="layui-btn" onclick="x_admin_show('添加人员','<?= Url::to(['car/user-create']) ?>', 520,350)"><i class="layui-icon"></i>添加</button>
        <span class="x-right" style="line-height:30px">共有数据：<span class="count_num"><?= $pagination->totalCount ?></span> 条 ( <?= $pagination->getPageCount() ?> 页 )</span>
    </xblock>
    <table class="layui-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>中文名</th>
                <th>职务</th>
                <th>班别</th>
              
                <th>区域</th>
                <th>角色</th>
                <th>操作</th>
            </thead>
            <tbody>
            <?php foreach ($dataProvider as $list): ?>
                <tr>
                    <td><?= $list['id'] ?></td>
                    <td><?= $list['chinese_name'] ?></td>
                    <td><?= ArrayHelper::getValue(UserInfo::$positionArr, $list['position'],'') ?></td>
                    <td><?= ArrayHelper::getValue(UserInfo::$shiftArr, $list['shift'],'') ?></td>
                    <td><?= ArrayHelper::getValue(UserInfo::$areaArr, $list['area'],'') ?></td>
                    <td><?= (new Role())->getRoleName($list['role_id']) ?></td>              
                    <td class="td-manage">
                        <button class="layui-btn layui-btn-xs"   onclick="del(this,'<?= $list['id'] ?>', '<?= Url::to(['car/userdel']) ?>')"><i class="layui-icon">&#xe640;</i>删除</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?= $this->render('@app/views/layouts/pagination', compact('pagination')) ?>
</div>

<?php $this->beginBlock('footer') ?>
<?php $this->endBlock() ?>
