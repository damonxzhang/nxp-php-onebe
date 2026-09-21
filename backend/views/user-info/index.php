<?php

use backend\models\Role;
use backend\models\User;
use backend\models\UserInfo;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">人员管理</a>
        <a href="javascript:;">
          <cite>人员列表</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so">
            <input class="layui-input" name="badge_code" id="autofocus" value="<?php echo ArrayHelper::getValue($request, 'badge_code') ?>" autocomplete="off" placeholder="工卡卡号">
            <input class="layui-input" name="chinese_name" value="<?php echo ArrayHelper::getValue($request, 'chinese_name') ?>" autocomplete="off" placeholder="姓名">
            <input class="layui-input" name="tel" value="<?php echo ArrayHelper::getValue($request, 'tel') ?>" autocomplete="off" placeholder="电话">
            <input class="layui-input" name="leader" value="<?php echo ArrayHelper::getValue($request, 'leader') ?>" autocomplete="off" placeholder="直属领导">
            <div class="layui-input-inline">
                <?= Html::dropDownList('position', ArrayHelper::getValue($request, 'position'), ArrayHelper::merge(['' => '职务'], UserInfo::$positionArr), ['name' => 'position']); ?>
            </div>
            <div class="layui-input-inline">
                <?= Html::dropDownList('role', ArrayHelper::getValue($request, 'role'), ArrayHelper::merge(['' => '角色'], Role::getRole()), ['name' => 'role']); ?>
            </div>
            <div class="layui-input-inline">
                <?= Html::dropDownList('section', ArrayHelper::getValue($request, 'section'), ArrayHelper::merge(['' => '区域'], UserInfo::$sectionArr), ['name' => 'station_id']); ?>
            </div>
            <div class="layui-input-inline">
                <?= Html::dropDownList('station_id', ArrayHelper::getValue($request, 'station_id'), ArrayHelper::merge(['' => '站别'], $stations), ['name' => 'station_id']); ?>
            </div>
            <div class="layui-input-inline">
                <?= Html::dropDownList('shift', ArrayHelper::getValue($request, 'shift'), ArrayHelper::merge(['' => '班别'], UserInfo::$shiftArr), ['name' => 'shift']); ?>
            </div>
            <button class="layui-btn"  lay-submit="" lay-filter="sreach"><i class="layui-icon">&#xe615;</i>查询</button>
            <button class="layui-btn" type="button" onclick="location.href= '<?= Url::to([$this->context->id . '/index'])?>'" >
                <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>刷新
            </button>
        </form>
    </div>
    <xblock>
        <button class="layui-btn" onclick="x_admin_show('添加用户','<?= Url::to([$this->context->id . '/create']) ?>')"><i class="layui-icon"></i>添加
        </button>
        <span class="x-right" style="line-height:30px">共有数据：<span class="count_num"><?= $pagination->totalCount ?></span> 条 ( <?= $pagination->getPageCount() ?> 页 )</span>
    </xblock>
    <div>
        <table class="layui-table">
            <thead>
            <tr>
                <th>ID</th>
                <th>直属领导</th>
                <th>手机号</th>
                <th>中文名</th>
                <th>职务</th>
                <th>班别</th>
                <th>站别</th>
                <th>区域</th>
                <th>角色</th>
                <th>工卡卡号</th>
                <th>创建时间</th>
                <th>操作</th>
            </thead>
            <tbody>
            <?php foreach ($dataProvider as $list): ?>
                <tr>
                    <td><?= $list['id'] ?></td>
                    <td><?= (new UserInfo())->getUserName($list['leader_uid']) ?></td>
                    <td><?= $list['tel'] ?></td>
                    <td><?= $list['chinese_name'] ?></td>
                    <td><?= ArrayHelper::getValue(UserInfo::$positionArr, $list['position'],'') ?></td>
                    <td><?= ArrayHelper::getValue(UserInfo::$shiftArr, $list['shift'],'') ?></td>
                    <td><?= ArrayHelper::getValue($list->userstation, 'station','') ?></td>
                    <td><?= ArrayHelper::getValue(UserInfo::$areaArr, $list['area'],'') ?></td>
                    <td><?= (new Role())->getRoleName($list['role_id']) ?></td>
                    <td><?= $list['badge_code'] ?></td>
                    <td><?= date('Y-m-d H:i:s',strtotime($list['create_time'])) ?></td>
                    <td class="td-manage">
                        <button type="button" class="layui-btn layui-btn-xs" onclick="x_admin_show('详情','<?= Url::to([$this->context->id . '/detail', 'id' => $list['id']]) ?>')"><i class="layui-icon">&#xe63c;</i>详情</button>
                        <?php if ($role_id == 1 || $role_id == 10 || $role_id == 9 || $role_id == 4): ?>
								<button class="layui-btn layui-btn-xs layui-btn-normal" onclick="x_admin_show('编辑','<?= Url::to([$this->context->id . '/update', 'id' => $list['id']]) ?>')"><i class="layui-icon">&#xe642;</i>编辑</button>
                        <?php endif; ?>
						<?php if (User::findOne(['user_id' =>$list['id']])): ?>
                            <button class="layui-btn layui-btn-xs layui-bg-cyan" onclick="x_admin_show('修改密码','<?= Url::to([$this->context->id . '/update-password', 'id' => $list['id']])?>',500,400)"><i class="layui-icon">&#xe642;</i>修改密码</button>
                        <?php else: ?>
                            <button class="layui-btn layui-btn-xs" onclick="x_admin_show('添加密码','<?=  Url::to([$this->context->id . '/create-password', 'id' => $list['id']]) ?>',500,400)"><i class="layui-icon"></i>添加密码</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if (empty($dataProvider)): ?>
        <p class="text-empty">-- 暂无数据 --</p>
    <?php endif; ?>
    <?= $this->render('@app/views/layouts/pagination', compact('pagination')) ?>
</div>
<?php $this->beginBlock('footer') ?>
<?php $this->endBlock() ?>

