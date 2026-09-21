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
        <form class="layui-form layui-col-md12 x-so">
            <input class="layui-input" id="autofocus" value="<?php echo ArrayHelper::getValue($request, 'title') ?>" autocomplete="off" placeholder="标题" name="title">
            <input class="layui-input" id="autofocus" value="<?php echo ArrayHelper::getValue($request, 'matter_number') ?>" autocomplete="off" placeholder="物料编号" name="matter_number">
            <input class="layui-input" id="autofocus" value="<?php echo ArrayHelper::getValue($request, 'device_number') ?>" autocomplete="off" placeholder="设备编号" name="device_number">
            <div class="layui-input-inline">
                <?= Html::dropDownList('status', ArrayHelper::getValue($request, 'status'), ArrayHelper::merge(['' => '状态选择'], Blog::$searchStatusArr), ['name' => 'status']); ?>
            </div>
            <?php if ($userInfo->id == 1): ?>
            <div class="layui-input-inline">
                <?= Html::dropDownList('area', ArrayHelper::getValue($request, 'area'), ArrayHelper::merge(['' => '区域选择'], UserInfo::$areaArr), ['name' => 'area']); ?>
            </div>
            <?php endif; ?>
            <input class="layui-input" placeholder="开始日" name="start_time" id="start_time" value="<?= !empty($request['start_time']) ? date('Y-m-d H:i:s',strtotime($request['start_Ptime'])) : '' ?>" autocomplete="off">
            <input class="layui-input" placeholder="截止日" name="end_time" id="end_time" value="<?= !empty($request['end_time']) ? date('Y-m-d H:i:s',strtotime($request['end_time'])) : '' ?>" autocomplete="off">
            <button class="layui-btn"  lay-submit="" lay-filter="sreach"><i class="layui-icon">&#xe615;</i>查询</button>
            <button class="layui-btn" type="button" onclick="location.href= '<?= Url::to([$this->context->id . '/index'])?>'" >
                <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>刷新
            </button>
        </form>
    </div>
    <xblock>
        <button class="layui-btn" onclick="x_admin_show('添加日志','<?=  Url::to([$this->context->id . '/create']) ?>')"><i class="layui-icon"></i>新增</button>
        <button class="layui-btn layui-btn-normal" onclick="location.href= '<?= Url::to([$this->context->id . '/index', 'associate' => Blog::SEND_MYSELF])?>'" >我发起的</button>
        <button class="layui-btn layui-btn-warm" onclick="location.href= '<?= Url::to([$this->context->id . '/index', 'associate' => Blog::ASSOCIATE])?>'" >与我相关</button>
        <button class="layui-btn layui-btn" onclick="add_show()">展示日志</button>
        <?php if($userInfo->role_id === 4 || $userInfo->role_id === 1){

            echo '<button class="layui-btn layui-btn" onclick="handover_to_day()">今日交接</button>';

            }?>
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
            <th>必读</th>
            <th>创建时间</th>
            <th>更新时间</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php if (!empty($dataProvider)): ?>
            <?php foreach ($dataProvider as $value): ?>
                <tr <?php if ($value['status'] == Blog::STATUS_DEFERRED): ?> style="font-weight:bold;color: red;" <?php endif; ?>>
                    <td>
                        <div class="layui-unselect layui-form-checkbox" lay-skin="primary" data-id=<?= $value['id'] ?>><i class="layui-icon">&#xe605;</i></div>
                    </td>
                    <td><?= $value['id'] ?></td>
                    <td><?= $value['title'] ?></td>
                    <td style="max-width: 110px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;"><?= $value['content_data'] ?></td>
                    <td><?= (new UserInfo())->getUserName($value['user_id']) ?></td>
                    <td><?= ArrayHelper::getValue(Blog::$statusArr, $value['status'],'') ?></td>
                    <td><?= ArrayHelper::getValue(Blog::$readAllArr, $value['read_all'],'') ?></td>
                    <td><?= date('Y-m-d H:i:s',strtotime($value['create_time'])) ?></td>
                    <td><?= date('Y-m-d H:i:s',strtotime($value['update_time'])) ?></td>
                    <td class="td-manage">
                        <button type="button" class="layui-btn layui-btn-xs" onclick="x_admin_show('日志详情','<?= Url::to([$this->context->id . '/detail', 'id' => $value['id']]) ?>')">日志详情</button>
                        <button type="button" class="layui-btn layui-btn-xs" onclick="x_admin_show('反馈列表','<?= Url::to([$this->context->id . '/feedback', 'id' => $value['id']]) ?>')">反馈列表</button>
                        <button type="button" class="layui-btn layui-btn-xs" onclick="x_admin_show('编辑','<?= Url::to([$this->context->id . '/update', 'id' => $value['id']]) ?>')">编辑</button>
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
    function handover_to_day(){
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
                url: '<?= Url::to(['/handover/add-handover'])?>',
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
